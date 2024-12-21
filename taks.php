<?php

define("TASKS_FILE", "tasks.json");


function loadTasks(): array
{
    return file_exists(TASKS_FILE) ? json_decode(file_get_contents(TASKS_FILE), true) ?? [] : [];
}


function saveTasks(array $tasks): void
{
    file_put_contents(TASKS_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}


$tasks = loadTasks();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['task'])) {

        $tasks[] = [
            'task' => htmlspecialchars(trim($_POST['task'])),
            'done' => false
        ];
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['delete'])) {

        unset($tasks[$_POST['delete']]);
        saveTasks(array_values($tasks)); // Re-index array
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['toggle'])) {

        $tasks[$_POST['toggle']]['done'] = !$tasks[$_POST['toggle']]['done'];
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
        body {
            margin-top: 20px;
            background-color: #f4f5f7;
            color: #333;
        }

        .task-card {
            border: 1px solid #c4a7e7;
            padding: 20px;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(88, 67, 126, 0.15);
        }

        .task {
            color: #565656;
            font-size: 16px;
        }

        .task-done {
            text-decoration: line-through;
            color: #a1a1a1;
        }

        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        ul {
            padding-left: 0;
        }

        button {
            cursor: pointer;
            background-color: #6a4cae;
            color: #ffffff;
            border: none;
            align-items: center;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            align-items: center;
        }

        button:hover {
            background-color: #5a3c9a;
        }

        .button-outline {
            background-color: transparent;
            border: 1px solidrgb(212, 214, 112);
            color: #6a4cae;
        }

        .button-outline:hover {
            background-color: #6a4cae;
            color: #ffffff;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="task-card">
            <h1>To-Do App</h1>


            <form method="POST">
                <div class="row">
                    <div class="column column-75">
                        <input type="text" name="task" placeholder="Enter a new task" required>
                    </div>
                    <div class="column column-25">
                        <button type="submit" class="button-primary">Add Task</button>
                    </div>
                </div>
            </form>


            <h2>TASK LIST</h2>
            <ul style="list-style: none; padding: 0;">
                <?php if (empty($tasks)): ?>
                    <li>Your list is empty for now. Add a task to begin your day!</li>
                <?php else: ?>
                    <?php foreach ($tasks as $index => $task): ?>
                        <li class="task-item">

                            <form method="POST" style="flex-grow: 1;">
                                <input type="hidden" name="toggle" value="<?= $index ?>">
                                <button type="submit" style="border: none; background: none; cursor: pointer; text-align: left; width: 100%;">
                                    <span class="task <?= $task['done'] ? 'task-done' : '' ?>">
                                        <?= htmlspecialchars($task['task']) ?>
                                    </span>
                                </button>
                            </form>


                            <form method="POST">
                                <input type="hidden" name="delete" value="<?= $index ?>">
                                <button type="submit" class="button button-outline" style="margin-left: 10px;">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>

</html>