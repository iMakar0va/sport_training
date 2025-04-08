<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

// Подключение к базе данных
require 'db/connect.php';

// Получение сегодняшней даты
$today = date('Y-m-d');

// Получение первой даты этой недели
$start_of_week = date('Y-m-d', strtotime('monday this week'));

// Получение первой даты этого месяца
$start_of_month = date('Y-m-01');

// Функция для получения статистики (количество тренировок и калории)
function getWorkoutStats($user_id, $start_date, $end_date)
{
    global $conn;
    $query = "SELECT COUNT(*) AS count, SUM(calories) AS total_calories, SUM(duration) AS total_duration
              FROM workouts
              WHERE user_id = $1 AND date >= $2 AND date <= $3";
    $result = pg_query_params($conn, $query, [$user_id, $start_date, $end_date]);
    $row = pg_fetch_assoc($result);
    return [
        'count' => $row['count'] ?? 0, // Количество тренировок
        'total_calories' => $row['total_calories'] ?? 0, // Сумма калорий
        'total_duration' => $row['total_duration'] ?? 0, // Сумма времени
    ];
}

// Статистика для сегодня
$today_stats = getWorkoutStats($user_id, $today, $today);

// Статистика для недели
$week_stats = getWorkoutStats($user_id, $start_of_week, $today);

// Статистика для месяца
$month_stats = getWorkoutStats($user_id, $start_of_month, $today);

// Получение целей пользователя из таблицы goals
$query = "SELECT * FROM goals WHERE user_id = $1";
$result_goals = pg_query_params($conn, $query, [$user_id]);

$goals = [];
while ($row = pg_fetch_assoc($result_goals)) {
    $goals[] = $row;
}

// Функция для получения прогресса по целям с учётом типа тренировки
function getGoalProgress($goal)
{
    global $conn;

    // Получаем тип тренировки из цели
    $goal_type = $goal['type'];

    // Запрос для получения прогресса по калориям и длительности тренировок данного типа
    $query = "SELECT SUM(calories) AS total_calories, SUM(duration) AS total_duration
              FROM workouts
              WHERE user_id = $1
                AND date >= $2
                AND date <= $3
                AND type = $4
                AND date <= CURRENT_DATE";  // Фильтруем по типу тренировки

    $result = pg_query_params($conn, $query, [
        $goal['user_id'],
        $goal['created_at'],
        $goal['target_date'],
        $goal_type  // Передаем тип тренировки из цели
    ]);

    $row = pg_fetch_assoc($result);
    return [
        'total_calories' => $row['total_calories'] ?? 0,
        'total_duration' => $row['total_duration'] ?? 0
    ];
}

