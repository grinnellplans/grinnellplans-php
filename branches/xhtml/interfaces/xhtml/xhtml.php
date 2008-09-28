<?
/**
 * XHTML Interface
 * Gives Plans a long-needed bump into the world of CSS.  This version is fairly
 * sparse, for flexibility combined with readable and not-bloated code.  There
 * may be a more bloated version for fancy-schmancy stylesheets appearing later.
 */

/*
 * Interface files must include the public function 
 *   interface_disp_page(PlansPage $page)
 * which must handle a PlansPage object and everything it contains, as
 * specified in syntax-classes.php.
 * 
 */

// include the common function file //TODO deprecated?
include (realpath(dirname(__FILE__).'/../common.php'));

/* DEPRECATED */
function interface_disp_begin($searchname, $linkarr, $autofingerarr, $myurl, $myprivl, $cssloc, $jsfile)
{

  if ($searchname) {
      $title = "[$searchname]'s Plan";
  } else {
      $title = "Plans 2.2";
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php echo $title?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $cssloc?>" />
<?
if ( !is_null( $jsfile ) )
    echo "<script language=\"javascript\" type=\"text/javascript\" src=\"$jsfile\"></script>";
?>
</head>
<body>

<div id="wrapper">

<div id="nav">
	<a href="index.php" class="logo">&nbsp;</a>
	<div id="finger">

		<form action="read.php" method="get">
		<input type="text" name="searchname" />
		<input type="submit" value="Finger" />
		</form>
	</div>

	<div id="links">
		<ul>
<?
$front = "\t\t<li>";
$end = "</li>\n";	// TODO do we want a class div around these?

// print out the links
for ($i=0; $linkarr[$i]; $i++) {
	echo $front . $linkarr[$i] . $end;
}
?>
		</ul>
	</div>

	<div id="autofingerlist"><span class="autofinger">autofinger list</span>
		<ul>
<?
$front = "\t\t\t<li class=\"autoreadentry\">";
$end = "</li>\n";

$new_url = remove_param($myurl, 'mark_as_read');

for ($i=0; $autofingerarr[$i][0]; $i++) {
	$priority = $i+1;
	$new_url = add_param($new_url, 'myprivl', $priority);

	if ($priority == $privl) {
		echo "\t\t" . '<div id="current"><li class="autoread">';
	} else {
		echo "\t\t<li class=\"autoread\">";
	}

	// design decision: print a link and nolink option for each level, to give 
	// stylesheet creators the choice of what to display
	echo "<span class=\"autoreadlevel\">"
		. $autofingerarr[$i][0]	. "</span>\n"
		. "\t\t<a class=\"autoreadlink\" href=\"http://$new_url\">"
		. $autofingerarr[$i][0] . "</a>\n";

	// print the little X to mark all as read
	echo "\t\t<a class=\"markasread\" onClick =\"return confirm("
			. "'Are you sure you\'d like to mark all the Plans on level "
			. $priority . " as read?')\" href=\"http://"
			. add_param($new_url, 'mark_as_read', 1) . "\"><span>X</span></a>\n"; 
	// now print the plans on this autoread level
	echo "\t\t\t<ul>\n";
	$new_url = remove_param($new_url, 'mark_as_read');

	for ($j=1; $autofingerarr[$i][$j]; $j++) {
		echo $front . $autofingerarr[$i][$j] . $end;
	}
?>
			</ul>
		</li>
<?
}
?>
	</ul></div>

</div>

<div id="main">

<?

}

//TODO needed?
function priority_link($myurl, $notprivl)
{

if (ereg("myprivl", $myurl))
{$myurlx = ereg_replace("myprivl=[0-9]{0,1}", "myprivl=" . $notprivl,
$myurl);
}//if already has privl
else { //if doesn't already have privl
if (ereg("\?", $myurl)) // if has ? but not privl
{$myurlx = $myurl . "&myprivl=" . $notprivl;}
else  //must add on extra info
{$myurlx= $myurl . "?myprivl=" . $notprivl;}
}//else, if doesn't already have privl
 
echo "<tr><td></td><td><p class=\"imagelev2\">&nbsp;</p></td><td></td>";  
echo "<td><a href=\"http://" . $myurlx . "\" class=\"lev2\">level " .
$notprivl .
"</a></td></tr>";
}//function priority_link



/* DEPRECATED */
function interface_disp_end($myurl) {
	echo "\n</div>\n</body></html>";
}


/* DEPRECATED */
function interface_disp_header($username, $pseudo, $lastlogin, $lastupdate) {
?>
	<div id="header">
		<ul>
		<li class="username">Username: <span class="username"><?=$username?></span></li>
		<li class="lastupdated">Last Updated: <span class="lastupdated"><?=$lastupdate?></span></li>
		<li class="lastlogin">Last Login: <span class="lastlogin"><?=$lastlogin?></span></li>

		<li class="name">Name: <span class="name"><?=$pseudo?></span></li>
		</ul>
	</div>
<?

}

/* DEPRECATED */
function interface_disp_plantext($text) {
?>
	<div id="plan">
<? echo $text; ?>

	</div>
<?
}

function interface_disp_editbox($text, $rows, $cols) {
?>
		<form action="edit.php" method="post" id="editform" name="editform">
		<textarea id="edittextarea" rows="<?=$rows?>" cols="<?=$cols?>" 
			name="plan" wrap="virtual" onkeyup="javascript:countlen();">
<?
	echo $text;
?>

		</textarea><input type="hidden" name="part" value="1"><br>
		<img id="leftfill" src="left.gif" width="2" height="16"><img id="filled" src="filled.gif" width="0" height="16"><img id="unfilled" src="unfilled.gif" width="100" height="16"><img id="rightfill" src="right.gif" width="2" height="16"> <input type="text" name="perc" value="0%" size="4" style="border: 0px" readonly>
		&nbsp;&nbsp;&nbsp;<input type="submit" value="Change Plan"></form>
<?
}

/* DEPRECATED */
function interface_disp_footer($showaddform, $searchnum, $justupdated) {
?>

	<div id="footer">
<? if($showaddform) { 
	//TODO make this not suck
?>

	<form method="POST" action="readadd.php">
	<input type="hidden" name="addtolist" value="1">
	<input type="radio" name="privlevel" value="0" <?php echo $myonlist[0];?>>X
	<input type="radio" name="privlevel" value="1" <?php echo $myonlist[1];?>>1
	<input type="radio" name="privlevel" value="2" <?php echo $myonlist[2];?>>2
	<input type="radio" name="privlevel" value="3" <?php echo $myonlist[3];?>>3
	<input type="hidden" name="searchnum" value="<?php echo $searchnum;?>">
	<input type="submit" value="Set Priority">&nbsp;
	</form>

<?	}
$justupdated = "<a href=\"read.php?searchname=" . $justupdated . "\">" . $justupdated . "</a>";
?>

		<div id="doyouread">Do you read <?=$justupdated?>, who just updated?</div>

		<div id="tos">
<?
		disclaimer();
?>

		</div>
	</div>
<?

}

function interface_disp_page(PlansPage $page) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php echo $page->title?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $page->stylesheet?>" />
<?
	//TODO Javascript
?>
</head>
<body>

<div id="wrapper">

<div id="nav">
<?
	//print the mainpanel
	if ($page->mainpanel)
		disp_mainpanel($page);

?>
</div>

<div id="main">

<?
	// display the widgets in the contents of the page
	array_walk($page->contents, 'disp_widget');

	echo "\n</div>\n</div><!--wrapper-->\n";
		
	if ($page->footer)
		disp_footer($page->footer);

	echo "</body></html>";
}

