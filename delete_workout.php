<?php
session_start();
require 'db/connect.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Проверка наличия параметра workout_id
if (isset($_GET['workout_id'])) {
    $workout_id = intval($_GET['workout_id']);

    // Удаляем цель только если она принадлежит текущему пользователю
    $query = "DELETE FROM workouts WHERE id = $workout_id AND user_id = $user_id";
    $result = pg_query($conn, $query);

    if ($result) {
        $_SESSION['message'] = "Тренировка успешно удалена.";
    } else {
        $_SESSION['message'] = "Ошибка при удалении тренировки.";
    }
} else {
    $_SESSION['message'] = "ID тренировки не указан.";
}

header('Location: all_workouts.php');
exit;
