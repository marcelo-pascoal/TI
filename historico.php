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
  ?>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">Veículo</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="historico.php">Histórico</a>
          </li>
        </ul>
        <form class="d-flex" action="logout.php" method="post">
          <button class="btn btn-outline-success" type="submit">Logout</button>
        </form>
      </div>
    </div>
  </nav>
  <form>
    <select name="nome">
      <option value="temperatura">Temperatura</option>
      <option value="humidade">Humidade</option>
      <option value="iluminacao">Iluminacao</option>
      <option value="portas">Portas</option>
      <option value="ventoinha">Ventoinha</option>
      <option value="lugar-0-1">lugar-0-1</option>
    </select>
    <br><br>
    <input type="submit">
  </form>

  <h1><?php if (isset($_GET['nome'])) echo file_get_contents("api/files/" . $_GET['nome'] . "/nome.txt"); ?></h1>
  <a href="dashboard.php">Voltar Atrás</a>
  <div>
    <table class="table">
      <tr>
        <th>data</th>
        <th>valor</th>
      </tr>
      <?php
      if (isset($_GET['nome'])) {
        $linhasLog = file("api/files/" . $_GET['nome'] . "/log.txt", FILE_IGNORE_NEW_LINES);
        foreach ($linhasLog as $linha) {
          echo "<tr><td>";
          print_r(explode(';', $linha)[0]);
          echo "</td><td>";
          print_r(explode(';', $linha)[1]);
          echo "</td></tr>";
        }
      }
      ?>
    </table>
  </div>
</body>

</html>