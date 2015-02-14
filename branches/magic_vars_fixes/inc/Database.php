<?php
require_once ("Plans.php");
require_once ("Configuration.php");
class Database {
    private $handle;
    private $last_resource;
    private $error = FALSE;
    public function Database() {
        $_error = false;
        $this->handle = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) or $_error = true;
        mysql_select_db(MYSQL_DB, $this->handle) or $_error = true;
        /*		mysql_query("SET NAMES UTF8", $this->handle) or $_error = true;
        mysql_query("SET CHARACTER SET UTF8", $this->handle) or $_error = true; */
        if ($_error) {
            $this->error = true;
            throw new Exception('Database connection failed');
        };
    }
    function __destruct() {
        mysql_close($this->handle);
    }
    function query($query) {
        $this->lastresource = mysql_query($query, $this->handle);
        if (($this->lastresource) == 0) {
            $this->error = true;
            throw new Exception("Query \"$query\" failed");
        }
        return $this->lastresource;
    }
    function value_from_query($query) {
        $tmp = mysql_fetch_row($this->query($query));
        return $tmp[0];
    }
    function get_item($table, $column, $search_column, $search_item) {
        $search_item = addslashes($search_item);
        return $this->value_from_query("select $column From $table where $search_column = '$search_item'");
    }
}
?>