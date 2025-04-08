<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Перенаправляем на страницу входа
    exit;
}

$user_id = $_SESSION['user_id']; // ID пользователя

// Подключение к базе данных
require 'db/connect.php';

// Получение всех тренировок пользователя
$query = "SELECT * FROM workouts WHERE user_id = $1 ORDER BY date DESC";
$result = pg_query_params($conn, $query, [$user_id]);

$workouts = [];
while ($row = pg_fetch_assoc($result)) {
    $workouts[] = $row;
}

// Группировка тренировок по дням
$grouped_workouts = [];
foreach ($workouts as $workout) {
    $date = date('Y-m-d', strtotime($workout['date']));
    $grouped_workouts[$date][] = $workout;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все тренировки</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Все твои тренировки</h1>
            <p>Просматривай свои тренировки, изменяй и отслеживай прогресс.</p>
            <a href="dashboard.php"><button type="submit" class="btn">Назад к панели</button></a>
        </header>

        <h2>Все тренировки</h2>
        <section>
            <?php foreach ($grouped_workouts as $date => $day_workouts): ?>
                <h3><?php echo date('d.m.Y (l)', strtotime($date)); ?>:</h3>
                <div class="cards">
                    <?php foreach ($day_workouts as $workout): ?>
                        <div class="goal-card"> <!-- Используем стили goal-card -->
                            <div class="goal-info">
                                <?php
                                $workout_date = date('Y-m-d', strtotime($workout['date']));
                                $today = date('Y-m-d');
                                if ($workout_date >= $today):
                                ?>
                                    <a href="delete_workout.php?workout_id=<?php echo $workout['id']; ?>" onclick="return confirm('Удалить тренировку?');">
                                        <img src="assets/img/4.svg" alt="Удалить">
                                    </a>
                                <?php endif; ?>
                                <h3>Тип тренировки: <?php echo htmlspecialchars($workout['type']); ?></h3>
                            </div>
                            <div class="goal-info">
                                <p>Калории: <?php echo htmlspecialchars($workout['calories']); ?> ккал</p>
                                <?php if (isset($workout['duration'])): ?>
                                    <p>Продолжительность: <?php echo htmlspecialchars($workout['duration']); ?> мин</p>
                                <?php endif; ?>
                            </div>
                            <?php
                            if ($workout_date >= $today):
                            ?>
                                <a href="edit_workout.php?workout_id=<?php echo $workout['id']; ?>">
                                    <button class="btn-action">Изменить данные</button>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
</body>

</html>