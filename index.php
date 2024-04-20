<?php
#Login
#Verifica se as variáveis 'username' e 'password' estão definidas através de pedido POST.
#       Caso não seja possível abrir o ficheiro de utilizadores é retornado erro de servidor (500)
#       precorre o ficheiro linha a linha e explode cada uma para validação
#           Caso o username seja igual ao username fornecido e a password seja validada com o hash guardado
#               o utilizador é redireccionado para o dashboard
#               o username é defenido como variável de sessão para posterior verificação. 
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    $users = fopen("./users.txt", "r") or http_response_code(500) && die("Erro ao abrir ficheiro");
    while (!feof($users)) {
        $linha = explode(";", fgets($users));
        if (strcmp($linha[0], $_POST['username']) && password_verify($_POST['password'], $linha[1])) {
            $_SESSION["username"] = $_POST['username'];
            fclose($users);
            header("Location: dashboard.php");
        }
    }
    fclose($users);
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
</head>

<!--Formulario de login-->

<body>
    <div class="container">
        <div class="row justify-content-center">
            <form class="login" method="post">
                <a href="index.php"><img src="./img/estg_h.png" alt=""></a>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>