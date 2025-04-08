<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Подключеник к базе данных
require 'db/connect.php';

// Получаем цели пользователя
$query = "SELECT * FROM goals WHERE user_id = $1";
$result = pg_query_params($conn, $query, [$user_id]);

$goals = [];
while ($row = pg_fetch_assoc($result)) {
    $goals[] = $row;
}

// Прогресс по каждой цели
$goal_progress = [];

foreach ($goals as $goal) {
    $goal_id = $goal['id'];

    // Сумма калорий и длительности по тренировкам в рамках срока цели
    $query_progress = "
        SELECT
            COALESCE(SUM(calories), 0) AS total_calories,
            COALESCE(SUM(duration), 0) AS total_duration
        FROM workouts
        WHERE user_id = $1 AND date >= $2 AND date <= $3 AND type = $4 AND date <= CURRENT_DATE
    ";
    $progress_result = pg_query_params($conn, $query_progress, [
        $user_id,
        $goal['created_at'],
        $goal['target_date'],
        $goal['type']
    ]);
    $progress = pg_fetch_assoc($progress_result);

    // Проверяем выполнение по калориям и времени
    $calories_ok = $goal['target_calories'] > 0
        ? $progress['total_calories'] >= $goal['target_calories']
        : true;
    $duration_ok = $goal['target_duration'] > 0
        ? $progress['total_duration'] >= $goal['target_duration']
        : true;

    // Только если обе метрики выполнены, добавляем в список
    if ($calories_ok && $duration_ok) {
        $goal_progress[] = [
            'goal' => $goal
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Завершённые цели</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Завершённые цели</h1>
            <p>Список всех завершённых целей пользователя.</p>
            <a href="dashboard.php"><button class="btn">Назад к панели</button></a>
        </header>

        <section class="cards">
            <?php if (count($goal_progress) === 0): ?>
                <p>Нет завершённых целей.</p>
            <?php else: ?>
                <?php foreach ($goal_progress as $goal_item): ?>
                    <div class="goal-card">
                        <div class="goal-info">
                            <h4>Срок выполнения: <?php echo date('d.m.Y', strtotime($goal_item['goal']['target_date'])); ?></h4>
                            <h3>Тип тренировки: <?php echo htmlspecialchars($goal_item['goal']['type']); ?></h3>
                        </div>

                        <div class="goal-info">
                            <p>Потрачено: <?php echo $goal_item['goal']['target_calories']; ?> ккал</p>
                        </div>

                        <div class="goal-info">
                            <p>Длительности: <?php echo $goal_item['goal']['target_duration']; ?> мин</p>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>