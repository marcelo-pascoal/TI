<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="refresh" content="5">
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

  $url = 'http://127.0.0.1/projeto';
  //$url = 'http://iot.dei.estg.ipleiria.pt/ti/g168/projeto';
  $apiUrl = $url . '/api/api.php';
  $params = [
    'nome' => 'lugares'
  ];
  $urlWithParams = $apiUrl . '?' . http_build_query($params);
  $data = file_get_contents($urlWithParams);
  $lugares = json_decode($data, true);
  $lugares_existentes = 0;
  $lugares_ocupados = 0;
  $lugares_livres = 0;

  foreach ($lugares as $linha) {
    foreach ($linha as $posicao) {
      switch ($posicao) {
        case 1:
          $lugares_livres++;
          $lugares_existentes++;
          break;
        case 2:
          $lugares_ocupados++;
          $lugares_existentes++;
          break;
      }
    }
  }

  $valor_temperatura = file_get_contents($url . "/api/api.php?nome=temperatura");
  $valor_humidade = file_get_contents($url . "/api/api.php?nome=humidade");
  $valor_iluminacao = file_get_contents($url . "/api/api.php?nome=iluminacao");
  ?>


  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Veículo</a>
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
          <div class="card-header">
            <div class="row">
              <h6><img src=<?php
                            if ($valor_temperatura >= 20) echo "imagens/temperature-high.png ";
                            else echo "imagens/temperature-low.png";
                            ?> width="20px"> Temperatura</h6>
            </div>
          </div>
          <div class="card-body">
            <h1><?php echo $valor_temperatura ?>ºC</h1>
          </div>
          <div class="card-footer">
            <h6><a href="historico.php?nome=temperatura">Histórico</a></h6>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h6><img src=<?php
                          if ($valor_humidade >= 50) echo "imagens/humidity-high.png ";
                          else echo "imagens/humidity-low.png";
                          ?> width="20px">Humidade</h6>
          </div>
          <div class="card-body">
            <h1><?php echo $valor_humidade ?>%</h1>
          </div>
          <div class="card-footer">
            <h6><a href="historico.php?nome=humidade">Histórico</a></h6>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h6><img src=<?php
                          if ($valor_iluminacao) echo "imagens/light-on.png";
                          else echo "imagens/light-off.png";
                          ?> width="20px">Iluminação</h6>
          </div>
          <div class="card-body">
            <h1><?php
                if ($valor_iluminacao) echo "ON";
                else echo "OFF";
                ?></h1>
          </div>
          <div class="card-footer">
            <h6><a href="historico.php?nome=iluminacao">Histórico</a></h6>
          </div>
        </div>
      </div>




      <div class="col-sm-8">
        <div class="card">
          <div class="card-header">
            <h4>Lugares</h4>
            <div class="row">
              <div class="col-sm-4">
                <h6>Disponiveis</h6>
                <h6><?php echo $lugares_livres ?></h6>
              </div>
              <div class="col-sm-4">
                <h6>Ocupados</h6>
                <h6><?php echo $lugares_ocupados ?></h6>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table class="table">
              <tbody>
                <?php foreach ($lugares as $linha) {
                  echo "<tr>";
                  foreach ($linha as $posicao) {
                    if ($posicao == 0) {
                      echo "<td></td>";
                    } else {
                      echo "<td class=\"lugar ";
                      if ($posicao < 0) echo " ocupado ";
                      echo "style=\"\"><img class=\"";
                      switch (abs($posicao)) {
                        case 2:
                          echo " oeste ";
                          break;
                        case 3:
                          echo " sul ";
                          break;
                      }

                      echo "\" src=\"imagens\lugar.png\" widtht=\"100px\" height=\"100px\"></td>";
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


    <div class="card top-right-card">
      <div class="card-header">
        <div class="row">
          <h6>WebCam</h6>
        </div>
      </div>
      <div class="card-body">
        <img src="imagens/webcam.jpg">

      </div>

    </div>
  </div>


  </div>


  <br>


</body>

</html>