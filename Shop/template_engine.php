<?php

require_once "database.php";

class TemplateEngine {
    public function view($code, $values) {
        ob_start();
        extract($values);
        eval('?>' . $code);
        echo ob_get_clean();
    }
}

?>