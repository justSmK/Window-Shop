<?php
    session_start();

    require_once "../database.php";
    require_once "../util.php";
    
    $util = new Util();

    function getUserHash($username) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $mysqli = new mysqli('localhost', 'root', 'semko777', 'ShopDB');

        $smt = $mysqli->prepare("SELECT password_hash FROM `app_admin` WHERE user_name = ?");
        $smt->bind_param('s', $username);
        $smt->execute();
        $result = $smt->get_result();
        $password_hash = $result->fetch_array()["password_hash"];
        
        return $password_hash;
    }

    $isLoggedIn = false;

    if (!empty($_SESSION["logged_in"])) {
        $isLoggedIn = true;
    }

    if (!empty($_POST["login"])) {
        $username = $_POST["login"];
        $password = $_POST["password"];

        $userHash = getUserHash($username);

        $isLoggedIn = password_verify($password, $userHash);

        if ($isLoggedIn) {
            $_SESSION["logged_in"] = "true";
        }
    }

    if ($isLoggedIn == true) {
        $util->redirect("panel");
        
    }
    else {
        readfile("html/login.html", true);
    } 
?>