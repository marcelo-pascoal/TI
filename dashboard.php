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
      <a class="navbar-brand" href="#">Dashboard EI-TI</a>
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
  <div class="container d-flex justify-content-around align-items-center">
    <div id="title-header">
      <h1>Servidor IoT</h1>
      <h6>user: Alexandre Lopes</h6>
    </div>
    <img src="estg.png" width="300">
  </div>
  <hr>
  <div class="container">
    <div class="row text-center">
      <div class="col-sm-4">
        <div class="card">
          <div class="card-header sensor">
            <h4>Temperatura 40°</h4>
          </div>
          <div class="card-body">
            <img src="temperature-high.png">
          </div>
          <div class="card-footer">
            <h6><b>Atualização:</b> 2024/03/13 22:09 - <a href="temperatura.html">Histórico</a></h6>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card">
          <div class="card-header sensor">
            <h4>Humidade 70°</h4>
          </div>
          <div class="card-body">
            <img src="humidity-high.png">
          </div>
          <div class="card-footer">
            <h6><b>Atualização:</b> 2024/03/13 22:09 - <a href="humidade.html">Histórico</a></h6>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card">
          <div class="card-header atuador">
            <h4>Led Arduino: Ligado</h4>
          </div>
          <div class="card-body">
            <img src="light-on.png">
          </div>
          <div class="card-footer">
            <h6><b>Atualização:</b> 2024/03/13 22:09 - <a href="ledArduino.html">Histórico</a></h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <h4>Tabela de Sensores</h4>
      </div>
      <div class="card-body">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Tipo de Dispositivo IoT</th>
              <th scope="col">Valor</th>
              <th scope="col">Data de Avaliação</th>
              <th scope="col">Estado Alertas</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Temperatura</td>
              <td>40°</td>
              <td>2024/03/13 22:09</td>
              <td><button type="button" class="btn rounded-pill btn-danger btn-sm">Elevada</button></td>
            </tr>
            <tr>
              <td>Humidade</td>
              <td>70°</td>
              <td>2024/03/13 22:09</td>
              <td><button type="button" class="btn rounded-pill btn-info btn-sm">Normal</button></td>
            </tr>
            <tr>
              <td>Led Arduino</td>
              <td>Ligado</td>
              <td>2024/03/13 22:09</td>
              <td><button type="button" class="btn rounded-pill btn-primary btn-sm">Ligado</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>