<?php
$host = 'localhost';
$port = '5432';
$user = 'postgres';
$password = '123';
$dbname = 'training';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Ошибка подключения к базе данных: " . pg_last_error());
}
