<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma IoT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("refresh:5;url=index.php");
        die("Acesso restrito.");
    }
    if (!isset($_GET['nome']) || !is_dir("api/files/" . $_GET['nome'])) {
        header("refresh:5;url=dashboard.php");
        die("Historico Inexistente.");
    }
    ?>

    <h1><?php echo $_GET['nome'] ?></h1>
    <a href="dashboard.php">Voltar Atrás</a>
    <div>
        <table class="table">
            <tr>
                <th>data</th>
                <th>valor</th>
            </tr>
            <?php
            $linhasLog = file("api/files/" . $_GET['nome'] . "/log.txt", FILE_IGNORE_NEW_LINES);
            foreach ($linhasLog as $linha) {
                echo "<tr><td>";
                print_r(explode(';', $linha)[0]);
                echo "</td><td>";
                print_r(explode(';', $linha)[1]);
                echo "</td></tr>";
            }
            ?>
        </table>
    </div>
</body>

</html>