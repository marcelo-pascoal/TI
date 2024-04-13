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
  $apiUrl = 'http://127.0.0.1/projeto/api/api.php';
  $params = [
    'ref' => 'lugares'
  ];
  $urlWithParams = $apiUrl . '?' . http_build_query($params);
  $data = file_get_contents($urlWithParams);
  $lugares = json_decode($data, true);
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
              <h6><img src="imagens/temperature-high.png" width="20px">
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
            <h6><img src="imagens/humidity-high.png" width="20px">Humidade</h6>
          </div>
          <div class="card-body">
            <h1>50%</h1>
          </div>
          <div class="card-footer">
            <h6><a href="humidade.html">Histórico</a></h6>
          </div>
        </div>
        <div class="card">
          <div class="card-header atuador">
            <h6><img src="imagens/light-on.png" width="20px">Iluminação</h6>
          </div>
          <div class="card-body">
            <h1>20%</h1>
          </div>
          <div class="card-footer">
            <h6><a href="ledArduino.html">Histórico</a></h6>
          </div>
        </div>
      </div>



      <div class="col-sm-8">
        <div class="card">
          <div class="card-header">
            <h4>Tabela de Sensores</h4>
          </div>
          <div class="card-body">
            <table class="table">
              <tbody>
                <?php foreach ($lugares as $linha) {
                  echo "<tr>";

                  foreach ($linha as $posicao) {
                    switch ($posicao) {
                      case 0:
                        echo "<td style=\"width:16.66%\"></td>";
                        break;
                      case 1:
                        echo "<td class=\"lugar livre\" style=\"width:16.66%\"><img src=\"imagens\lugar_vazio.png\" height=\"100px\"></td>";
                        break;
                      case 2:
                        echo "<td class=\"lugar ocupado\" style=\"width:16.66%\"><img src=\"imagens\lugar_ocupado.png\" height=\"100px\"></td>";
                        break;
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

</body>

</html>