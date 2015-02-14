<?php
echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
echo "\n";
include 'inc-db.php';
include 'inc-error.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
  <head><title>Donations Made to Support Plans</title></head>
  <body>
    <h2>Donations Made to Support Plans</h2>
    <hr />

<?php
foreach($_POST as $varname => $value) $formVars[$varname] = $value;
// if (!($formVars["dbpass"])) {
//   $formVars["dbpass"] = "readonly";
//}
if (!($connection = @mysql_pconnect($hostName, $username, $password))) {
    showError();
    exit;
}
if (!(mysql_select_db($databaseName, $connection))) {
    showerror();
    exit;
}
if (!empty($_POST)) {
    if (!($formVars["date"])) {
        $formVars["date"] = "CURDATE()";
    }
    $insertQuery = "INSERT INTO donations (donor, amount, date, comment) values
                    ('" . $formVars["donor_name"] . "','
		    " . $formVars["amount_added"] . "','
		    " . $formVars["date"] . "','
		    " . $formVars["comments_added"] . "')";
    if (!(mysql_query($insertQuery, $connection))) {
        showerror();
    }
}
function p_print($text) {
    echo "<p>";
    echo "$text";
    echo "</p>";
}
function show_donations_table($conn) {
    echo "    <table border=\"2\">
      <tr>
	<th>Donor</th>
	<th>Amount ($)</th>
	<th>Date</th>
	<th>Comments</th>
      </tr>";
    $selectQuery = "SELECT donor, amount, DATE_FORMAT(date,'%m/%d/%Y'), comment FROM donations order by date desc, donor";
    if (!($infoz = mysql_query($selectQuery, $conn))) {
        showerror();
        exit;
    }
    if ($row = mysql_fetch_array($infoz)) {
        do {
            echo "<tr align=\"center\">\n\t<td>" . $row["donor"] . "</td>";
            echo "\n\t<td>" . $row["amount"] . "</td>";
            echo "\n\t<td>" . $row["DATE_FORMAT(date,'%m/%d/%Y')"] . "</td>";
            echo "\n\t<td>" . $row["comment"] . "</td>";
            echo "\n</tr>\n\n";
        }
        while ($row = mysql_fetch_array($infoz));
    }
    echo "</table>";
}
function show_expenses_table($conn) {
    echo "    <table border=\"2\">
      <tr>
	<th>Expense</th>
	<th>Amount ($)</th>
	<th>Date</th>
      </tr>";
    $selectQuery = "SELECT expense, amount, DATE_FORMAT(date,'%m/%d/%Y') FROM expenses order by date desc, expense";
    if (!($infoz = mysql_query($selectQuery, $conn))) {
        showerror();
        exit;
    }
    if ($row = mysql_fetch_array($infoz)) {
        do {
            echo "<tr align=\"center\">\n\t<td>" . $row["expense"] . "</td>";
            echo "\n\t<td>" . $row["amount"] . "</td>";
            echo "\n\t<td>" . $row["DATE_FORMAT(date,'%m/%d/%Y')"] . "</td>";
            echo "\n</tr>\n\n";
        }
        while ($row = mysql_fetch_array($infoz));
    }
    echo "</table>\n";
}
function get_table_total($conn, $tbl) {
    $sumquery = "SELECT SUM(amount) From $tbl";
    if (!($result = mysql_query($sumquery, $conn))) {
        showerror();
        exit;
    }
    $val = mysql_fetch_array($result);
    return $val[0];
}
function get_table_count($conn, $tbl) {
    $countquery = "SELECT count(*) From $tbl";
    if (!($result = mysql_query($countquery, $conn))) {
        showerror();
        exit;
    }
    $val = mysql_fetch_array($result);
    return $val[0];
}
function show_statistics($conn) {
    $donations_total = get_table_total($conn, 'donations');
    $expenses_total = get_table_total($conn, 'expenses');
    $difference = $donations_total - $expenses_total;
    p_print("There has been <b>\$$donations_total</b> in donations and <b>\$$expenses_total</b> in expenses for a net total of <b>\$$difference</b>.");
    $donations_count = get_table_count($conn, 'donations');
    $expenses_count = get_table_count($conn, 'expenses');
    p_print("There have been <b>$donations_count</b> donations and <b>$expenses_count</b> expense(s).");
}
?>
<table><tr><td><p align="center"><strong>Donations</strong></p>
<?php
show_donations_table($connection);
?></td><td><p align="center"><strong>Expenses</strong></p>
<?php
show_expenses_table($connection);
?></td></tr></table>
<?php
show_statistics($connection);
?>
    <hr />
    <p>
      The dispensation of the fund is explained on <a href="http://www.cs.grin.edu/~wellons/plans/treasurer-duties.html">the treasurer's page</a>.
    </p>
    <hr />
    <p>
      Some individuals have been sending donations to:<br />
      Jonathan Wellons<br />   
      Box# 15 - 51<br />
      Grinnell, IA 50112
    </p>
<p>
Others are using the <a href="http://www.paypal.com">PayPal</a> account of 'wellons<!-- -->@cs.gr<!-- -->in.edu'. <b>PayPal charges a fee of 2.9% + $ 0.30 when you pay by credit card.</b>
</p>
    <p>
      Some individuals who are donating anonymously are choosing a pseudonym so that they can verify that their donation appears in the table.
</p>
    <hr />
    <p>
      Questions and comments should be directed to: wellons@<!-- -->cs .grinnell.edu
    </p>
    
    
    <p>
      <a href="http://validator.w3.org/check/referer"><img
	  src="http://www.w3.org/Icons/valid-xhtml11"
	  alt="Valid XHTML 1.1!" height="31" width="88" /></a>
    </p>
    
    <p>
      Created: Thursday, July 24, 2003
    </p>
    
  </body>
</html>