function disp_mainpanel($page) {

	$panel = $page->mainpanel;

	// print the logo
	if ($panel->linkhome) {
		$panel->linkhome->description = '&nbsp;';
		$panel->linkhome->html_attributes = ' class="logo"';
		echo("\t" . $panel->linkhome->toHTML() . "\n");
	}

	// print the finger form
	if ($panel->fingerbox) {
?>
	<div id="finger">
	<form method="<?=$panel->fingerbox->method ?>" action="<?=$panel->fingerbox->action?>">
<?
	foreach($panel->fingerbox->fields as $item) {
		print("\t\t".$item->toHTML()."\n");
	}
?>
	</form>
	</div>
<?
	}

	// print the user's links
	if ($panel->requiredlinks)
		disp_links($panel);

	// print the autoread
	if ($panel->autoreads)
		disp_autoread($panel->autoreads, $page->url, $page->autoreadpriority);
}

function disp_footer($footer) {

	echo "<div id=\"footer\">\n";

	if ($footer->doyouread) {
		echo "\t<div id=\"justupdated\">\n";
		echo "\t\tDo you read ". $footer->doyouread->toHTML() .", who just updated?\n";
		echo "\t</div>\n";
	}

	if ($footer->legal) {
		echo "\t<div id=\"legal\">\n";
		echo "\t\t" . $footer->legal->toHTML() . "\n";
		echo "\t</div>\n";
	}

	echo "</div>\n";
}

