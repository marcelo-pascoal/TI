<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
http_response_code(400);
if (isset($_POST['username']) && isset($_POST['password'])) {

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $users = fopen("./files/users.txt", "r") or http_response_code(500) && die("Erro ao abrir ficheiro");
        while (!feof($users)) {
            $linha = explode(";", fgets($users));
            if ($linha[0] === $_POST['username'] && password_verify($_POST['password'], $linha[1])) {
                $_SESSION["username"] = $_POST['username'];
                $_SESSION["role"] = $linha[2];
                fclose($users);
                http_response_code(200);
                header("Location: ../dashboard.php");
                exit;
            }
        }
        fclose($users);
        http_response_code(401);
        header("Location: ../index.php");
        echo ("login failed");
    }
}
