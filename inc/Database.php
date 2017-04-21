<?php
require_once ("Plans.php");
require_once ("Configuration.php");
class Database {
    private $handle;
    private $last_resource;
    private $error = FALSE;
    public function __construct() {
        $_error = false;
        $this->handle = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS,MYSQL_DB) or $_error = true;
        if ($_error) {
            $this->error = true;
            throw new Exception('Database connection failed');
        };
    }
    function __destruct() {
        mysqli_close($this->handle);
    }
    function query($query) {
        $this->lastresource = mysqli_query($this->handle, $query);
        if (($this->lastresource) == 0) {
            $this->error = true;
            throw new Exception("Query \"$query\" failed");
        }
        return $this->lastresource;
    }
    function value_from_query($query) {
        $tmp = mysqli_fetch_row($this->query($query));
        return $tmp[0];
    }
    function get_item($table, $column, $search_column, $search_item) {
        $search_item = mysqli_real_escape_string($this->handle,$search_item);
        return $this->value_from_query("select $column From $table where $search_column = '$search_item'");
    }
}
?>
