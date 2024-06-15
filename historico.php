<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("refresh:5;url=index.php");
  die("Acesso restrito.");
}
if (!isset($_GET['veiculo']) || !is_dir("api/files/" . $_GET['veiculo'])) {
  header("refresh:2;url=index.php");
  die("Acesso iválido.");
}

$url = 'http://127.0.0.1/projeto/api/api.php?veiculo=' . $_GET['veiculo'];
//$url = 'https://iot.dei.estg.ipleiria.pt/ti/g168';


//nomes permitidos para um pedido de histórico
$valid_names = [];
$directory = './api/files/' . $_GET['veiculo'];
$contents = scandir($directory);
foreach ($contents as $item) {
  if ($item != '.' && $item != '..') {
    // Verifica se é uma diretoria
    if (is_dir($directory . '/' . $item)) {
      $valid_names[] = $item;
    }
  }
}
//valida os parametros do pedido GET para exebição da histórico
if (isset($_GET['nome']) && !in_array($_GET['nome'], $valid_names)) {
  header("refresh:2;url=historico.php");
  die("Historico Inválido.");
}
?>

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
  <!-- barra de navegação -->
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">SmartDrive</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php
          if (trim($_SESSION['role']) === 'Admin') {
            echo '<li class="nav-item">';
            echo '  <a class="nav-link" href="admin.php">Administração</a>';
            echo '</li>';
          }
          ?>
          <li class="nav-item">
            <a class="nav-link" href="dashboard.php?veiculo=<?php echo $_GET['veiculo'] ?>"><?php echo $_GET['veiculo'] ?></a>
          </li>
          <li class="nav-item active">
            <a class="nav-link">Histórico</a>
          </li>
        </ul>
        <span class="navbar-text">
          user: <b><?php echo $_SESSION['username'] ?></b> &nbsp;
        </span>
        <form class="d-flex" action="logout.php" method="post">
          <button class="btn btn-outline-success" type="submit">Logout</button>
        </form>
      </div>
    </div>
  </nav>


  <div class="card">
    <div class="card-header">
      <!--Apresenta o conteúdo do ficheiro nome.txt do dispositivo pretendido-->
      <h1><?php if (isset($_GET['nome']) && $_GET['nome'] != "") echo file_get_contents($url . '&nome=' . $_GET['nome']); ?></h1>
    </div>
    <div class="card-body">
      <table class="table">
        <tr>
          <th>data</th>
          <th>valor</th>
        </tr>
        <!--Apresenta o conteúdo do ficheiro log.txt do dispositivo pretendido-->
        <?php
        if (isset($_GET['nome']) && $_GET['nome'] != "") {
          $linhasLog = file($url . '&log=' . $_GET['nome']);
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
  </div>

  <!--Seletor para escolha de histórico a apresentar-->
  <div class="container " style="position: fixed; bottom: 20px;">
    <form>
      <input type="hidden" name="veiculo" value="<?php echo $_GET['veiculo']; ?>">
      <div>
        <h3>Seleccionar: </h3>
        <select name="nome">
          <option value="" label=" "></option>
          <?php
          foreach ($valid_names as $name) {
            echo '<option value="' . $name . '">' . $name . '</option>';
          }
          ?>
        </select>
        <input type="submit">
      </div>
    </form>
  </div>
</body>

</html>