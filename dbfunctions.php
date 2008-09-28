<?php
	require_once("Plans.php");


/*
 *Connects to the Database and returns the database handler.
 *Establishes a persistant connection.
 */

 function db_connect() {
	 $dbh = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
	 mysql_select_db(MYSQL_DB);
	echo mysql_error();
	 if (!$dbh)
	 {
		 print "Obviously, the above messages suggest that the database connection failed. It's not a bad idea to report this error to grinnellplans@gmail.com";
		 exit;
	 }
	 else
	 return $dbh;
 } 

/*
 *Given the database handler, closes the connection to the database.
 */

 function db_disconnect($dbh) {
	 mysql_close($dbh);
 }

/*
 *Adds a row to a table in the database.
 *Takes the row as an array that represent the different columns.
 */

function add_row($dbh, $table, $row)
{
	while(list ($key, $items) = each ($row))
	{$row[$key] = "\"" . addslashes($items) . "\"";
}
$joined_row = join(',', $row);
if (!mysql_query("INSERT INTO $table VALUES($joined_row)"))
{
	echo "Error adding entry to $table";
	#echo $joined_row . "<br />";
	mysql_close($dbh);
	exit();
}
}

/*
 *Gets a single item from the database. Returns a single item.
 */

function get_item($dbh,$get_column,$table,$search_column, $search_item)
{
	$search_item= addslashes($search_item);
	$my_result = mysql_query("Select $get_column From $table where
	$search_column = '$search_item'");

	$my_row = mysql_fetch_array($my_result);
	return $my_row[0];
}

/*
 *Returns multiple items from a database.
 *Returns an 2-d array. The first index represents rows in
 *the database. The second represents columns in the database.
 */

function get_items($dbh,$get_column,$table,$search_column, $search_item)
{
	$search_item= addslashes($search_item);
	$my_result = mysql_query("Select $get_column From $table where
	$search_column = '$search_item'");

	while($new_row = mysql_fetch_row($my_result)) {
		$all[] = $new_row;
	}
	return $all;
}

/*
 *Removes a row from the database.
 */

function delete_item($dbh, $table, $search_column, $search_item)
{
	$search_item = addslashes($search_item);
	mysql_query("DELETE FROM $table WHERE
	$search_column = '$search_item'");
}

/*
 *Changes an item in the database.
 */

function set_item($dbh, $dtable, $dcolumn_change, $dcolumn_value,
                  $dsearch_column, $dsearch_item)
				  {
					  $dcolumn_value = addslashes($dcolumn_value);
					  $dsearch_item = addslashes($dsearch_item);
					  mysql_query("UPDATE $dtable SET $dcolumn_change = '$dcolumn_value' WHERE
					  $dsearch_column = '$dsearch_item'");
				  }

/*
 *Gets rid of an entire table in the database.
 */

function annihilate($dbh, $table)
{
	if (!mysql_query("DROP TABLE $table"))
	{
		printf("Error is dropping $table");
		mysql_close($dbh);
		exit();
	}
}

/*
 *Adds a table to the database, with items being the creation values
 *for the table (i.e. TINYINT mynumber)
 */

function create_table($dbh, $table_name, $items)
{
	$joined_items = join(',', $items);
	if (!mysql_query("CREATE TABLE $table_name($joined_items)"))
	{printf("Error creating table $table_name with values $joined_items");}
}

/*
 *Searches the database for a partial term and returns those parts
 *that contain the term
 */

function partial_search($dbh,$get_column,$table,$search_column, 
                        $search_item,$orderby)
						{
							$search_item= addslashes($search_item);
							$my_result = mysql_query("Select $get_column From $table where
							$search_column RLIKE '$search_item' ORDER by $orderby");

							while($new_row = mysql_fetch_row($my_result)) {
								$all[] = $new_row;
							}
							return $all;
						}



?>
