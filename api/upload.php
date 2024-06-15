<?php
header('Content-Type: text/html; charset=utf-8');

http_response_code(400);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_FILES['imagem']) && isset($_POST['veiculo']) && is_dir("files/" . $_POST['veiculo'])) {
        $imagem = $_FILES['imagem'];
        // Verifica o tamanho do arquivo (limite de 1000kB = 1MB)
        if ($imagem['size'] > 1024000) {
            echo "Erro: A imagem tem de ter no máximo 1000kB.";
            exit;
        }

        // Verifica a extensão do arquivo
        $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
        if (!in_array($extensao, ['jpg', 'png'])) {
            echo "Erro: A imagem tem de ser .jpg, ou .png.";
            exit;
        }
        http_response_code(200);
        move_uploaded_file($_FILES['imagem']['tmp_name'], "files/" . $_POST['veiculo'] . "/webcam.jpg");
    }
} else {
    http_response_code(403);
    echo "metodo nao permitido";
}
