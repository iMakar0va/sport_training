<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Подключение к базе данных
require 'db/connect.php';

// Получаем ID цели из GET-запроса
if (!isset($_GET['workout_id'])) {
    echo "Тренировка не найдена.";
    exit;
}

$workout_id = $_GET['workout_id'];

// Получаем данные текущей тренировки
$query = "SELECT * FROM workouts WHERE id = $1 AND user_id = $2";
$result = pg_query_params($conn, $query, [$workout_id, $user_id]);
$goal = pg_fetch_assoc($result);

if (!$goal) {
    echo "Тренировка не найдена или доступ запрещён.";
    exit;
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['type']);
    $duration = (int)$_POST['duration'];
    $calories = (int)$_POST['calories'];
    $date = $_POST['date'];

    $updateQuery = "UPDATE workouts SET type = $1, duration = $2, calories = $3, date = $4 WHERE id = $5 AND user_id = $6";
    $updateResult = pg_query_params($conn, $updateQuery, [$type, $duration, $calories, $date, $workout_id, $user_id]);

    if ($updateResult) {
        echo "<script>
                alert('Тренировка обновлена успешно!');
                window.location.href = 'all_workouts.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Ошибка при обновлении тренировки.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Изменить цель</title>
    <link rel="stylesheet" href="assets/css/form.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Изменить тренировку</h1>
            <a href="dashboard.php"><button type="submit" class="btn">Назад к панели</button></a>
        </header>
        <section class="form-container">
            <form method="POST">
                <label for="type">Тип тренировки</label>
                <select name="type" id="type" required>
                    <option value="бег" <?php if ($goal['type'] == 'бег') echo 'selected'; ?>>Бег</option>
                    <option value="ходьба" <?php if ($goal['type'] == 'ходьба') echo 'selected'; ?>>Ходьба</option>
                    <option value="силовая тренировка" <?php if ($goal['type'] == 'силовая тренировка') echo 'selected'; ?>>Силовая тренировка</option>
                    <option value="плавание" <?php if ($goal['type'] == 'плавание') echo 'selected'; ?>>Плавание</option>
                    <option value="велосипед" <?php if ($goal['type'] == 'велосипед') echo 'selected'; ?>>Велосипед</option>
                    <option value="йога" <?php if ($goal['type'] == 'йога') echo 'selected'; ?>>Йога</option>
                    <option value="аэробика" <?php if ($goal['type'] == 'аэробика') echo 'selected'; ?>>Аэробика</option>
                    <option value="кардиотренировка" <?php if ($goal['type'] == 'кардиотренировка') echo 'selected'; ?>>Кардиотренировка</option>
                    <option value="растяжка" <?php if ($goal['type'] == 'растяжка') echo 'selected'; ?>>Растяжка</option>
                    <option value="гребля" <?php if ($goal['type'] == 'гребля') echo 'selected'; ?>>Гребля</option>
                    <option value="танцы" <?php if ($goal['type'] == 'танцы') echo 'selected'; ?>>Танцы</option>
                    <option value="пеший туризм" <?php if ($goal['type'] == 'пеший туризм') echo 'selected'; ?>>Пеший туризм</option>
                    <option value="скалолазание" <?php if ($goal['type'] == 'скалолазание') echo 'selected'; ?>>Скалолазание</option>
                    <option value="лыжи" <?php if ($goal['type'] == 'лыжи') echo 'selected'; ?>>Лыжи</option>
                    <option value="боевые искусства" <?php if ($goal['type'] == 'боевые искусства') echo 'selected'; ?>>Боевые искусства</option>
                </select>

                <label for="duration">Длительность (в минутах)</label>
                <input type="number" name="duration" id="duration" value="<?php echo $goal['duration']; ?>" required>

                <label for="calories">Калории</label>
                <input type="number" name="calories" id="calories" value="<?php echo $goal['calories']; ?>" required>

                <label for="date">Дата</label>
                <input type="date" name="date" id="date" value="<?php echo $goal['date']; ?>" required>

                <button type="submit">Сохранить изменения</button>
                <button onclick="window.location.href='all_workouts.php'; return false;">Отмена</button>
            </form>
        </section>
    </div>
</body>

</html>