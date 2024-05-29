<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $name = $_POST['name'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $assigned_to = $_POST['assigned_to'];
    $project_id = $_POST['project_id'];


    // Проверяем, чтобы все поля были заполнены
    if (empty($name) || empty($description) || empty($due_date) || empty($assigned_to)) {
        echo "Пожалуйста, заполните все поля.";
    } else {
        // Добавляем задачу в базу данных
        $sql = "INSERT INTO Tasks (name, description, due_date, assigned_to, project_id) 
                VALUES ('$name', '$description', '$due_date', '$assigned_to', '$project_id')";

        if ($conn->query($sql) === TRUE) {
            echo "Задача успешно добавлена.";
        } else {
            echo "Ошибка: " . $sql . "<br>" . $conn->error;
        }
    }
}
header("Location: main.php");
?>
