<?php
session_start();

// Подключение к БД
require 'db/connect.php';

$email = trim($_POST['email']);
$password = $_POST['password'];

// Проверка наличия пользователя в БД
$query = pg_query_params($conn, "SELECT id, email, password, name FROM users WHERE email = $1", [$email]);
$user = pg_fetch_assoc($query);

if (!$user) {
    echo "Неверный email или пароль";
    exit;
}

// Проверка пароля
if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'];
    echo "success";
} else {
    echo "Неверный email или пароль";
}