// Вычисление прогресса по целям
$goal_progress = [];
foreach ($goals as $goal) {
    // Пропускаем завершённые цели
    $progress = getGoalProgress($goal);
    $calories_percent = $goal['target_calories'] > 0 ? ($progress['total_calories'] / $goal['target_calories']) * 100 : 0;
    $duration_percent = $goal['target_duration'] > 0 ? ($progress['total_duration'] / $goal['target_duration']) * 100 : 0;
    $total_percent = ($calories_percent + $duration_percent) / 2;

    // Добавляем цель и её прогресс только если цель не завершена
    if ($total_percent < 100) {
        $goal_progress[] = [
            'goal' => $goal,
            'progress' => $progress,
            'percent' => $total_percent
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель пользователя</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Добро пожаловать в Твой Тренер Онлайн!</h1>
            <p>Твоя панель управления тренировки</p>
        </header>

        <section class="dashboard">
            <div class="welcome-card">
                <h2>Привет, <?php echo $name; ?>!</h2>
                <p>Следи за своими тренировками и достигай целей быстрее.</p>
                <a href="logout.php">
                    <button class="btn">Выйти</button>
                </a>
            </div>

            <div class="dashboard-actions">
                <button class="btn" onclick="window.location.href='all_workouts.php';">Посмотреть тренировки</button>
                <button class="btn" onclick="window.location.href='all_goals.php';">Посмотреть выполненные цели</button>
                <button class="btn" onclick="window.location.href='add_workout.php';">Добавить тренировку</button>
                <button class="btn" onclick="window.location.href='add_goal.php';">Добавить цель</button>
            </div>

            <section class="stats">
                <h2>Сводная статистика</h2>
                <div class="stat-cards">
                    <div class="stat-card">
                        <h3>Сегодня</h3>
                        <p>Тренировок: <?php echo $today_stats['count']; ?></p>
                        <p>Калории: <?php echo $today_stats['total_calories']; ?> ккал</p>
                        <p>Время тренировок: <?php echo $today_stats['total_duration']; ?> мин</p>
                    </div>
                    <div class="stat-card">
                        <h3>Неделя</h3>
                        <p>Тренировок: <?php echo $week_stats['count']; ?></p>
                        <p>Калории: <?php echo $week_stats['total_calories']; ?> ккал</p>
                        <p>Время тренировок: <?php echo $week_stats['total_duration']; ?> мин</p>
                    </div>
                    <div class="stat-card">
                        <h3>Месяц</h3>
                        <p>Тренировок: <?php echo $month_stats['count']; ?></p>
                        <p>Калории: <?php echo $month_stats['total_calories']; ?> ккал</p>
                        <p>Время тренировок: <?php echo $month_stats['total_duration']; ?> мин</p>
                    </div>
                </div>
            </section>

            <h2>Прогресс по целям</h2>
            <section class="cards">
                <?php
                $has_incomplete_goals = false;

                foreach ($goal_progress as $goal_item):
                    $target_date = $goal_item['goal']['target_date'];
                    $target_calories = $goal_item['goal']['target_calories'];
                    $target_duration = $goal_item['goal']['target_duration'];
                    $total_calories = $goal_item['progress']['total_calories'];
                    $total_duration = $goal_item['progress']['total_duration'];

                    // Проценты выполнения
                    $calories_percent = $target_calories > 0 ? min(1, $total_calories / $target_calories) : 0;
                    $duration_percent = $target_duration > 0 ? min(1, $total_duration / $target_duration) : 0;
                    $total_percent = (($calories_percent + $duration_percent) / 2) * 100;
                    $percent_rounded = round($total_percent);
                    // Если цель не завершена, устанавливаем флаг
                    if ($total_percent < 100) {
                        $has_incomplete_goals = true;
                    }
                    // Цвет прогресс-бара
                    if ($percent_rounded <= 25) {
                        $color_class = 'progress-red';
                    } elseif ($percent_rounded <= 50) {
                        $color_class = 'progress-orange';
                    } elseif ($percent_rounded <= 75) {
                        $color_class = 'progress-yellow';
                    } else {
                        $color_class = 'progress-green';
                    }

                    // Подсчёт оставшихся или просроченных дней
                    $target_timestamp = strtotime($target_date);
                    $current_timestamp = strtotime($today);
                    $diff_days = floor(($target_timestamp - $current_timestamp) / (60 * 60 * 24));
                ?>
                    <div class="goal-card">
                        <div class="progress-bar-container">
                            <div class="progress-bar <?php echo $color_class; ?>" style="width: <?php echo min(100, $percent_rounded); ?>%;"></div>
                        </div>

                        <div class="goal-info">
                            <a href="delete_goal.php?goal_id=<?php echo $goal_item['goal']['id']; ?>" onclick="return confirm('Удалить цель?');">
                                <img src="assets/img/4.svg" alt="Удалить">
                            </a>
                            <h4>Срок выполнения: <?php echo date('d.m.Y', strtotime($target_date)); ?></h4>
                            <p>
                                <?php
                                if ($diff_days < 0) {
                                    echo "Просрочено на " . abs($diff_days) . " дн.";
                                } else {
                                    echo "Осталось: " . $diff_days . " дн.";
                                }
                                ?>
                            </p>
                            <h3>Тип тренировки: <?php echo htmlspecialchars($goal_item['goal']['type']); ?></h3>
                        </div>

                        <div class="goal-info">
                            <p>Цель: потратить <?php echo $target_calories; ?> ккал</p>
                            <p>Осталось: <?php echo max(0, $target_calories - $total_calories); ?> ккал</p>
                        </div>

                        <div class="goal-info">
                            <p>Цель: тренироваться <?php echo $target_duration; ?> мин</p>
                            <p>Осталось: <?php echo max(0, $target_duration - $total_duration); ?> мин</p>
                        </div>

                        <a href="edit_goal.php?goal_id=<?php echo $goal_item['goal']['id']; ?>">
                            <button class="btn-action">Изменить цель</button>
                        </a>
                    </div>
                <?php endforeach; ?>

                <?php if (!$has_incomplete_goals): ?>
                    <p>Целей пока нет.</p>
                <?php endif; ?>
            </section>

        </section>
    </div>
</body>

</html>