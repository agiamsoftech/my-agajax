<?php
include_once dirname(__FILE__)."/db_constants.php";
include_once dirname(__FILE__)."/DbFunctions.php";
class DBMain {
    public $partner;
    
    public function __construct() {
        try {
            $this->partner = new DbFunctions(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            
        } catch (Exception $e) {
            echo json_encode(array("error", "Error in Database connection !"));
            die();
        }
    }
    public function __destruct() {
        unset($this->partner);
    }
}
?>