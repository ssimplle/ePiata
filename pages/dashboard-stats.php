<?php

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }

    if (!$userService->isAdmin($_SESSION['user_id'])) {
        http_response_code(403);
        echo "Access denied.";
        exit;
    }

    $error = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $formName = $_POST["form-name"];

        switch ($formName) {
            case "create-form":
                $name = $_POST["category_name"];

                if (empty($name)) {
                    $error = "Category name is required.";
                } else {
                    $success = $categoryService->create($name);

                    if ($success) {
                        header("Location: index.php?page=categories");
                        exit;
                    }

                    $error = "Something went wrong.";
                }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stats</title>
    </head>
    <body>
        <h1>Stats</h1>  

        <div>
            <h2>Stats</h2>
            
            <form method="POST">
                <input type="hidden" name="form-name" value="create-form">
                <input type="text" name="category_name" placeholder="Category Name" required>
                <button type="submit">Add Category</button>
            </form>
        </div>
    </body>
    </html>