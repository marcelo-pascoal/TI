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
      <a class="navbar-brand" href="#">Estacionamento</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Histórico</a>
          </li>
        </ul>
        <form class="d-flex" action="logout.php" method="post">
          <button class="btn btn-outline-success" type="submit">Logout</button>
        </form>
      </div>
    </div>
  </nav>
  <div class="container">
    <div class="row text-center">
      <div class="col-sm-2">
        <div class="card">
          <div class="card-header sensor">
            <div class="row">
              <h6><img src="temperature-high.png" width="20px">
                Temperatura</h6>
            </div>
          </div>
          <div class="card-body">
            <h1>24ºC</h1>
          </div>
          <div class="card-footer">
            <h6><a href="temperatura.html">Histórico</a></h6>
          </div>
        </div>
        <div class="card">
          <div class="card-header sensor">
            <h6>Humidade 70°</h6>
          </div>
          <div class="card-body">
            <img src="humidity-high.png" width="40px">
          </div>
          <div class="card-footer">
            <h6><b>Atualização:</b> 2024/03/13 22:09 - <a href="humidade.html">Histórico</a></h6>
          </div>
        </div>
        <div class="card">
          <div class="card-header atuador">
            <h6>Iluminação</h6>
          </div>
          <div class="card-body">
            <img src="light-on.png" width="40px">
          </div>
          <div class="card-footer">
            <h6><b>Atualização:</b> 2024/03/13 22:09 - <a href="ledArduino.html">Histórico</a></h6>
          </div>
        </div>
      </div>

      <?php
      // Sample two-dimensional array
      $lugaresEstacionamento = [
        [0, 1, 1, 1, 0, 1],
        [2, 2, 2, 2, 2, 2],
        [0, 1, 1, 1, 0, 2],
        [0, 1, 1, 1, 0, 2],
      ];

      // Serialize the two-dimensional array
      $data = serialize($lugaresEstacionamento);

      // Specify the file path
      $filename = 'lugaresEstacionamento.txt';

      // Write the serialized data to the file
      file_put_contents($filename, $data);
      ?>

      <?php
      // Specify the file path
      $filename = 'lugaresEstacionamento.txt';

      // Read the serialized data from the file
      $data = file_get_contents($filename);

      // Unserialize the data to retrieve the two-dimensional array
      $lugaresEstacionamento = unserialize($data);
      ?>

      <div class="col-sm-8">
        <div class="card">
          <div class="card-header">
            <h4>Tabela de Sensores</h4>
          </div>
          <div class="card-body">
            <table class="table">
              <tbody>
                <?php foreach ($lugaresEstacionamento as $linha) {
                  echo "<tr>";

                  foreach ($linha as $posicao) {
                    if ($posicao == 1) {
                      echo "<td class=\"lugar rotate-image\" style=\"width:16.66%\"><img src=\"carro.png\" height=\"100px\"></td>";
                    }
                    if ($posicao == 0) {
                      echo "<td class=\"lugar rotate-image\" style=\"width:16.66%\"></td>";
                    }
                    if ($posicao == 2) {
                      echo "<td style=\"width:16.66%\"></td>";
                    }
                  }

                  echo "</tr>";
                }
                ?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>