<?php
#verificação de início de sessão 
#   Verifica se a variável 'username' está definida através de pedido POST.
#   Caso esteja verifica se ela se encontra no array 'usernames' carregado do ficheiro 'users.txt'
#   A posição no array irá corresponder à posição no array 'hashes' carregado do ficheiro 
#'users_hash.txt' que guarda a hash das passwords e a mesma é comparada com o hash obtido 
#pela 'password' recebida também por POST.
#   Caso a verificação seja bem sucedida o utilizador é redireccionado para o dashboard e o username
#é defenido como variável de sessão para posterior verificação. 
    session_start();
    $password_hash="";
    if(isset($_POST['username'])){
        $usernames=array_map('rtrim', file('./users.txt'));
        $hashes=array_map('rtrim', file('./users_hash.txt'));
        if($key = array_search($_POST['username'], $usernames)){
            $password_hash=$hashes[$key];
        }
    }
    if (isset($_POST['password']) and password_verify($_POST['password'], $password_hash)) {
        $_SESSION["username"]=$_POST['username'];
        header("Location: dashboard.php");
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Wi-Food</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row justify-content-center">
            <form class="login" method="post">
            <a href="index.php"><img src="./img/estg_h.png" alt=""></a>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username"  placeholder="." name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" placeholder="." name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" >Submit</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  </body>
</html>