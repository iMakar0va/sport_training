<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Подключение к базе данных
require 'db/connect.php';

// Обработка формы добавления тренировки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $user_id = $_SESSION['user_id'];
    $type = trim($_POST['type']);
    $duration = (int)$_POST['duration'];
    $calories = (int)$_POST['calories'];
    $date = $_POST['date'];

    // Вставляем данные в таблицу workouts
    $query = "INSERT INTO goals (user_id, type, target_duration, target_calories, target_date)
              VALUES ($1, $2, $3, $4, $5)";

    $result = pg_query_params($conn, $query, [$user_id, $type, $duration, $calories, $date]);

    if ($result) {
        echo "<script>
                alert('Цель успешно добавлена!');
                window.location.href = 'dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Произошла ошибка при создании цели.');
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить цель</title>
    <link rel="stylesheet" href="assets/css/form.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Добавить цель</h1>
            <p>Заполни форму для добавления цели</p>
            <a href="dashboard.php"><button type="submit" class="btn">Назад к панели</button></a>
        </header>
        <section class="form-container">
            <form id="workoutForm" action="add_goal.php" method="POST">
                <label for="type">Тип тренировки</label>
                <select name="type" id="type" required>
                    <option value="бег">Бег</option>
                    <option value="ходьба">Ходьба</option>
                    <option value="силовая тренировка">Силовая тренировка</option>
                    <option value="плавание">Плавание</option>
                    <option value="велосипед">Велосипед</option>
                    <option value="йога">Йога</option>
                    <option value="аэробика">Аэробика</option>
                    <option value="кардиотренировка">Кардиотренировка</option>
                    <option value="растяжка">Растяжка</option>
                    <option value="гребля">Гребля</option>
                    <option value="танцы">Танцы</option>
                    <option value="пеший туризм">Пеший туризм</option>
                    <option value="скалолазание">Скалолазание</option>
                    <option value="лыжи">Лыжи</option>
                    <option value="боевые искусства">Боевые искусства</option>
                </select>

                <label for="duration">Длительность (в минутах)</label>
                <input type="number" name="duration" id="duration" placeholder="Время тренировки" required>

                <label for="calories">Калории</label>
                <input type="number" name="calories" id="calories" placeholder="Количество калорий" required>

                <label for="date">Дата</label>
                <input type="date" name="date" id="date" required>

                <button type="submit">Добавить цель</button>
            </form>
        </section>
    </div>
</body>

</html>