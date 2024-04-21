<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--<meta http-equiv="refresh" content="5">-->
  <title>Plataforma IoT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">

</head>

<body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


  <?php
  /* configuração inicial de lugares
  $lugares = [
    [-1, 1, 0, 2],
    [3, 3, 0, -2],
    [-1, 1, 0, 2],
    [1, 1, 0, 9],
    [-1, -1, 1, 1]
    
  ];

  $data = serialize($lugares);
  $filename = './api/files/lugares.txt';
  file_put_contents($filename, $data);
  */
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
  $codigoPorta = 9;

  $controlo_humidade = 50;
  $controlo_temperatura  = 25;

  foreach ($lugares as $linha) {
    foreach ($linha as $posicao) {
      if ($posicao != $codigoPorta) {
        if ($posicao > 0) $lugares_livres++;
        elseif ($posicao < 0) $lugares_ocupados++;
      }
    }
  }
  $lugares_existentes = $lugares_livres + $lugares_ocupados;

  $valor_temperatura = file_get_contents($url . "/api/api.php?nome=temperatura");
  $valor_humidade = file_get_contents($url . "/api/api.php?nome=humidade");
  $valor_iluminacao = file_get_contents($url . "/api/api.php?nome=iluminacao");
  $valor_portas = file_get_contents($url . "/api/api.php?nome=portas");
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
            <a class="nav-link" href="historico.php">Histórico</a>
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
            <h6><img src=<?php
                          if ($valor_temperatura >= $controlo_temperatura) echo "imagens/temperature-high.png ";
                          else echo "imagens/temperature-low.png";
                          ?> width="20px"> Temperatura</h6>
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
                          if ($valor_humidade >= $controlo_humidade) echo "imagens/humidity-high.png ";
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
      </div>
      <div class="col-sm-6">
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
                    echo '<td class="posicao';
                    if ($valor_iluminacao == "1") {
                      echo " luzBaixa";
                    } elseif ($valor_iluminacao == "2") {
                      echo " luzAlta";
                    }
                    if ($posicao != 0) {
                      if ($posicao == $codigoPorta) {
                        if ($valor_portas == 0) {
                          echo ' porta"><img src="imagens\porta.png" widtht="80px" height="80px';
                        }
                      } else {
                        echo ' lugar"><img class="';
                        switch (abs($posicao)) {
                          case 2:
                            echo "oeste";
                            break;
                          case 3:
                            echo "sul";
                            break;
                        }
                        if ($posicao < 0) echo " ocupado ";
                        echo '" src="imagens\lugar.png" widtht="80px" height="80px';
                      }
                    }
                    echo '"></td>';
                  }
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card-header">
          <div class="row">
            <h6>WebCam</h6>
          </div>
        </div>
        <div class="card-body">
          <img src="imagens/webcam.jpg" width="100%">
          <hr>
        </div>

        <div style="display: flex; justify-content: space-around;">
          <div class="card col-sm-5">
            <div class="card-header">
              <h4>Iuminação</h4>
            </div>
            <div class="card-body">
              <div class="d-flex flex-column justify-content-around align-content-center" style="height: 35vh;">
                <button type=" button" class="btn btn-primary" <?php if ($valor_iluminacao == 0) echo "disabled" ?> onclick="toggleIluminacao(0);">OFF</button>
                <button type="button" class="btn btn-primary" <?php if ($valor_iluminacao == 1) echo "disabled" ?> onclick="toggleIluminacao(1);">Baixa</button>
                <button type="button" class="btn btn-primary" <?php if ($valor_iluminacao == 2) echo "disabled" ?> onclick="toggleIluminacao(2);">Alta</button>
              </div>
            </div>
            <div class="card-footer">
              <h6><a href="historico.php?nome=iluminacao">Histórico</a></h6>
            </div>
          </div>

          <div class="card col-sm-5">
            <div class="card-header">
              <h4>Portas</h4>
            </div>
            <div class="card-body">
              <div class="justify-content-around align-content-center" style="height: 35vh;">
                <h4><?php echo ($valor_portas == 0) ? "Fechadas" : "Abertas" ?></h4>
                <img src=<?php echo ($valor_portas == 0) ? "imagens/abrir_portas.png" : "imagens/fechar_portas.png" ?> width="100px">
                <?php
                if ($valor_portas == 0) echo '<button type="button" class="btn btn-success" onclick="togglePortas();";">Abrir</button>';
                else echo '<button type="button" class="btn btn-danger" onclick="togglePortas();">Fechar</button>'; ?>
              </div>
            </div>
            <div class="card-footer">
              <h6><a href="historico.php?nome=iluminacao">Histórico</a></h6>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>


  </div>


  <br>

  <script>
    function getHora() {
      var dateISO = new Date().toISOString();
      var data0 = dateISO.split('T')[0];
      var data1 = dateISO.split('T')[1];
      var time = data1.split('.')[0];
      var datahora = data0 + " " + time;
      return datahora;
    }

    function toggleIluminacao(valor) {
      const data = new URLSearchParams({
        nome: "iluminacao",
        valor: valor,
        hora: getHora()
      });
      fetch('./api/api.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded' // Correct content type
          },
          body: data.toString() // URL-encoded data as a string
        })
        .then(window.location.reload())
    }

    function togglePortas() {
      const data = new URLSearchParams({
        nome: "portas",
        valor: <?php
                if ($valor_portas == 0) echo "1";
                else echo "0";
                ?>,
        hora: getHora()
      });
      fetch('./api/api.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded' // Correct content type
          },
          body: data.toString() // URL-encoded data as a string
        })
        .then(window.location.reload())
    }
  </script>
</body>

</html>