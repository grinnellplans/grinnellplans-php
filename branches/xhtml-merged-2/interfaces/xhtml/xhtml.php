<?php
/**
 * Returns the interface object that we're using for this particular interface.
 *
 * For now, every file in interfaces/ must implement this.
 * @todo At some point, get rid of this (probably by changing how interfaces are stored in the DB)
 * @return DisplayInterface
 */
function interface_construct()
{
	return new XHTMLInterface();
}
/**
 * Gives Plans a long-needed bump into the world of CSS.
 *
 * This version is fairly
 * sparse, for flexibility combined with readable and not-bloated code.  There
 * may be a more bloated version for fancy-schmancy stylesheets appearing later.
 */
class XHTMLInterface implements DisplayInterface {
	public function display_page(PlansPage $page)
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?php
		echo $page->title ?></title>
			<link rel="stylesheet" type="text/css" href="<?php
			echo $page->stylesheet ?>" />
<?php
			//TODO Javascript

?>
</head>
<body>

<div id="wrapper">

<div id="nav">
<?php
			//print the mainpanel
			if ($page->mainpanel) $this->disp_mainpanel($page);
?>
</div>

<div id="main">

<?php
		// display the widgets in the contents of the page
		array_walk($page->contents, array($this, 'disp_widget'));
		echo "\n</div>\n</div><!--wrapper-->\n";
		if ($page->footer) $this->disp_footer($page->footer);
		echo "</body></html>";
	}
	protected function disp_mainpanel($page) 
	{
		$panel = $page->mainpanel;
		// print the logo
		if ($panel->linkhome) {
			$panel->linkhome->description = '<span>Home</span>';
			$panel->linkhome->html_attributes = ' class="logo"';
			echo ("\t" . $panel->linkhome->toHTML() . "\n");
		}
		// print the finger form
		if ($panel->fingerbox) {
			echo '<div id="finger">';
			echo $panel->fingerbox->toHTML();
		/*
		<form method="<?=$panel->fingerbox->method ?>" action="<?=$panel->fingerbox->action?>">
		foreach($panel->fingerbox->contents as $item) {
		print("\t\t".$item->toHTML()."\n");
		}
		</form>
		 */
			echo "</div>";
		}
		// print the user's links
		if ($panel->requiredlinks) $this->disp_links($panel);
		// print the autoread
		if ($panel->autoreads) $this->disp_autoread($panel->autoreads, $page->url, $page->autoreadpriority);
	}
	protected function disp_footer($footer) 
	{
		echo "<div id=\"footer\">\n";
		if ($footer->doyouread) {
			echo "\t<div id=\"justupdated\">\n";
			echo "\t\tDo you read " . $footer->doyouread->toHTML() . ", who just updated?\n";
			echo "\t</div>\n";
		}
		if ($footer->legal) {
			echo "\t<div id=\"legal\">\n";
			echo "\t\t" . $footer->legal->toHTML() . "\n";
			echo "\t</div>\n";
		}
		echo "</div>\n";
	}
	protected function disp_autoread($autoreads, $myurl, $privl) 
	{
?>
	<div id="autofingerlist"><span class="autofinger">autofinger list</span>
		<ul>
<?php
		$front = "\t\t\t<li class=\"autoreadentry\">";
		$end = "</li>\n";
		$new_url = remove_param($myurl, 'mark_as_read');
		foreach($autoreads as $ar) {
			$priority = $ar->priority;
			$new_url = add_param($new_url, 'myprivl', $priority);
			if ($priority == $privl) {
				echo "\t\t" . '<div id="current"><li class="autoread">' . "\n";
			} else {
				echo "\t\t<li class=\"autoread\">\n";
			}
			// design decision: print a link and nolink option for each level, to give
			// stylesheet creators the choice of what to display
			echo "\t\t<span class=\"autoreadlevel\">" . $ar->title . "</span>\n" . "\t\t<a class=\"autoreadlink\" href=\"$new_url\">" . $ar->title . "</a>\n";
			// print the little X to mark all as read
			echo "\t\t<a class=\"markasread\" onClick =\"return confirm(" . "'Are you sure you\'d like to mark all the Plans on level " . $priority . " as read?')\" href=\"http://" . add_param($new_url, 'mark_as_read', 1) . "\"><span>X</span></a>\n";
			// now print the plans on this autoread level
			echo "\t\t\t<ul>\n";
			foreach($ar->contents as $item) {
				echo $front . $item->toHTML() . $end;
			}
?>
			</ul>
<?php
			if ($priority == $privl) {
				echo "\t\t</li></div>\n";
			} else {
				echo "\t\t</li>\n";
			}
		}
?>
	</ul></div>
<?php
	}
	protected function disp_links($panel) 
	{
?>
	<div id="links">
		<ul>
<?php
		$front = "\t\t<li>";
		$end = "</li>\n"; // TODO do we want a class div around these?
		// print out the links
		if ($panel->requiredlinks) for ($i = 0; ($l = $panel->requiredlinks[$i]); $i++) {
			if (strtolower($l->description) == 'log out') $logout = $l;
			else echo $front . strtolower($l->toHTML()) . $end;
		}
		if ($panel->optionallinks) for ($i = 0; ($l = $panel->optionallinks[$i]); $i++) {
			echo $front . strtolower($l->toHTML()) . $end;
		}
		if ($logout) echo $front . strtolower($logout->toHTML()) . $end;
?>
		</ul>
	</div>
<?php
	}
	protected function disp_widget($obj, $key = null) 
	{
		if ($obj instanceof EditBox) {
			$this->disp_editbox($obj);

		} else if ($obj instanceof Form) {
			print ($obj->toHTML(array($this, 'disp_widget_str')) . "\n");

		} else if ($obj instanceof FormItem) {
			if ($obj->title != null) {
				$title = '<span class="prompt_label">';
				$title .= $obj->title;
				$title .= ' </span>';
			}
			$item .= $obj->toHTML();
			if ($obj->description != null) {
				$desc = '<span class="prompt_description">';
				$desc .= $obj->description;
				$desc .= '</span>';
			}

			$str = "\t" . '<div class="form_prompt">';
			if ($obj instanceof TextInput || $obj instanceof TextareaInput || $obj instanceof PasswordInput) {
				$str .= $title . $item . $desc;
			} else {
				$str .= $item . $title . $desc;
			}
			$str .= '</div>';
			print($str . "\n");

		} else if ($obj instanceof Hyperlink) {
			print (strtolower($obj->toHTML()) . "\n");

		} else if ($obj instanceof Secret) {
			print("<div class='secret'>\n");
			print("\t<span class='secret_id'>$obj->secret_id</span>\n");
			print("\t<span class='date'>$obj->date</span>\n");
			print($obj->message);
			print("</div>");

		} else if ($obj instanceof InfoText) {
			print ("\t<span class=\"info\">\n");
			if ($obj->title && $obj->title != '') print ("\t<span class=\"infotitle\">" . $obj->title . "</span>\n");
			print ("\t" . $obj->toHTML() . "\n");
			print ("\t</span>\n");

		} else if ($obj instanceof RequestText) {
			print ("\t<span class=\"question\">\n");
			print ("\t" . $obj->toHTML() . "\n");
			print ("\t</span>\n");

		} else if ($obj instanceof AlertText) {
			print ("\t<span class=\"alert\">\n");
			print ("\t" . $obj->toHTML() . "\n");
			print ("\t</span>\n");

		} else if ($obj instanceof HeadingText) {
			print ('<h' . $obj->sublevel . '>' . $obj->message . '</h' . $obj->sublevel . '>');

		} else if ($obj instanceof RegularText) {
			print ("\t<span>" . $obj->toHTML() . "</span>\n");

		} else if ($obj instanceof PlanContent) {
			$this->disp_plan($obj);

		} else if ($obj instanceof PlanText) {
			print('<div class="plan_text">');
			print $obj->toHTML();
			print('</div>');

		} else if ($obj instanceof WidgetList) {
			if ($obj->title != null) {
				$str .= '<span class="prompt_label">';
				$str .= $obj->title;
				$str .= '</span>';
				print($str);
			}
			print ("\n<ul id='" . $obj->identifier . ($obj->class ? "' class=" . $obj->class : "'") . ">");
			foreach($obj->contents as $widg) {
				print ("\n<li>");
				$this->disp_widget($widg, null);
				print ("</li>");
			}
			print ("\n</ul>\n");
		} else if ($obj instanceof WidgetGroup) {
			//TODO are we still using ->class?
			print ("\n<div id='" . $obj->identifier . ($obj->class ? "' class=" . $obj->class : "'") . ">");
			foreach($obj->contents as $widg) {
				$this->disp_widget($widg, null);
			}
			print ("\n</div>\n");
		}
	}

	/**
	 * Poor foresight led to disp_widget printing everything directly instead of
	 * outputting a string.  This is a simple hack that uses output buffering to
	 * get around that fact.
	 * @param mixed $value See {@link disp_widget()}
	 * @return string The widget as HTML
	 */
	public function disp_widget_str($value) {
		ob_start();
		$this->disp_widget($value);
		$retval = ob_get_contents();
		ob_end_clean();
		return $retval;
	}
	protected function disp_plan($plan) 
	{
?>
	<div id="header">
		<ul>
		<li class="username">Username: <span class="username"><?php echo $plan->username ?></span></li>
		<li class="lastupdated">Last Updated: <span class="lastupdated"><?php echo $plan->lastupdate ?></span></li>
		<li class="lastlogin">Last Login: <span class="lastlogin"><?php echo $plan->lastlogin ?></span></li>

		<li class="name">Name: <span class="name"><?php echo $plan->planname ?></span></li>
		</ul>
	</div>

<?php
		$this->disp_widget($plan->text, null);
	/*
	if ($plan->text->markup)
	print($plan->text->message);
	else
	print("Uh oh! Plan markup is screwy! This is a bug.\n");
	 */

		if ($plan->addform) {
			$this->disp_widget($plan->addform, NULL);
		}
	}
	protected function disp_editbox($box) 
	{
?><div id="editform">
	<form action="<?php echo $box->action
	?>" method="<?php echo $box->method
?>">
<textarea id="edittextarea" rows="<?php echo $box->rows
?>" cols="<?php echo $box->columns
?>" 
		name="plan" wrap="virtual" onkeyup="javascript:countlen();">
<?php
print ($box->text->message);
?>
		</textarea>
		<input type="hidden" name="part" value="1"><br>
		<img id="leftfill" src="left.gif" width="2" height="16"><img id="filled" src="filled.gif" width="0" height="16"><img id="unfilled" src="unfilled.gif" width="100" height="16"><img id="rightfill" src="right.gif" width="2" height="16"> <input type="text" name="perc" value="0%" size="4" style="border: 0px" readonly>
		&nbsp;&nbsp;&nbsp;<input type="submit" value="Change Plan">
	</form></div>
<?php
//TODO ^^ that is ugly and not very styleable. Find something better.

	}
}
?>
