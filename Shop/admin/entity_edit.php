<?php
    session_start();

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

    $values = array(
        "action" => "post",
        "id" => "",
        "type" => "",
        "profile" => "",
        "num_of_cam" => "",
        "glass_unit" => "",
        "fittings" => "",
        "colour" => "",
        "price" => "",
        "category" => ""
    );

    if (array_key_exists("id", $params)) {
        $values["action"] = "edit_entity";

        $id = $params["id"];


        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $mysqli = new mysqli('localhost', 'root', 'semko777', 'ShopDB');
        $result = $mysqli->prepare("SELECT w.id AS id, w.type AS type, p.name AS profile, num_of_cam, glass_unit, f.name AS fittings, colour, price, CONCAT('img/', i.name) AS image_path, c.name AS category FROM `window` w JOIN profile p ON profile_id = p.id JOIN fittings f ON fittings_id = f.id JOIN category c ON category_id = c.id JOIN window_image wi ON w.id = wi.window_id JOIN image i ON wi.image_id = i.id WHERE w.id = ? ORDER BY w.id;");
        $result->bind_param('i', $id);
        $result->execute();
        $properties = $result->get_result()->fetch_array();

        // $result = pg_query_params($dbconnection, "SELECT t.name AS name, vendor_code, c.name AS color_name, mf.name AS vendor, ts.width AS width, ts.height AS height, ts.thickness AS thickness, t.rub_price AS price, CONCAT('img/', i.name) AS image_path FROM tile t JOIN color c ON color_id = c.id JOIN tile_size ts ON ts.id = tile_size_id JOIN tile_image ti ON t.id = ti.tile_id JOIN manufacturer mf ON mf.id = t.manufacturer_id JOIN image i ON ti.image_id = i.id WHERE t.id = $1 LIMIT 1", array($id));
        // $properties = pg_fetch_assoc($result);
        // pg_free_result($result);

        $values["id"] = $id;
        $values["type"] = $properties["type"];
        $values["profile"] = $properties["profile"];
        $values["num_of_cam"] = $properties["num_of_cam"];
        $values["glass_unit"] = $properties["glass_unit"];
        $values["fittings"] = $properties["fittings"];
        $values["colour"] = $properties["colour"];
        $values["price"] = $properties["price"];
        $values["category"] = $properties["category"];
    }

    $template_engine = new TemplateEngine();
    $content = file_get_contents("html/entity_edit.html");

    $template_engine->view($content, $values);
?>