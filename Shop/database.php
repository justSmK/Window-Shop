<?php

interface DatabaseContext {
    public function getPage($path);
}

class MySqlDatabaseContext implements DatabaseContext {
    public $dbconnection;

    public function __construct($connection) {
        $this->dbconnection = $connection;
    }

    public function getPage($path) {
        $stnt = $this->dbconnection->prepare("SELECT * FROM `page` WHERE path = ?");
        $stnt->bind_param('s', $path);
        $stnt->execute();
        $result = $stnt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        if ($rows == null) {
            return null;
        }
        $data = $rows[0];

        $content_type = $data["mime_type"];

        header("Content-type: $content_type");

        return [
            "data" => $data["data"],
            "content_type" => $data["mime_type"]
        ];
    }

    public function getImage($path) {
        $stnt = $this->dbconnection->prepare("SELECT TO_BASE64(data) AS data FROM `image` WHERE name = ?");
        $stnt->bind_param('s', $path);
        $stnt->execute();
        $result = $stnt->get_result();

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $data = $rows[0]["data"];

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $content_type = '';
        if ($ext == "jpg") {
            $content_type = "image/jpeg";
        }
        else if ($ext == "png") {
            $content_type = "image/png";
        }

        return [
            "data" => base64_decode($data),
            "content_type" => $content_type
        ];
    }
}

?>