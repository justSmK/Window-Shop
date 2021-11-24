<?php 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', 'semko777', 'ShopDB');

require_once 'config.php';
require_once 'database.php';
require_once 'page_context.php';
require_once 'template_engine.php';

$db_context = new MySqlDatabaseContext($mysqli);

$full_url = $_SERVER["REQUEST_URI"];
$url = strtok($full_url, '?');
$query = parse_url($full_url, PHP_URL_QUERY);

parse_str($query, $params);

$uri_parts = explode('/', $_SERVER["REQUEST_URI"]);
// if (array_search("admin", $uri_parts)) {
//     include "admin.php";
// }
// else 
if (array_search("img", $uri_parts)) {
    $img = end($uri_parts);
    $data = $db_context->getImage($img);

    if ($data == null) {
        http_response_code(404);
        exit;
    }

    header("Content-type: {$data["content_type"]}");
    echo $data["data"];
}
else {
    $contexts = [
        "/Shop/" => new MainPageContext($db_context->dbconnection),
        "/Shop/about" => new AboutPageContext($db_context->dbconnection)
    ];

    $data = $db_context->getPage($url);

    if ($data == null) {
        http_response_code(404);
        exit;
    }

    $variables = [];
    if (array_key_exists($url, $contexts)) {
        $variables = $contexts[$url]->getVariables($params);
    }

    header("Content-type: {$data["content_type"]}");
    $template_engine = new TemplateEngine();
    $template_engine->view($data["data"], $variables);
}

?>