<?php
session_start();
include 'config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение информации о пользователе
$sql_user = "SELECT * FROM users WHERE user_id='$user_id'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $role = $user['role'];
}

// Функции доступные для всех кроме менеджера


// Функции доступные только менеджеру
function showManagerTasks($conn, $user_id)
{
    // Получение задач, связанных с менеджером
    $sql = "SELECT tasks.*, users.name AS assigned_to_name
            FROM tasks
            INNER JOIN users ON tasks.assigned_to = users.user_id
            WHERE tasks.project_id IN (
                SELECT project_id FROM users WHERE role = 'Менеджер'
            )";
    // Выполнение запроса и отображение задач менеджера
    $result = $conn->query($sql);

    $manager_tasks = [];
    $assigned_users = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $manager_tasks[] = $row;
            $assigned_users[$row['assigned_to']] = $row['assigned_to_name'];
        }
    }

    echo '<div class="row mt-5">
            <div class="col">
                <h2>Задачи менеджера</h2>
                <ul class="list-group">';
    if (count($manager_tasks) > 0) {
        foreach ($manager_tasks as $task) {
            echo '<li class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">' . $task['name'] . '</h5>
                                    <small>Назначено: ' . $assigned_users[$task['assigned_to']] . '</small>
                                </div>
                                <p class="mb-1">' . $task['description'] . '</p>
                                <small>Срок выполнения: ' . $task['due_date'] . '</small>
                            </li>';
        }
    } else {
        echo '<li class="list-group-item">Нет задач для отображения</li>';
    }
    echo '</ul>
            </div>
        </div>';

    echo '<div class="row mt-5">
            <div class="col">
                <h2>Добавить задачу</h2>
                <form method="post" action="add_task.php">
                <input type="hidden" name="user_id" value="' . $user_id . '">
                    <div class="form-group">
                        <label>Название:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Срок выполнения:</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Назначить пользователю:</label>
                        <select name="assigned_to" class="form-control" required>
                            <option value="">Выберите пользователя</option>';
    // Получение списка пользователей, которым можно назначить задачу
    $sql_users = "SELECT * FROM users WHERE role != 'Менеджер'";
    $result_users = $conn->query($sql_users);
    if ($result_users->num_rows > 0) {
        while ($row_user = $result_users->fetch_assoc()) {
            echo '<option value="' . $row_user['user_id'] . '">' . $row_user['name'] . '</option>';
        }
    }
    echo '</select>
                    </div>
                    <div class="form-group">
                    <label>Проект:</label>
                    <select name="project_id" class="form-control" required>
                        <option value="">Выберите проект</option>';
// Здесь вы можете получить список проектов, к которым у пользователя есть доступ, из базы данных и добавить их в выпадающий список
$sql_projects = "SELECT * FROM teams WHERE user_id='$user_id'";
$result_projects = $conn->query($sql_projects);
if ($result_projects->num_rows > 0) {
    while ($row_project = $result_projects->fetch_assoc()) {
        echo '<option value="' . $row_project['project_id'] . '">' . $row_project['project_id'] . '</option>';
    }
}
echo '</select>
                </div>
                    <button type="submit" class="btn btn-primary">Добавить задачу</button>
                </form>
            </div>
        </div>';
}

// Отображение раздела в зависимости от роли пользователя
if ($role === 'Менеджер') {
    showManagerTasks($conn, $user_id);
} else {
    header("Location: ShowUserTask.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Мои задачи</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Ваша разметка страницы (если есть) -->
</body>
</html>