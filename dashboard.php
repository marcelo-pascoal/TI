<?php
/*Verifica se foi inicializada a variavel de sessao username*/
session_start();
if (!isset($_SESSION['username'])) {
  header("refresh:1;url=index.php");
  die("Acesso restrito.");
}
/*Verifica se existe a variavel veiculo e se esta é válida*/
if (!isset($_GET['veiculo']) || !is_dir("api/files/" . $_GET['veiculo'])) {
  if (trim($_SESSION['role']) === 'Admin') {
    header("Location: admin.php");
  } else {
    header("Location: dashboard.php?veiculo=" . $_SESSION['role']);
  }
  exit;
}
if (trim($_SESSION['role']) !== 'Admin' && trim($_SESSION['role']) !== $_GET['veiculo']) {
  header("Location: dashboard.php?veiculo=" . $_SESSION['role']);
}
$veiculo = $_GET['veiculo'];
//pedido à API pelo array de lugares para construção do dashboard, esta informação (array de lugares) é trocada usando o formato JSON
$url = 'http://127.0.0.1/projeto';
//$url = 'https://iot.dei.estg.ipleiria.pt/ti/g168';
$data = file_get_contents($url . '/api/api.php?valor=lugares&veiculo=' . $veiculo);
$lugares = json_decode($data, true);
$codigoPorta = 9;
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
      <a class="navbar-brand" href="#">SmartDrive</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto">
          <?php
          if (trim($_SESSION['role']) === 'Admin') {
            echo '<li class="nav-item">';
            echo '  <a class="nav-link" href="admin.php">Administração </a>';
            echo '</li>';
          }
          ?>
          <li class="nav-item active">
            <a class="nav-link"><?php echo $veiculo ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="historico.php?veiculo=<?php echo $_GET['veiculo'] ?>">Histórico</a>
          </li>
        </ul>
        <span class="navbar-text">
          user: <b><?php echo $_SESSION['username'] ?></b> &nbsp;
        </span>
        <form class="d-flex" action="api/logout.php" method="post">
          <button class="btn btn-outline-success" type="submit">Logout</button>
        </form>
      </div>
    </div>
  </nav>
  <br>
  <div class="container">
    <div class="row text-center">
      <div class="col-sm-2 flex-column justify-content-around align-content-center">
        <!-- SENSOR DE TEMPERATURA -->
        <div class="card">
          <div class="card-header">
            <h6>Temperatura</h6>
          </div>
          <div class="card-body">
            <h1 id="temperatura">0</h1>
          </div>
          <div class="card-footer">
            <h6><a href="historico.php?veiculo=<?php echo $veiculo ?>&amp;nome=temperatura">Histórico</a></h6>
          </div>
        </div>
        <br>
        <!-- SENSOR DE HUMIDADE -->
        <div class="card ">
          <div class="card-header">
            <h6>Humidade</h6>
          </div>
          <div class="card-body">
            <h1 id="humidade">0</h1>
          </div>
          <div class="card-footer">
            <h6><a href="historico.php?veiculo=<?php echo $veiculo ?>&amp;nome=humidade">Histórico</a></h6>
          </div>
        </div>
        <hr>
        <!-- CONTROLADOR DE ATUADOR DE VENTOINHA -->
        <div class="card">
          <div class="card-header">
            <h6>Ventilação</h6>
          </div>
          <div class="ventoinha d-flex ">
            <!--O controlo de ligamento/desligamento da ventoinha deverá ser desenvolvido usando um controlador externo
                Este campo apenas permite configurar os parametros para controlo
                Caso o estado do atuador da ventoinha seja alterado, a imagem é atualizada de correspondedn ao estado atual-->
            <img alt="" src="api/imagens/fan.png " width="100" id="ventoinha">
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
                <img alt="" src="api/imagens/temperature-high.png " width="30">
              </div>
              <div class="col-sm-3">
                <input type="text" style="width: 30px;" id="controlo_temperatura">
              </div>
              <div class="col-sm-3">
                <img alt="" src="api/imagens/humidity-high.png " width="30">
              </div>
              <div class="col-sm-3">
                <input type="text" style="width: 30px;" id="controlo_humidade">
              </div>
              <div class="col-sm-1">
              </div>
            </div>
            <hr>
            <h6><a href="historico.php?veiculo=<?php echo $veiculo ?>&amp;nome=ventoinha">Histórico</a></h6>
          </div>
        </div>
      </div>
      <div class="col-sm-6  flex-column justify-content-around align-content-center">
        <!-- Monitorização de Sensores de Lugares  -->
        <div class="card">
          <div class="card-header">
            <h4>Lugares</h4>
            <hr>
            <div class="row">
              <!-- Contadores de lugares ocupados/disponíveis/lotação-->
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

            <table class="table">
              <tbody>
                <?php
                //variaveis X Y que representam as posições no array $lugares
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
                      echo ' porta"><img alt="" class="estado_porta" src="' . $url . '/api/imagens/porta.png" height="80">';
                    } else {
                      //é um lugar
                      //Link diferenciado para o histórico do sensor
                      echo ' lugar"><a href="historico.php?veiculo=<?php echo $veiculo ?>&amp;nome=lugar-' . $X . "-" . $Y . '"><img alt="" class="';
                      //classe CSS para rotação da imagem (norte não necessita de rotação)
                      switch (abs($posicao)) {
                        case 2:
                          echo "oeste";
                          break;
                        case 3:
                          echo "sul";
                          break;
                      }
                      //id no formato "posicao-X-Y" para monitorização independete do estado de cada sensor de lugar
                      echo '" id="posicao-' . $X . '-' . $Y . '" src="' . $url . '/api/imagens/lugar.png" height="80"></a>';
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
      <div class="col-sm-4  flex-column justify-content-around align-content-center">
        <div class="card">
          <div class="card-header">
            <!--webcam-->
            <h4>Webcam</h4>
          </div>
          <div class="card-body">
            <?php
            echo "<img id='webcam_image' src='api/files/" . $veiculo . "/webcam.jpg?id=" . time() . "' style='width:100%' >";
            ?>
            <!--img id="webcam1" alt="" src="api/imagens/portas_abertas.png" width="100" id="imagem_portas"-->
          </div>
        </div>
        <br>
        <div class=" atuadores">
          <!--Atuador de Iluminação
              apresenta os modos de iluminação disponíveis
            para cada modo existe um butão com a identificação distinta para o mesmo ser desativado de acordo com o modo atual (js)
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
              <h6><a href="historico.php?veiculo=<?php echo $veiculo ?>&amp;nome=iluminacao">Histórico</a></h6>
            </div>
          </div>
          <!--Atuador de Porta
              apresenta e alterna o estado das portas usando a função togglePortas(), o arguemento a enviar e definido ao atualizar os daos
            a informacao e atualizada usando os identificadores estado_portas ,imagem_portas e butao_portas-->
          <div class="card col-sm-5">
            <div class="card-header">
              <h4>Portas</h4>
            </div>
            <div class="card-body">
              <div class="justify-content-around align-content-center" style="height: 35vh;">
                <h4 id="estado_portas">Fechadas</h4>
                <img alt="" src="api/imagens/portas_abertas.png" width="100" id="imagem_portas">
                <button type="button" class="btn btn-success" onclick="togglePortas(1);" id="butao_portas">Abrir</button>
              </div>
            </div>
            <div class="card-footer">
              <h6><a href="historico.php?veiculo=<?php echo $veiculo ?>&amp;nome=portas">Histórico</a></h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>

  <script>
    // uso da função setInterval() para atualizacao dos componentes da dashboard


    const searchParams = new URLSearchParams(window.location.search);
    var veiculo = searchParams.get('role');;
    setVeiculo();
    const intervalSensores = setInterval(updateEstados, 2000);

    updateEstados();
    resetControlador();

    function setVeiculo() {
      this.veiculo = searchParams.get('veiculo');
    }

    //função fornecida pelos docentes para criação de timestamp
    function getHora() {
      var dateISO = new Date().toISOString();
      var data0 = dateISO.split('T')[0];
      var data1 = dateISO.split('T')[1];
      var time = data1.split('.')[0];
      var datahora = data0 + " " + time;
      return datahora;
    }

    //POST - função para alterar o valor da iluminação, recebe como argumento o novo estado do atuador
    function toggleIluminacao(valor) {
      const data = new URLSearchParams({
        nome: "iluminacao",
        veiculo: veiculo,
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

    //POST - função para abertura/fecho de portas, recebe como argumento o novo estado do atuador
    function togglePortas(valor) {
      const data = new URLSearchParams({
        nome: "portas",
        veiculo: veiculo,
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
        veiculo: veiculo,
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
      fetch("./api/api.php?valor=controlador&veiculo=" + veiculo)
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

    //Atualiza a dashboard fazendo pedidos (GET) da informação de todos o ssensores e atuadores à API
    function updateEstados() {

      //Sensor Temperatura - atualiza o campo de texto com o valor da temperatura
      fetch("./api/api.php?valor=temperatura&veiculo=" + veiculo)
        .then(response => response.text())
        .then(data => document.getElementById("temperatura").innerHTML = data + "ºC")
        .catch(error => console.error(error));

      //Sensor Humidade - atualiza o campo de texto com o valor da humidade
      fetch("./api/api.php?valor=humidade&veiculo=" + veiculo)
        .then(response => response.text())
        .then(data => document.getElementById("humidade").innerHTML = data + "%")
        .catch(error => console.error(error));

      //Controlador Ventoinha - adiciona ou remove a class 'ocupado' à imagem da ventoinha
      fetch("./api/api.php?valor=ventoinha&veiculo=" + veiculo)
        .then(response => response.text())
        .then(data => {
          var fanImage = document.getElementById('ventoinha');
          if (data == "1") fanImage.classList.remove('ocupado')
          else if (data == "0") fanImage.classList.add('ocupado')
        })
        .catch(error => console.error(error));

      //Atuador Iluminacao - altera a cor de fundo (já associado às celulas da tabela de lugares) de acordo com o nivel de luminusidade
      //                   - coloca o butão correspondente como desativo, e os restantes como ativos
      fetch("./api/api.php?valor=iluminacao&veiculo=" + veiculo)
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

      //Atuador Portas - altera o atribulo "src" da imgem com o id "imagem_portas" (fechadas / abertas)
      //               - altera a classe do butão de controlo de portas (success / danger)
      //               - adiciona ou remove a class 'visible' à imagem da porta na tabela de lugares
      fetch("./api/api.php?valor=portas&veiculo=" + veiculo)
        .then(response => response.text())
        .then(data => {
          const styleSheet = document.styleSheets[0];
          const ruleIndex = Array.from(styleSheet.cssRules).findIndex(rule => rule.selectorText === ".estado_porta");
          var imagem = document.getElementById("imagem_portas");
          var butao = document.getElementById('butao_portas');

          if (data == "2") {
            imagem.src = "api/imagens/portas_fechadas.png";
            butao.classList.remove('btn-danger');
            butao.classList.remove('btn-warning');
            butao.classList.add('btn-success');
            butao.innerHTML = "Abrir";
            (styleSheet.cssRules[ruleIndex]).style.setProperty("visibility", "visible");
            document.getElementById("estado_portas").innerHTML = "Fechadas";
            butao.onclick = function() {
              togglePortas(0);
            }
          } else if (data == "1") {
            imagem.src = "api/imagens/portas_fechar.png";
            butao.classList.remove('btn-success');
            butao.classList.remove('btn-danger');
            butao.classList.add('btn-warning');
            butao.innerHTML = "Cancelar";
            (styleSheet.cssRules[ruleIndex]).style.setProperty("visibility", "hidden");
            document.getElementById("estado_portas").innerHTML = "A fechar";
            butao.onclick = function() {
              togglePortas(0);
            }
          } else if (data == "0") {
            imagem.src = "api/imagens/portas_abertas.png";
            butao.classList.remove('btn-success');
            butao.classList.remove('btn-warning');
            butao.classList.add('btn-danger');
            butao.innerHTML = "Fechar";
            (styleSheet.cssRules[ruleIndex]).style.setProperty("visibility", "hidden");
            document.getElementById("estado_portas").innerHTML = "Abertas";
            butao.onclick = function() {
              togglePortas(1);
            }
          }
        })
        .catch(error => console.error(error));

      //atualiza camera
      var img = document.getElementById('webcam_image');
      var currentTime = new Date().getTime();
      img.src = "api/files/<?php echo $veiculo; ?>/webcam.jpg?id=" + currentTime;

      //Atualização dos lugares ocupados
      //  - volta a pedir o array de lugares à API, para os lugares apenas é tido em conta o sinal de cada posição
      fetch("./api/api.php?valor=lugares&veiculo=" + veiculo)
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
              //identificador único de lugar
              elementId = 'posicao-' + X + '-' + Y;
              if (lugar < 0) {
                // avalia se os lugares estão ocupados (valor negativo) para aribuir a classe correspondente
                lugaresOcupados++;
                document.getElementById(elementId).classList.add("ocupado");
              } else if (lugar != 0 && lugar != 9) {
                // avalia se não é um espaço vazio ou uma porta para remover a classe correspondente
                lugaresLivres++;
                document.getElementById(elementId).classList.remove('ocupado');
              }
              Y++
            })
            X++
          });
          //atualiza a informaçao dos contadores de lugares ocupados/disponiveis/lotação
          document.getElementById("lugares_livres").innerHTML = lugaresLivres;
          document.getElementById("lugares_ocupados").innerHTML = lugaresOcupados;
          document.getElementById("lugares_total").innerHTML = lugaresLivres + lugaresOcupados;
        })
        .catch(error => console.error(error));
    }
  </script>

</body>

</html>