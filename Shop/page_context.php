<?php

interface PageContext {
    public function getVariables($values = []);
}

class MainPageContext implements PageContext {
    private $db_connection;

    public function __construct($db_connection) {
        $this->db_connection = $db_connection;
    }

    private function getWindowItems() {
        $stnt = $this->db_connection->prepare("SELECT * FROM `window`");
        $stnt->execute();
        $result = $stnt->get_result();
        
        $items = [];

        foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
            $image_row = $this->db_connection->prepare("SELECT CONCAT('img/', i.name) AS path FROM window_image wi JOIN image i ON wi.image_id = i.id WHERE window_id = ? LIMIT 1");
            $image_row->bind_param('i', $row["id"]);
            $image_row->execute();
            $img_row_result = $image_row->get_result();

            $row["image_path"] = $img_row_result->fetch_array()["path"];
            array_push($items, $row);

        }

        return $items;
    }

    private function getCategoryWindowsItems($id) {
        $items = $this->db_connection->prepare("SELECT w.id AS id, w.type AS type, p.name AS profile, CONCAT('img/', i.name) AS image_path FROM `window` w JOIN profile p ON profile_id = p.id JOIN window_image wi ON w.id = wi.window_id JOIN image i ON wi.image_id = i.id WHERE category_id = ? ORDER BY w.id;");
        $items->bind_param('i', $id);
        $items->execute();
        $items_result = $items->get_result();
        $data = $items_result->fetch_all(MYSQLI_ASSOC);

        return $data;
    }

    public function getVariables($values = []) {
        return [
            "items" => $this->getWindowItems(),
            "category_1" => $this->getCategoryWindowsItems(1),
            "category_2" => $this->getCategoryWindowsItems(2),
            "category_3" => $this->getCategoryWindowsItems(3)
        ];
    }
}

class AboutPageContext implements PageContext {
    private $db_connection;

    public function __construct($db_connection) {
        $this->db_connection = $db_connection;
    }

    public function getVariables($values = []) {
        $row = $this->db_connection->prepare("SELECT w.id AS id, w.type AS type, p.name AS profile, num_of_cam, glass_unit, f.name AS fittings, colour, price, CONCAT('img/', i.name) AS image_path, c.name AS category FROM `window` w JOIN profile p ON profile_id = p.id JOIN fittings f ON fittings_id = f.id JOIN category c ON category_id = c.id JOIN window_image wi ON w.id = wi.window_id JOIN image i ON wi.image_id = i.id WHERE w.id = ? ORDER BY w.id;");
        $row->bind_param('i', $values["id"]);
        $row->execute();
        $row_result = $row->get_result();
        $data = $row_result->fetch_array();
    

        return [
            "item" => $data
        ];
    }
}

?>