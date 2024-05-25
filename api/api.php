<?php
header('Content-Type: text/html; charset=utf-8');

//carrega para memoria o array representante das posições do veículo

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
            $file = fopen('files/lugares.txt', 'c+');
            if (flock($file, LOCK_EX)) { // Acquire an exclusive lock
                $lugares = unserialize(fread($file, filesize('files/lugares.txt')));
                $valor = $lugares[$x][$y];
                if (($_POST['valor'] == 0 && $valor < 0) || (($_POST['valor'] == 1) && ($valor > 0))) {
                    $valor = $valor * -1;
                    $lugares[$x][$y] = $valor;
                    rewind($file);
                    ftruncate($file, 0);
                    fwrite($file, serialize($lugares));
                    fflush($file);
                }
                flock($file, LOCK_UN); // Release the lock
            } else {
                throw new Exception("Could not lock the file for writing.");
            }
            fclose($file);
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
    //PEDIDOS DE VALOR
    if (isset($_GET['valor'])) {
        if (isset($_GET['valor']) && is_dir("files/" . $_GET['valor'])) {
            //informaçao sobre um sensor/atuador
            http_response_code(200);
            echo file_get_contents("files/" . $_GET['valor'] . "/valor.txt");
        } elseif ($_GET['valor'] == "lugares") {
            //envia o array de posições usando o formato de rede json
            $file = fopen('files/lugares.txt', 'r');
            if (flock($file, LOCK_SH)) { // Acquire a shared lock
                $lugares = unserialize(fread($file, filesize('files/lugares.txt')));
                flock($file, LOCK_UN); // Release the lock
            } else {
                throw new Exception("Could not lock the file for reading.");
            }
            fclose($file);
            http_response_code(200);
            echo json_encode($lugares);
        } elseif ($_GET['valor'] == "controlador") {
            //retorna valores atuais para o controlador da ventoinha
            http_response_code(200);
            echo file_get_contents("files/controlador.txt");
        }
    //PEDIDOS DE LOG   
    } else if (isset($_GET['log']) && is_dir("files/" . $_GET['log'])) {
        http_response_code(200);
        echo file_get_contents("files/" . $_GET['log'] . "/log.txt");
    //PEDIDOS DE NOME
    } else if (isset($_GET['nome']) && is_dir("files/" . $_GET['nome'])) {
        http_response_code(200);
        echo file_get_contents("files/" . $_GET['nome'] . "/nome.txt");
    } else {
        echo "faltam parametros no pedido GET";
    }
} else {
    http_response_code(403);
    echo "metodo nao permitido";
}
