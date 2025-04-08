<?php
session_start();
require 'db/connect.php';

// // Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Проверка наличия параметра goal_id
if (isset($_GET['goal_id'])) {
    $goal_id = intval($_GET['goal_id']);

    // Удаляем цель только если она принадлежит текущему пользователю
    $query = "DELETE FROM goals WHERE id = $goal_id AND user_id = $user_id";
    $result = pg_query($conn, $query);

    if ($result) {
        $_SESSION['message'] = "Цель успешно удалена.";
    } else {
        $_SESSION['message'] = "Ошибка при удалении цели.";
    }
} else {
    $_SESSION['message'] = "ID цели не указан.";
}

header('Location: dashboard.php');
exit;
