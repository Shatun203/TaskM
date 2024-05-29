<?php
session_start();
include 'config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение задач пользователя
$sql = "SELECT * FROM Tasks WHERE assigned_to='$user_id'";
$result = $conn->query($sql);

$current_tasks = [];
$completed_tasks = [];
$future_tasks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $today = date('Y-m-d');
        if ($row['status'] == 'Завершено') {
            $completed_tasks[] = $row;
        } elseif ($row['due_date'] > $today) {
            $future_tasks[] = $row;
        } else {
            $current_tasks[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Мои задачи</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Мои задачи</h1>
        <ul class="nav nav-tabs" id="taskTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="current-tasks-tab" data-toggle="tab" href="#current-tasks" role="tab" aria-controls="current-tasks" aria-selected="true">Текущие задачи</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="completed-tasks-tab" data-toggle="tab" href="#completed-tasks" role="tab" aria-controls="completed-tasks" aria-selected="false">Завершенные задачи</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="future-tasks-tab" data-toggle="tab" href="#future-tasks" role="tab" aria-controls="future-tasks" aria-selected="false">Будущие задачи</a>
            </li>
        </ul>
        <div class="tab-content" id="taskTabContent">
            <div class="tab-pane fade show active" id="current-tasks" role="tabpanel" aria-labelledby="current-tasks-tab">
                <div class="list-group mt-3">
                    <?php if (count($current_tasks) > 0): ?>
                        <?php foreach ($current_tasks as $task): ?>
                            <a href="#" class="list-group-item list-group-item-action">
                                <h5 class="mb-1"><?php echo $task['name']; ?></h5>
                                <p class="mb-1"><?php echo $task['description']; ?></p>
                                <small>Срок выполнения: <?php echo $task['due_date']; ?></small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="mt-3">Нет текущих задач.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="completed-tasks" role="tabpanel" aria-labelledby="completed-tasks-tab">
                <div class="list-group mt-3">
                    <?php if (count($completed_tasks) > 0): ?>
                        <?php foreach ($completed_tasks as $task): ?>
                            <a href="#" class="list-group-item list-group-item-action">
                                <h5 class="mb-1"><?php echo $task['name']; ?></h5>
                                <p class="mb-1"><?php echo $task['description']; ?></p>
                                <small>Срок выполнения: <?php echo $task['due_date']; ?></small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="mt-3">Нет завершенных задач.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="future-tasks" role="tabpanel" aria-labelledby="future-tasks-tab">
                <div class="list-group mt-3">
                    <?php if (count($future_tasks) > 0): ?>
                        <?php foreach ($future_tasks as $task): ?>
                            <a href="#" class="list-group-item list-group-item-action">
                                <h5 class="mb-1"><?php echo $task['name']; ?></h5>
                                <p class="mb-1"><?php echo $task['description']; ?></p>
                                <small>Срок выполнения: <?php echo $task['due_date']; ?></small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="mt-3">Нет будущих задач.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
