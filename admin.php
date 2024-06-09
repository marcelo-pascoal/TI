<?php
/*Verifica se foi inicializada a variavel de sessao username*/
session_start();
if (!isset($_SESSION['username']) || trim($_SESSION['role']) !== 'Admin') {
    header("refresh:2;url=index.php");
    die("Acesso restrito.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma IoT</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- barra de navegação -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand">SmartDrive</a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item active">
                    <a class="nav-link" href="admin.php"><b>Administração</b> </a>
                </li>
            </ul>
            <span class="navbar-text">
                user: <b><?php echo $_SESSION['username'] ?></b> &nbsp;
            </span>
            <form class="d-flex" action="logout.php" method="post">
                <button class="btn btn-outline-success" type="submit">Logout</button>
            </form>
        </div>
    </nav>
    <br>
    <div class="container">
        <div class="row text-center">
            <?php
            $users = fopen("./api/files/users.txt", "r") or http_response_code(500) && die("Erro ao abrir ficheiro");
            fgets($users);
            fgets($users);
            while (!feof($users)) {
                $linha = explode(";", fgets($users));
                $veiculo = $linha[2];
                echo '<div class="col-sm-4">';
                echo '  <div class="card">';
                echo '      <div class="card-header">';
                echo '          <h6>' . $veiculo . '</h6>';
                echo '      <div class="card-body">';
                echo "          <img src='api/files/" . $veiculo . "/webcam.jpg' style='width:100%'>";
                echo '      </div>';
                echo '      <div class="card-footer">';
                echo '          <h6><a href="dashboard.php?veiculo=' . $veiculo . '">Gerir</a></h6>';
                echo '      </div>';
                echo '     </div>';
                echo '  </div>';
                echo '</div>';
            }
            fclose($users);
            ?>
        </div>
    </div>
</body>