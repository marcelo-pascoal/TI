<?php
#php usado para terminar a sessão do utilizador
session_start();
session_unset();
session_destroy();
header("refresh:1;url=../index.php");
