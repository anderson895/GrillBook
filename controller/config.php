<?php
if (!defined('db_host')) {
    define("db_host", "grillbook.online");
    define("db_user", "u777088444_grillbook");
    define("db_pass", "Grillbook123@");
    define("db_name", "u777088444_grillbook");
}
/**
 * 
 * $username = "u777088444_grillbook";
    $password = "Grillbook123@";
    $database = "u777088444_grillbook";
 */


if (!class_exists('db_connect')) {
    class db_connect
    {
        public $host = db_host;
        public $user = db_user;
        public $pass = db_pass;
        public $name = db_name;
        public $conn;
        public $error;
        public $mysqli;

        public function connect()
        {
            try {
                $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);

                if (!$this->conn) {
                    $this->error = "Fatal Error: Can't connect to database" . $this->conn->connect_error;
                    return false;
                }
            } catch (\Throwable $th) {
                header("Location:setup.php");
            }
        }
    }
}
?>