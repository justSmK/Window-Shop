<?php

    session_start();

    require_once "../util.php";
    $util = new Util();

    $_SESSION["logged_in"] = "";
    session_destroy();

    $util->redirect("login");

?>