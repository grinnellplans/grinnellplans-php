<?php
require_once ('Plans.php');
/*
*Connects to the Database and returns the database handler.
*Establishes a persistant connection.
*/
function db_connect() {
    $dbh = @mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
    if (!$dbh) {
        print "Something's wrong with the database. You could report the error to grinnellplans@gmail.com. <br/><pre>" . mysqli_error($dbh) . "</pre>";
        exit;
    }
    $GLOBALS['dbh'] = $dbh;
    @mysqli_query($dbh, "SET NAMES 'utf8mb4';");
    return $dbh;
}
/*
*Given the database handler, closes the connection to the database.
*/
function db_disconnect($dbh) {
    mysqli_close($dbh);
}
/*
*Adds a row to a table in the database.
*Takes the row as an array that represent the different columns.
*/
function add_row($dbh, $table, $row) {
    while (list($key, $items) = each($row)) {
        $row[$key] = "\"" . addslashes($items) . "\"";
    }
    $joined_row = join(',', $row);
    if (!mysqli_query($dbh, "INSERT INTO $table VALUES($joined_row)")) {
        echo "Error adding entry to $table";
        echo $joined_row . "<br />";
        mysqli_close($dbh);
        exit();
    }
}
/*
*Gets a single item from the database. Returns a single item.
*/
function get_item($dbh, $get_column, $table, $search_column, $search_item) {
    $search_item = mysqli_real_escape_string($dbh,$search_item);
    $my_result = mysqli_query($dbh,"Select $get_column From $table where
	$search_column = '$search_item'");
    $my_row = mysqli_fetch_array($my_result);
    if (NULL !== $my_row) {
        return $my_row[0];
    } else {
        return NULL;
    }
}
/*
*Returns multiple items from a database.
*Returns an 2-d array. The first index represents rows in
*the database. The second represents columns in the database.
*/
function get_items($dbh, $get_column, $table, $search_column, $search_item) {
    $all = array();
    $search_item = mysqli_real_escape_string($dbh,$search_item);
    $my_result = mysqli_query($dbh,"Select $get_column From $table where
	$search_column = '$search_item'");
    while ($new_row = mysqli_fetch_row($my_result)) {
        $all[] = $new_row;
    }
    return $all;
}
/*
*Removes a row from the database.
*/
function delete_item($dbh, $table, $search_column, $search_item) {
    $search_item = mysqli_real_escape_string($dbh,$search_item);
    mysqli_query($dbh,"DELETE FROM $table WHERE
	$search_column = '$search_item'");
}
/*
*Changes an item in the database.
*/
function set_item($dbh, $dtable, $dcolumn_change, $dcolumn_value, $dsearch_column, $dsearch_item) {
    $dcolumn_value = mysqli_real_escape_string($dbh,$dcolumn_value);
    $dsearch_item = mysqli_real_escape_string($dbh,$dsearch_item);
    mysqli_query($dbh,"UPDATE $dtable SET $dcolumn_change = '$dcolumn_value' WHERE
					  $dsearch_column = '$dsearch_item'");
}
/*
*Searches the database for a partial term and returns those parts
*that contain the term
*/
function partial_search($dbh, $get_column, $table, $search_column, $search_item, $orderby) {
    $all = array();
    $search_item = mysqli_real_escape_string($dbh,$search_item);
    $my_result = mysqli_query($dbh,"Select $get_column From $table where
							$search_column RLIKE '$search_item' ORDER by $orderby");
    while ($new_row = mysqli_fetch_row($my_result)) {
        $all[] = $new_row;
    }
    return $all;
}
?>
