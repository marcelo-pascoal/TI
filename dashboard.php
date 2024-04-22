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
  session_start();
  if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
  }
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

  $url = 'http://127.0.0.1/projeto';
  //$url = 'http://iot.dei.estg.ipleiria.pt/ti/g168';
  $apiUrl = $url . '/api/api.php';
  $params = ['nome' => 'lugares'];
  $urlWithParams = $apiUrl . '?' . http_build_query($params);
  $data = file_get_contents($urlWithParams);
  $lugares = json_decode($data, true);

  $lugares_existentes = $lugares_ocupados = $lugares_livres = 0;
  $codigoPorta = 9;
  foreach ($lugares as $linha) {
    foreach ($linha as $posicao) {
      if ($posicao != $codigoPorta) {
        if ($posicao > 0) $lugares_livres++;
        elseif ($posicao < 0) $lugares_ocupados++;
      }
    }
  }
  $lugares_existentes = $lugares_livres + $lugares_ocupados;

  $atuador_iluminacao = file_get_contents($url . "/api/api.php?nome=iluminacao");
  $atuador_portas = file_get_contents($url . "/api/api.php?nome=portas");
  ?>

  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <!-- barra de navegação -->
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">Veículo</a>
      <button class=" navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
        <!-- SENSOR DE TEMPERATURA -->
        <div class="card">
          <div class="card-header">
            <h6>Temperatura</h6>
          </div>
          <div class="card-body">
            <h1 id="temperatura"></h1>
          </div>
          <div class="card-footer">
            <h6><a href="historico.php?nome=temperatura">Histórico</a></h6>
          </div>
        </div>
        <!-- SENSOR DE HUMIDADE -->
        <div class="card ">
          <div class="card-header">
            <h6>Humidade</h6>
          </div>
          <div class="card-body">
            <h1 id="humidade"></h1>
          </div>
          <div class="card-footer">
            <h6><a href="historico.php?nome=humidade">Histórico</a></h6>
          </div>
        </div>
        <hr>
        <!-- CONTROLADOR DE ATUADOR DE VENTOINHA -->
        <div class="card">
          <div class="card-header">
            <h6>Ventilação</h6>
          </div>
          <div class="ventoinha">
            <img src="imagens/fan.png " width="100px" id="ventoinha">
            <!-- envio de pedido post com as configurações -->
            <button type="button" class="btn btn-info" onclick="setControlador();">SET</button>
          </div>
          <div class="card-footer">
            <!-- valores a serem usados pelo controlador -->
            <div class="d-flex">
              <div class="col-sm-3">
                <img src="imagens/temperature-high.png " width="30px">
              </div>
              <div class="col-sm-3">
                <input type="text" style="width: 30px;" id="controlo_temperatura" />
              </div>
              <div class="col-sm-3">
                <img src="imagens/humidity-high.png " width="30px">
              </div>
              <div class="col-sm-3">
                <input type="text" style="width: 30px;" id="controlo_humidade" />
              </div>
              <div class="col-sm-1">
              </div>
            </div>
            <hr>
            <h6><a href="historico.php?nome=ventoinha">Histórico</a></h6>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <!-- Monitorização de Sensores de Lugares  -->
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
            <!-- tabela de lugares
                  constroi a tabela de lugares interpretando o array $lugares-->
            <table class="table">
              <tbody>
                <?php
                $X = -1;
                foreach ($lugares as $linha) {
                  $X++;
                  $Y = -1;
                  echo "<tr>";
                  foreach ($linha as $posicao) {
                    $Y++;
                    echo '<td class="posicao';
                    //classe CSS para iluminação
                    if ($atuador_iluminacao == "1") {
                      echo " luzBaixa";
                    } elseif ($atuador_iluminacao == "2") {
                      echo " luzAlta";
                    }
                    if ($posicao != 0) {
                      //caso seja uma porta
                      if ($posicao == $codigoPorta) {
                        if ($atuador_portas == 0) {
                          echo ' porta"><img src="imagens\porta.png" widtht="80px" height="80px>';
                        }
                      }
                      //classe CSS para rotação das imagens dos lugares
                      else {
                        echo ' lugar"><a href="historico.php?nome=lugar-' . $X . "-" . $Y . '"><img class="';
                        switch (abs($posicao)) {
                          case 2:
                            echo "oeste";
                            break;
                          case 3:
                            echo "sul";
                            break;
                        }
                        //classe CSS para sinalização de lugares ocupados
                        if ($posicao < 0) echo " ocupado ";
                        echo '" src="imagens\lugar.png" widtht="80px" height="80px></a>';
                      }
                    }
                    echo '"</td>';
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
        <!-- Espaço para implentação de video -->
        <hr>
        <img src="imagens/webcam.jpg" width="100%">
        <hr>
        <div class="atuadores">
          <!--Atuador de Iluminação
              apresenta e atualiza os modos de iluminação disponíveis marcando o estado atual como indisponível-->
          <div class="card col-sm-5">
            <div class="card-header">
              <h4>Iuminação</h4>
            </div>
            <div class="card-body">
              <div class="d-flex flex-column justify-content-around align-content-center" style="height: 35vh;">
                <button type="button" class="btn btn-primary" onclick="toggleIluminacao(0);" id="butao_iluminacao_0">OFF</button>
                <button type="button" class="btn btn-primary" onclick="toggleIluminacao(1);" id="butao_iluminacao_1">Baixa</button>
                <button type="button" class="btn btn-primary" onclick="toggleIluminacao(2);" id="butao_iluminacao_2">Alta</button>
              </div>
            </div>
            <div class="card-footer">
              <h6><a href="historico.php?nome=iluminacao">Histórico</a></h6>
            </div>
          </div>
          <!--Atuador de Porta
              apresenta e alterna o estado das portas-->
          <div class="card col-sm-5">
            <div class="card-header">
              <h4>Portas</h4>
            </div>
            <div class="card-body">
              <div class="justify-content-around align-content-center" style="height: 35vh;">
                <h4 id="estado_portas"></h4>
                <img src="imagens/abrir_portas.png" width="100px" id="imagem_portas">
                <button type="button" class="btn btn-success" onclick="togglePortas(1);" id="butao_portas">Abrir</button>
              </div>
            </div>
            <div class="card-footer">
              <h6><a href="historico.php?nome=portas">Histórico</a></h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const intervalSensores = setInterval(updateEstados, 1000);
    const intervalControlador = setInterval(updateControlador, 10000);
    updateEstados();
    updateControlador();

    //função fornecida para criação de timestamp
    function getHora() {
      var dateISO = new Date().toISOString();
      var data0 = dateISO.split('T')[0];
      var data1 = dateISO.split('T')[1];
      var time = data1.split('.')[0];
      var datahora = data0 + " " + time;
      return datahora;
    }

    //funçao para alterar o valor da iluminação
    function toggleIluminacao(valor) {
      const data = new URLSearchParams({
        nome: "iluminacao",
        valor: valor,
        hora: getHora()
      });
      fetch('./api/api.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data.toString()
      })
    }

    //funçao para abertura/fecho de portas, usa o estado atual para alteranar para o estado oposto
    function togglePortas(valor) {
      const data = new URLSearchParams({
        nome: "portas",
        valor: valor,
        hora: getHora()
      });
      fetch('./api/api.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data.toString()
      })
    }

    //funçao para atualização dos parametros para o controlador da ventoinha
    function setControlador() {
      const data = new URLSearchParams({
        nome: "controlador",
        temperatura: document.getElementById("controlo_temperatura").value,
        humidade: document.getElementById("controlo_humidade").value
      });
      fetch('./api/api.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: data.toString()
        })
        .then(window.location.reload())
    }

    function updateControlador() {
      fetch("./api/api.php?nome=controlador")
        .then(response => response.text())
        .then(data => {
          var inputTemperatura = document.getElementById("controlo_temperatura");
          var inputHumidade = document.getElementById("controlo_humidade");
          valores_controlador = data.split(";");
          console.log(valores_controlador[0]);
          inputTemperatura.value = valores_controlador[0];
          inputHumidade.value = valores_controlador[1];

        })
        .catch(error => console.error(error));
    }

    function updateEstados() {
      fetch("./api/api.php?nome=temperatura")
        .then(response => response.text())
        .then(data => document.getElementById("temperatura").innerHTML = data + "ºC")
        .catch(error => console.error(error));

      fetch("./api/api.php?nome=humidade")
        .then(response => response.text())
        .then(data => document.getElementById("humidade").innerHTML = data + "%")
        .catch(error => console.error(error));

      fetch("./api/api.php?nome=ventoinha")
        .then(response => response.text())
        .then(data => {
          var fanImage = document.getElementById('ventoinha');
          if (data == "0") fanImage.classList.remove('ocupado')
          else if (data == "1") fanImage.classList.add('ocupado')
        })
        .catch(error => console.error(error));

      fetch("./api/api.php?nome=iluminacao")
        .then(response => response.text())
        .then(data => {
          var b0 = document.getElementById('butao_iluminacao_0');
          var b1 = document.getElementById('butao_iluminacao_1');
          var b2 = document.getElementById('butao_iluminacao_2');
          switch (data) {
            case "0":
              b0.disabled = true;
              b1.disabled = false;
              b2.disabled = false;
              break;
            case "1":
              b0.disabled = false;
              b1.disabled = true;
              b2.disabled = false;
              break;
            case "2":
              b0.disabled = false;
              b1.disabled = false;
              b2.disabled = true;
              break;
          }
        })
        .catch(error => console.error(error));

      fetch("./api/api.php?nome=portas")
        .then(response => response.text())
        .then(data => {
          var imagem = document.getElementById("imagem_portas");
          var butao = document.getElementById('butao_portas');
          if (data == "0") {
            imagem.src = "imagens/abrir_portas.png";
            butao.classList.remove('btn-danger');
            butao.classList.add('btn-success');
            butao.innerHTML = "Abrir";
            butao.onclick = function() {
              togglePortas(1);
            }
          } else if (data == "1") {
            imagem.src = "imagens/fechar_portas.png";
            butao.classList.remove('btn-success');
            butao.classList.add('btn-danger');
            butao.innerHTML = "Fechar";
            butao.onclick = function() {
              togglePortas(0);
            }
          }
        })
        .catch(error => console.error(error));
    }
  </script>

</body>

</html>