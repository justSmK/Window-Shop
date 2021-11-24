<?php
    session_start();

    require_once "../database.php";
    require_once "../template_engine.php";
    require_once "../util.php";

    $util = new Util();

    if (empty($_SESSION["logged_in"])) {
        $util->redirect("login");
    }

    $full_url = $_SERVER["REQUEST_URI"];
    $url = strtok($full_url, '?');
    $query = parse_url($full_url, PHP_URL_QUERY);

    parse_str($query, $params);

    if (!isset($params["path"])) {
        echo "Not found";
        exit;
    }

    $path = $params["path"];

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli('localhost', 'root', 'semko777', 'ShopDB');
    $smt = $mysqli->prepare("SELECT path, data, mime_type  FROM `page` WHERE path = ?");
    $smt->bind_param('s', $path);
    $smt->execute();
    $result = $smt->get_result();
    $values = $result->fetch_array();

    if (is_bool($values) ) {
        echo "Not found";
        exit;
    }

    $template_engine = new TemplateEngine();
    $content = file_get_contents("html/page_edit.html");

    $template_engine->view($content, $values);
?>