<?php

header('Content-Type: text/html; charset=utf-8');

//echo $_SERVER['REQUEST_METHOD'];
//var_dump(file_get_contents("php://input"));

$lugares = unserialize(file_get_contents('files/lugares.txt'));

http_response_code(400);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //echo "recebi um POST<br>";
    //print_r($_POST);
    if (isset($_POST['nome']) && isset($_POST['valor']) && isset($_POST['hora']) && is_dir("files/" . $_POST['nome'])) { // verificar o metodo pela existencia de ficheiros
        if (strpos($_POST['nome'], "lugar") === 0) {
            $coordenadas = explode("-", substr($_POST['nome'], 5));
            $x = $coordenadas[0];
            $y = $coordenadas[1];
            $valor = $lugares[$x][$y];
            if (($_POST['valor'] == 0 && $valor < 0) || (($_POST['valor'] == 1) && ($valor > 0))) {
                $valor = $valor * -1;
                $lugares[$x][$y] = $valor;
                file_put_contents('./files/lugares.txt', serialize($lugares));
            }
        }
        http_response_code(200);
        file_put_contents("files/" . $_POST['nome'] . "/valor.txt", $_POST['valor']);
        file_put_contents("files/" . $_POST['nome'] . "/hora.txt", $_POST['hora']);
        file_put_contents("files/" . $_POST['nome'] . "/log.txt", $_POST['hora'] . ";" . $_POST['valor'] . PHP_EOL, FILE_APPEND);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
    //echo "recebi um GET<br>";
    if (isset($_GET['nome']) && is_dir("files/" . $_GET['nome'])) {
        http_response_code(200);
        echo file_get_contents("files/" . $_GET['nome'] . "/valor.txt");
    } elseif ($_GET['nome'] == "lugares") {
        http_response_code(200);
        echo json_encode($lugares);
    } else {
        echo "faltam parametros no GET";
    }
} else {
    http_response_code(403);
    echo "metodo nao permitido";
}