function disp_autoread($autoreads, $myurl, $privl) {
?>
	<div id="autofingerlist"><span class="autofinger">autofinger list</span>
		<ul>
<?
	$front = "\t\t\t<li class=\"autoreadentry\">";
	$end = "</li>\n";

	$new_url = remove_param($myurl, 'mark_as_read');

	foreach ($autoreads as $ar) {
		$priority = $ar->priority;
		$new_url = add_param($new_url, 'myprivl', $priority);

		if ($priority == $privl) {
			echo "\t\t" . '<div id="current"><li class="autoread">' . "\n";
		} else {
			echo "\t\t<li class=\"autoread\">\n";
		}

		// design decision: print a link and nolink option for each level, to give 
		// stylesheet creators the choice of what to display
		echo "\t\t<span class=\"autoreadlevel\">"
			. $ar->title . "</span>\n"
			. "\t\t<a class=\"autoreadlink\" href=\"$new_url\">"
			. $ar->title . "</a>\n";

		// print the little X to mark all as read
		echo "\t\t<a class=\"markasread\" onClick =\"return confirm("
				. "'Are you sure you\'d like to mark all the Plans on level "
				. $priority . " as read?')\" href=\"http://"
				. add_param($new_url, 'mark_as_read', 1) . "\"><span>X</span></a>\n"; 
		// now print the plans on this autoread level
		echo "\t\t\t<ul>\n";

		foreach($ar->contents as $item) {
			echo $front . $item->toHTML() . $end;
		}

?>
			</ul>
<?
		if ($priority == $privl) {
			echo "\t\t</li></div>\n";
		} else {
			echo "\t\t</li>\n";
		}
	}
?>
	</ul></div>
<?
}


function disp_links($panel) {
?>
	<div id="links">
		<ul>
<?
	$front = "\t\t<li>";
	$end = "</li>\n";	// TODO do we want a class div around these?

	// print out the links
	if ($panel->requiredlinks)
	for ($i=0; ($l = $panel->requiredlinks[$i]); $i++) {
		if (strtolower($l->description) == 'log out')
			$logout = $l;
		else
			echo $front . $l->toHTML() . $end;
	}
	if ($panel->optionallinks)
	for ($i=0; ($l = $panel->optionallinks[$i]); $i++) {
		echo $front . $l->toHTML() . $end;
	}
	if ($logout)
	echo $front . $logout->toHTML() . $end;
?>
		</ul>
	</div>
<?
}

function disp_widget($value, $key) {
	switch (get_class($value)) {
	case 'Form':
		print($value->toHTML() . "\n");
		break;
	case 'InfoText':
		print("\t<span class=\"info\">\n");
		if ($value->title && $value->title != '')
			print("\t<span class=\"infotitle\">" . $value->title . "</span>\n");
		print("\t" . $value->toHTML() . "\n");
		print("\t</span>\n");
		break;
	case 'RequestText':
		print("\t<span class=\"question\">\n");
		print("\t" . $value->toHTML() . "\n");
		print("\t</span>\n");
		break;
	case 'AlertText':
		print("\t<span class=\"alert\">\n");
		print("\t" . $value->toHTML() . "\n");
		print("\t</span>\n");
		break;
	case 'HeadingText':
		print('<h'.$value->sublevel.'>' . $value->message . '</h' . $value->sublevel . '>');
		break;
	case 'EditBox':
		disp_editbox($value);
		break;
	case 'PlanContent':
		disp_plan($value);
		break;
	default:
		//foobar
		break;
	}
}

