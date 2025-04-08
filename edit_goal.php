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
if (!isset($_GET['goal_id'])) {
    echo "Цель не найдена.";
    exit;
}

$goal_id = $_GET['goal_id'];

// Получаем данные текущей цели
$query = "SELECT * FROM goals WHERE id = $1 AND user_id = $2";
$result = pg_query_params($conn, $query, [$goal_id, $user_id]);
$goal = pg_fetch_assoc($result);

if (!$goal) {
    echo "Цель не найдена или доступ запрещён.";
    exit;
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['type']);
    $duration = (int)$_POST['duration'];
    $calories = (int)$_POST['calories'];
    $date = $_POST['date'];

    $updateQuery = "UPDATE goals SET type = $1, target_duration = $2, target_calories = $3, target_date = $4 WHERE id = $5 AND user_id = $6";
    $updateResult = pg_query_params($conn, $updateQuery, [$type, $duration, $calories, $date, $goal_id, $user_id]);

    if ($updateResult) {
        echo "<script>
                alert('Цель обновлена успешно!');
                window.location.href = 'dashboard.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Ошибка при обновлении цели.');</script>";
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
            <h1>Изменить цель</h1>
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
                <input type="number" name="duration" id="duration" value="<?php echo $goal['target_duration']; ?>" required>

                <label for="calories">Калории</label>
                <input type="number" name="calories" id="calories" value="<?php echo $goal['target_calories']; ?>" required>

                <label for="date">Дата</label>
                <input type="date" name="date" id="date" value="<?php echo $goal['target_date']; ?>" required>

                <button type="submit">Сохранить изменения</button>
                <button onclick="window.location.href='dashboard.php'; return false;">Отмена</button>
            </form>
        </section>
    </div>
</body>

</html>