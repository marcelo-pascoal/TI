<?php
session_start();
if (isset($_SESSION["username"])) {
    if(trim($_SESSION['role']) !== 'Admin'){
        header("Location: admin.php");
    }
    header("Location: dashboard.php");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Wi-Transport</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        .container {
            text-align: center;
            display: flex;
            width: 300px;
            height: 200px;
            position: absolute;
            top: 50px;
            left: 50%;
            margin-left: -150px;
        }
    </style>
</head>

<!--Formulario de login-->

<body>
    <div class="container">
        <div class="row">
            <form class=" login" method="post" action="api/login.php">
                <h2 class="index">Login</h2>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>

                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <!--caso o login tenha falhado o utilizador é notificado-->
                <div class="flex row">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST")
                        echo '<p class="erro">Credenciais inválidas</p>';
                    ?>
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>