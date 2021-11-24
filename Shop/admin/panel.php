<?php
    session_start();

    require_once "../database.php";
    require_once "../template_engine.php";
    require_once "../util.php";

    $util = new Util();

    if (empty($_SESSION["logged_in"])) {
        $util->redirect("login");
    }

    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $action = $_POST["action"];

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $mysqli = new mysqli('localhost', 'root', 'semko777', 'ShopDB');

            if ($action == "edit_page") {
                $data = $_POST["data"];
                $path = $_POST["path"];

                $smt = $mysqli->prepare("UPDATE `page` SET `data` = ? WHERE `path` = ?");
                $smt->bind_param('ss', $data, $path);
                $smt->execute();
            }
            else if ($action == "edit_entity") {
                $id = (int)$_POST["id"];
                $type = $_POST["type"];
                $profile = $_POST["profile"];
                $num_of_cam = $_POST["num_of_cam"];
                $glass_unit = $_POST["glass_unit"];
                $fittings = $_POST["fittings"];
                $colour = $_POST["colour"];
                $price = $_POST["price"];
                $category_id = (int)$_POST["category"];

                $window = $mysqli->prepare("SELECT * FROM `window` WHERE id = ?");
                $window->bind_param('i', $id);
                $window->execute();
                $arrayRow = $window->get_result()->fetch_array();

                $window_id = $id;
                $profile_id = (int)$arrayRow['profile_id'];
                $fittings_id = (int)$arrayRow['fittings_id'];


                $window = $mysqli->prepare("UPDATE `window` SET type=?, num_of_cam=?, glass_unit=?, colour=?, price=?, category_id=? WHERE id=?");
                $window->bind_param('sissiii', $type, $num_of_cam, $glass_unit, $colour, $price, $category_id, $id);
                $window->execute();

                $smt = $mysqli->prepare("UPDATE `profile` SET name=? WHERE id = ?");
                $smt->bind_param('si', $profile, $profile_id);
                $smt->execute();

                $smt = $mysqli->prepare("UPDATE `fittings` SET name=? WHERE id = ?");
                $smt->bind_param('si', $fittings, $fittings_id);
                $smt->execute();


                if (file_exists($_FILES["image"]["tmp_name"])) {
                    $image_path = $_FILES["image"]["name"];
                    $image_content = file_get_contents($_FILES["image"]["tmp_name"]);
                    $image_data = $mysqli->real_escape_string($image_content);
                    $query = "INSERT INTO `image` (name, data) VALUES (" . "'$image_path'" . "," . "'$image_data'" .")";
                    $mysqli->query($query);
                                    
                    $image_id = (int)$mysqli->query("SELECT MAX(id) AS mid FROM image")->fetch_array()['mid'];
                    
                    $smt = $mysqli->prepare("UPDATE window_image SET image_id=? WHERE window_id=?");
                    $smt->bind_param('ii', $image_id, $window_id);
                    $smt->execute();
                }
            }
            else if ($action == "delete") {
                $id = $_POST["id"];

                $smt = $mysqli->prepare("DELETE FROM `window` WHERE id = ?");
                $smt->bind_param('i', $id);
                $smt->execute();
            }
            else if ($action == "post" AND isset($_FILES["image"]["name"])) {
                $type = $_POST["type"];
                $profile = $_POST["profile"];
                $num_of_cam = $_POST["num_of_cam"];
                $glass_unit = $_POST["glass_unit"];
                $fittings = $_POST["fittings"];
                $colour = $_POST["colour"];
                $price = $_POST["price"];
                $category_id = (int)$_POST["category"];

                $image_path = $_FILES["image"]["name"];
                $image_content = file_get_contents($_FILES["image"]["tmp_name"]);
                $image_data = $mysqli->real_escape_string($image_content);
                $query = "INSERT INTO `image` (name, data) VALUES (" . "'$image_path'" . "," . "'$image_data'" .")";
                $mysqli->query($query);
                                
                $image_id = (int)$mysqli->query("SELECT MAX(id) AS mid FROM image")->fetch_array()['mid'];
                
                $smt = $mysqli->prepare("INSERT INTO `profile` (name) VALUES (?)");
                $smt->bind_param('s', $profile);
                $smt->execute();
                $profile_id = (int)$mysqli->query("SELECT MAX(id) AS mid FROM profile")->fetch_array()['mid'];

                $smt = $mysqli->prepare("INSERT INTO `fittings` (name) VALUES (?)");
                $smt->bind_param('s', $profile);
                $smt->execute();
                $fittings_id = (int)$mysqli->query("SELECT MAX(id) AS mid FROM fittings")->fetch_array()['mid'];
                
                $smt = $mysqli->prepare("INSERT INTO `window` (type, profile_id, num_of_cam, glass_unit, fittings_id, colour, price, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $smt->bind_param('ssssssss', $type, $profile_id, $num_of_cam, $glass_unit, $fittings_id, $colour, $price, $category_id);
                $smt->execute();
                $window_id = (int)$mysqli->query("SELECT MAX(id) AS mid FROM `window`")->fetch_array()['mid'];
                
                $smt = $mysqli->prepare("INSERT INTO `window_image` (window_id, image_id) VALUES (?, ?)");
                $smt->bind_param('ii', $window_id, $image_id);
                $smt->execute();

            }
        }
    }
    catch (Error $e) {
        function function_alert($message) {
            echo "<script>alert('$message');</script>";
        }
        function_alert("Ошибка при вводе данных");
        // echo "error occur";
    }
    catch (Exception $e) {
        function function_alert($message) {
            echo "<script>alert('$message');</script>";
        }
        function_alert("Исключение при вводе данных");
        // echo "exception occur";
    }

    $template_engine = new TemplateEngine();

    $content = file_get_contents("html/panel.html");

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli('localhost', 'root', 'semko777', 'ShopDB');

    $smt = $mysqli->prepare("SELECT w.id, w.type, c.name AS category FROM `window` w JOIN category c ON category_id = c.id ORDER BY id;");
    $smt->execute();
    $result = $smt->get_result();

    $entities = $result->fetch_all(MYSQLI_ASSOC);

    $smt = $mysqli->prepare("SELECT path, mime_type FROM `page`");
    $smt->execute();
    $result = $smt->get_result();
    $pages = $result->fetch_all(MYSQLI_ASSOC);

    $template_engine->view($content, [ 
        "entities" => $entities,
        "pages" => $pages
    ]);
?>