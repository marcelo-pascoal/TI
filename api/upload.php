<?php
header('Content-Type: text/html; charset=utf-8');

http_response_code(400);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_FILES['imagem'])) {
        print_r($_FILES['imagem']);
        move_uploaded_file($_FILES['imagem']['tmp_name'], "imagens/webcam.jpg");
    }
} else {
    http_response_code(403);
    echo "metodo nao permitido";
}
