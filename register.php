<?php
// Подключение к БД
require 'db/connect.php';

$email = trim($_POST['email']);
$password = $_POST['password'];
$name = trim($_POST['name']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Некорректный email";
    exit;
}

if (strlen($password) < 6) {
    echo "Пароль должен быть не менее 6 символов";
    exit;
}

// Проверка на существующий email
$check = pg_query_params($conn, "SELECT id FROM users WHERE email = $1", [$email]);
if (pg_num_rows($check) > 0) {
    echo "Такой email уже зарегистрирован";
    exit;
}

// Хэширование и вставка
$hashed = password_hash($password, PASSWORD_DEFAULT);
$result = pg_query_params(
    $conn,
    "INSERT INTO users (email, password, name) VALUES ($1, $2, $3)",
    [$email, $hashed, $name]
);

if ($result) {
    echo "Регистрация прошла успешно!";
} else {
    echo "Ошибка при регистрации";
}
