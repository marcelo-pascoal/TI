<?php
#php usado para terminar a sessão do utilizador
    session_start();
    session_unset();
    session_destroy();
    header( "refresh:0;url=index.php");
?> 