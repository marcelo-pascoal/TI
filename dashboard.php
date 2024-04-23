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

  <?php
  /*Verifica se foi inicializada a variavel de sessao username*/
  session_start();
  if (!isset($_SESSION['username'])) {
    header("refresh:5;url=index.php");
    die("Acesso restrito.");
  }
  /* configuração inicial de lugares
     é usado um array para comunição do sensor de todos os lugares que
     casas negativas são lugares ocupados
     casas positicas são lugares livres
     o valor numero é usado para representar a orientação da cadeira
     0 = vazio/passagem
     9 = porta*/

  /*configuração inicial de lugares
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
  //$url = 'https://iot.dei.estg.ipleiria.pt/ti/g168';
  $apiUrl = $url . '/api/api.php';
  $params = ['nome' => 'lugares'];
  $urlWithParams = $apiUrl . '?' . http_build_query($params);
  $data = file_get_contents($urlWithParams);

  //array de lugares para construção do dashboard
  $lugares = json_decode($data, true);
  $codigoPorta = 9;
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
            <h1 id="temperatura">0</h1>
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
            <h1 id="humidade">0</h1>
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
          <div class="ventoinha d-flex ">
            <!--O constrolo de ligamento/desligamento da ventoinha deverá ser desenvolvido 
                Este campo apenas permite configurar os parametros para controlo externo
                Caso o estado do atuador da ventoinha seja alterado, a imagem é sinalizada de acordo com o estado atual-->
            <img alt="" src="imagens/fan.png " width="100" id="ventoinha">
            <!-- envio de pedido post com as configurações -->
            <div class=" flex-column justify-content-around align-content-center">
              <button type="button" class="btn btn-warning btn-sm" onclick="resetControlador();">RESET</button>
              <br>
              <button type="button" class="btn btn-success btn-sm" onclick="saveControlador();">SAVE</button>
            </div>
          </div>
          <div class="card-footer">
            <!-- valores a serem usados para atualizar o controlador -->
            <div class="d-flex">
              <div class="col-sm-3">
                <img alt="" src="imagens/temperature-high.png " width="30">
              </div>
              <div class="col-sm-3">
                <input type="text" style="width: 30px;" id="controlo_temperatura">
              </div>
              <div class="col-sm-3">
                <img alt="" src="imagens/humidity-high.png " width="30">
              </div>
              <div class="col-sm-3">
                <input type="text" style="width: 30px;" id="controlo_humidade">
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
            <hr>
            <div class="row">
              <!--Contadoes de lugares ocupados/disponíveis-->
              <div class="col-sm-4">
                <h6>Disponiveis</h6>
                <h6 id="lugares_livres">0</h6>
              </div>
              <div class="col-sm-4">
                <h6>Ocupados</h6>
                <h6 id="lugares_ocupados">0</h6>
              </div>
              <div class="col-sm-4">
                <h6>Lotação</h6>
                <h6 id="lugares_total">0</h6>
              </div>
            </div>
          </div>
          <div class="card-body">
            <!-- tabela de lugares
                  constroi a tabela de lugares interpretando o array $lugares-->
            <table class="table">
              <tbody>
                <?php
                $X = 0;
                foreach ($lugares as $linha) {
                  $Y = 0;
                  echo "<tr>";
                  foreach ($linha as $posicao) {
                    echo '<td class="posicao';
                    if ($posicao == 0) {
                      //caso seja um espaço vazio
                      echo ' vazio">';
                    } else if ($posicao == $codigoPorta) {
                      //caso seja uma porta
                      echo ' porta"><img alt="" class="porta" src="' . $url . '/imagens/porta.png" height="80">';
                    } else {
                      //é um lugar
                      //Link diferenciado para os historico do sensor
                      echo ' lugar"><a href="historico.php?nome=lugar-' . $X . "-" . $Y . '"><img alt="" class="';
                      //classe CSS para rotação da imagem
                      switch (abs($posicao)) {
                        case 2:
                          echo "oeste";
                          break;
                        case 3:
                          echo "sul";
                          break;
                      }
                      //id no formato posicao-X-Y para monitorização independete do estado de cada sensor de lugar
                      echo '" id="posicao-' . $X . '-' . $Y . '" src="' . $url . '/imagens/lugar.png" height="80"></a>';
                    }
                    echo '</td>
                    ';
                    $Y++;
                  }
                  echo "</tr>";
                  $X++;
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
        <img alt="" src="imagens/webcam.jpg" style="width: 100%;">
        <hr>
        <div class=" atuadores">
          <!--Atuador de Iluminação
              apresenta os modos de iluminação disponíveis
            para cada modo existe um butao com a identificacao distinta para o mesmo ser desativado de acordo com o modo atual(js)
          a funcao toggleIluminacao() e chamada com parametros distintos quando e acionado o evento onclick em cada butao-->
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
              apresenta e alterna o estado das portas
            a informacao e atualizada usando os identificadores estado_portas ,imagem_portas e butao_portas-->
          <div class="card col-sm-5">
            <div class="card-header">
              <h4>Portas</h4>
            </div>
            <div class="card-body">
              <div class="justify-content-around align-content-center" style="height: 35vh;">
                <h4 id="estado_portas">Fechadas</h4>
                <img alt="" src="imagens/abrir_portas.png" width="100" id="imagem_portas">
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

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script>
    // uso da funcao setInterval() para atualizacao dos componentes da dashboard
    const intervalSensores = setInterval(updateEstados, 2000);
    updateEstados();
    resetControlador();

    //função fornecida para criação de timestamp
    function getHora() {
      var dateISO = new Date().toISOString();
      var data0 = dateISO.split('T')[0];
      var data1 = dateISO.split('T')[1];
      var time = data1.split('.')[0];
      var datahora = data0 + " " + time;
      return datahora;
    }

    //funçao para alterar o valor da iluminação, recebe como argumento o novo estado do atuador
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

    //funçao para abertura/fecho de portas, recebe como argumento o novo estado do atuador
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

    //POST - funçao para atualização dos parametros para o controlador da ventoinha
    function saveControlador() {
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
    }

    //GET - Atualiza os valores do controlador da ventoinha a partir da API
    function resetControlador() {
      fetch("./api/api.php?nome=controlador")
        .then(response => response.text())
        .then(data => {
          var inputTemperatura = document.getElementById("controlo_temperatura");
          var inputHumidade = document.getElementById("controlo_humidade");
          valores_controlador = data.split(";");
          inputTemperatura.value = valores_controlador[0];
          inputHumidade.value = valores_controlador[1];

        })
        .catch(error => console.error(error));
    }

    //Atualiza a dashboard pedindo toda a informação à API
    function updateEstados() {
      //Sensor Temperatura
      fetch("./api/api.php?nome=temperatura")
        .then(response => response.text())
        .then(data => document.getElementById("temperatura").innerHTML = data + "ºC")
        .catch(error => console.error(error));

      //Sensor Humidade
      fetch("./api/api.php?nome=humidade")
        .then(response => response.text())
        .then(data => document.getElementById("humidade").innerHTML = data + "%")
        .catch(error => console.error(error));

      //Controlador Ventoinha
      fetch("./api/api.php?nome=ventoinha")
        .then(response => response.text())
        .then(data => {
          var fanImage = document.getElementById('ventoinha');
          if (data == "1") fanImage.classList.remove('ocupado')
          else if (data == "0") fanImage.classList.add('ocupado')
        })
        .catch(error => console.error(error));

      //Atuador Iluminacao
      fetch("./api/api.php?nome=iluminacao")
        .then(response => response.text())
        .then(data => {
          const styleSheet = document.styleSheets[0];
          const ruleIndex = Array.from(styleSheet.cssRules).findIndex(rule => rule.selectorText === ".posicao");
          var b0 = document.getElementById('butao_iluminacao_0');
          var b1 = document.getElementById('butao_iluminacao_1');
          var b2 = document.getElementById('butao_iluminacao_2');
          switch (data) {
            case "0":
              (styleSheet.cssRules[ruleIndex]).style.setProperty("background-color", "rgba(255, 255, 0, 0)", "important");
              b0.disabled = true;
              b1.disabled = false;
              b2.disabled = false;
              break;
            case "1":
              (styleSheet.cssRules[ruleIndex]).style.setProperty("background-color", "rgba(255, 255, 0, 0.20)", "important");
              b0.disabled = false;
              b1.disabled = true;
              b2.disabled = false;
              break;
            case "2":
              (styleSheet.cssRules[ruleIndex]).style.setProperty("background-color", "rgba(255, 255, 0, 0.40)", "important");
              b0.disabled = false;
              b1.disabled = false;
              b2.disabled = true;
              break;
          }
        })
        .catch(error => console.error(error));

      //Atuador Portas
      fetch("./api/api.php?nome=portas")
        .then(response => response.text())
        .then(data => {
          const styleSheet = document.styleSheets[0];
          const ruleIndex = Array.from(styleSheet.cssRules).findIndex(rule => rule.selectorText === ".porta");
          var imagem = document.getElementById("imagem_portas");
          var butao = document.getElementById('butao_portas');
          if (data == "0") {
            imagem.src = "imagens/abrir_portas.png";
            butao.classList.remove('btn-danger');
            butao.classList.add('btn-success');
            butao.innerHTML = "Abrir";
            (styleSheet.cssRules[ruleIndex]).style.setProperty("visibility", "visible");
            butao.onclick = function() {
              togglePortas(1);
            }
          } else if (data == "1") {
            imagem.src = "imagens/fechar_portas.png";
            butao.classList.remove('btn-success');
            butao.classList.add('btn-danger');
            butao.innerHTML = "Fechar";
            (styleSheet.cssRules[ruleIndex]).style.setProperty("visibility", "hidden");
            butao.onclick = function() {
              togglePortas(0);
            }
          }
        })
        .catch(error => console.error(error));

      /*Atualização dos lugares ocupados
      volta a pedir o array de lugares à API 
      avalia se os lugares estão ocupados para aribuir/remover a classe correspondente
      */
      fetch("./api/api.php?nome=lugares")
        .then(response => response.text())
        .then(data => {
          const lugares = JSON.parse(data);
          var lugaresOcupados = 0;
          var lugaresLivres = 0;
          var elementId = ""
          var X = Y = 0;
          lugares.forEach(function(linha) {
            Y = 0;
            linha.forEach(function(lugar) {
              //identificador único
              elementId = 'posicao-' + X + '-' + Y;
              if (lugar < 0) {
                lugaresOcupados++;
                document.getElementById(elementId).classList.add("ocupado");
              } else if (lugar != 0 && lugar != 9) {
                lugaresLivres++;
                document.getElementById(elementId).classList.remove('ocupado');
              }
              Y++
            })
            X++
          });
          //atualiza a informaçao dos contadores de ligares ocupados/disponiveis
          document.getElementById("lugares_livres").innerHTML = lugaresLivres;
          document.getElementById("lugares_ocupados").innerHTML = lugaresOcupados;
          document.getElementById("lugares_total").innerHTML = lugaresLivres + lugaresOcupados;
        })
        .catch(error => console.error(error));
    }
  </script>

</body>

</html>