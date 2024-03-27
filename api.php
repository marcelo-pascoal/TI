<?php
#   Verificação de sessão iniciada. (identico a dashboard.php)
/*
session_start();
if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
}
header('Content-Type: text/html; charset=utf-8');
*/

#  A variável 'checkRef' será um array com as referências possíveis de chamada à api
$checkRef = array("lugares");

# Carregamento de variaveis
$lugaresEstacionamento = unserialize(file_get_contents('lugaresEstacionamento.txt'));

####Caso seja rececionado um pedido GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    #  O código de resposta é colocado 400 (bad response) antes de verificar o pedido
    http_response_code(400);
    #  Caso a variavel 'ref' (referência) esteja definida no pedido
    if (isset($_GET['ref'])) {
        $ref = $_GET['ref'];
        #  Verifica se ela é válida
        if (in_array($ref, $checkRef)) {
            switch ($ref) {
                case "lugares":
                    http_response_code(200);
                    echo json_encode($lugaresEstacionamento);
                    break;
            }
        }
        #Caso a referencia não seja válida
        else {
            header("refresh:5;url=./dashboard.php");
            die("Dados incorrectos");
        }
    }
    #Caso a referência não esteja definida no pedido
    else {
        header("refresh:5;url=./dashboard.php");
        die("Dados incorrectos");
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    

    #caso não seja um pedido GET ou POST é retornado o código 403 (nao permitido)
} else http_response_code(403);