function disp_plan($plan) {
?>
	<div id="header">
		<ul>
		<li class="username">Username: <span class="username"><?=$plan->username?></span></li>
		<li class="lastupdated">Last Updated: <span class="lastupdated"><?=$plan->lastupdate?></span></li>
		<li class="lastlogin">Last Login: <span class="lastlogin"><?=$plan->lastlogin?></span></li>

		<li class="name">Name: <span class="name"><?=$plan->planname?></span></li>
		</ul>
	</div>

<div id="plan">
<?
	print($plan->text);
	/*
	if ($plan->text->markup)
		print($plan->text->message);
	else
		print("Uh oh! Plan markup is screwy! This is a bug.\n");
	*/
?>

</div>

<?
	if ($plan->addform) {
		disp_widget($plan->addform, NULL);
	}
}


function disp_editbox($box) {
?><div id="editform">
	<form action="<?=$box->action?>" method="<?=$box->method?>">
		<textarea id="edittextarea" rows="<?=$box->rows?>" cols="<?=$box->columns?>" 
		name="plan" wrap="virtual" onkeyup="javascript:countlen();">
<?
	print($box->text->message);
?>
		</textarea>
		<input type="hidden" name="part" value="1"><br>
		<img id="leftfill" src="left.gif" width="2" height="16"><img id="filled" src="filled.gif" width="0" height="16"><img id="unfilled" src="unfilled.gif" width="100" height="16"><img id="rightfill" src="right.gif" width="2" height="16"> <input type="text" name="perc" value="0%" size="4" style="border: 0px" readonly>
		&nbsp;&nbsp;&nbsp;<input type="submit" value="Change Plan">
	</form></div>
<? //TODO ^^ that is ugly and not very styleable. Find something better.
}

/* DEPRECATED */
function interface_disp_preferences($linkarray) {

	print("\t<ul id=i\"prefs\"><h2>Preferences</h2>\n");

	foreach ($linkarray as $index => $value) {
		if ($value == 'nolink') {
			// It's a heading, print it
			print("\t\t<li><h3>$index</h3></li>\n");
		} else {
			print("\t\t<li><a href=\"$value\" class=\"pref\">$index</a></li>\n");
		}
	}

	print("\t</ul>");
}

/* DEPRECATED */
function interface_disp_preferences_list($page) {
	print("\t<div id=\"pref_list\">\n");

	foreach ($page->content as $item) {
		print("\t\t<h2>" . $page->title . "</h2>\n\t\t<div>\n");
		parse_chunks($item->content, "\t\t\t");
		print("\t\t</div>\n");
	}
	print("\t</div>\n");
}

/* DEPRECATED */
function parse_chunks($list, $prepend) {
	foreach($list as $chunk) {
		switch ($chunk->type) {
		case 'head':
			print("$prepend<h3>$chunk->source</h3>");
			break;
		case 'info':
			print("$prepend<span>$chunk->source</span>");
			break;
		case 'form':
			//print($chunk->source);
			parse_chunks($chunk->source, $prepend);
			break;
		case 'formd':
			print("$prepend<span>");
			print($chunk->source);
			print("</span>");
			break;
		case 'formh':
			print($prepend);
			print($chunk->source);
			break;
		//TODO add others
		}
		print("\n");
	}
}

/*
function interface_disp_preferences_list($list) {
	print("\t<div class=\"pref_list\">\n");
	print_preflist($list, "\t\t");
	print("\t</div>\n");
}
 */

/* Helper function for interface_disp_preferences_list */
/*
function print_preflist($list, $prepend) {
	print("$prepend<h2>$list[1]</h2>\n");
	print("$prepend<ul class=\"pref_$list[0]\">\n");
	for ($i=2; $i<count($list); $i++) {
		if (is_array($list[$i])) {
			//recurse
			print_preflist($list[$i], "\t" . $prepend);
		} else {
			print($prepend . $list[$i]);
		}
	}
	print("$prepend</ul>");
}
 */

?>
