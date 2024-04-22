<?php
header('Content-Type: text/html; charset=utf-8');

//carrega para memoria o array representante das posições do veículo
$lugares = unserialize(file_get_contents('files/lugares.txt'));

http_response_code(400);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //verificação dos parametros do pedido
    if (isset($_POST['nome']) && isset($_POST['valor']) && isset($_POST['hora']) && is_dir("files/" . $_POST['nome'])) {
        //caso o sensor validado comece por "lugar":
        //são obtidas as coordenadas do lugar e é atualizado o array de posições
        if (strpos($_POST['nome'], "lugar") === 0) {
            $coordenadas = explode("-", substr($_POST['nome'], 5));
            $x = (int)$coordenadas[1];
            $y = (int)$coordenadas[2];
            $valor = $lugares[$x][$y];
            if (($_POST['valor'] == 0 && $valor < 0) || (($_POST['valor'] == 1) && ($valor > 0))) {
                $valor = $valor * -1;
                $lugares[$x][$y] = $valor;
                file_put_contents('./files/lugares.txt', serialize($lugares));
            }
        }
        //atualização dos ficheiros do sensor
        file_put_contents("files/" . $_POST['nome'] . "/valor.txt", $_POST['valor']);
        file_put_contents("files/" . $_POST['nome'] . "/hora.txt", $_POST['hora']);
        file_put_contents("files/" . $_POST['nome'] . "/log.txt", $_POST['hora'] . ";" . $_POST['valor'] . PHP_EOL, FILE_APPEND);
        http_response_code(200);
    } else if (isset($_POST['nome']) && $_POST['nome'] == "controlador" && isset($_POST['temperatura']) && isset($_POST['humidade'])) {
        //caso seja um pedido para atualizacao do controlador da ventoinha grava os dados em ficheiro
        file_put_contents("files/controlador.txt", $_POST['temperatura'] . ";" . $_POST['humidade']);
        http_response_code(200);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
    //PEDIDOS GET

    if (isset($_GET['nome']) && is_dir("files/" . $_GET['nome'])) {
        //informaçao sobre um sensor/atuador
        http_response_code(200);
        echo file_get_contents("files/" . $_GET['nome'] . "/valor.txt");
    } elseif ($_GET['nome'] == "lugares") {
        //envia o array de posições usando o formato de rede json
        http_response_code(200);
        echo json_encode($lugares);
    } elseif ($_GET['nome'] == "controlador") {
        //retorna valores atuais para o controlador da ventoinha
        http_response_code(200);
        echo file_get_contents("files/controlador.txt");
    } else {
        echo "faltam parametros no GET";
    }
} else {
    http_response_code(403);
    echo "metodo nao permitido";
}
