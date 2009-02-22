<?php
define('LONG_DATE_FORMAT', 'D F jS Y, g:i A');
define('SHORT_DATE_FORMAT', 'm-d-Y');

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
<title><?php echo $page->title ?></title>
<?php
		foreach ($page->stylesheets as $css) {
			echo '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
		}
		// Add the length counter script if it's the edit page
		if ($page->identifier == 'edit') {
			echo '<script src="edit.js" type="text/javascript" language="javascript"></script>';
		}

?>
</head>
<body id="planspage_<?php echo strtolower($page->identifier) ?>" class="<?php echo strtolower($page->group) ?>">

<div id="wrapper">

<div id="nav"><div>
<?php
			//print the mainpanel
			if ($page->mainpanel) $this->disp_mainpanel($page);
?>
</div></div>

<div id="main"><div>

<?php
		// display the widgets in the contents of the page
		array_walk($page->contents, array($this, 'disp_widget'));
		echo "\n</div></div>"; // "main"
		if ($page->footer) $this->disp_footer($page->footer);
		echo "\n</div>"; // "wrapper"
		echo "\n</body></html>";
	}
	protected function disp_mainpanel($page) 
	{
		$panel = $page->mainpanel;
		// print the logo
		if ($panel->linkhome) {
			echo '<div id="logo">';
			$panel->linkhome->description = 'Home';
			echo ("\t" . $panel->linkhome->toHTML() . "\n");
			echo "</div>";
		}
		// print the finger form
		if ($panel->fingerbox) {
			echo '<div id="finger">';
			echo $panel->fingerbox->toHTML(array($this, 'disp_widget_str'));
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
			echo "\t<div id=\"justupdated\"><div>\n";
			echo "\t\tDo you read " . $footer->doyouread->toHTML() . ", who just updated?\n";
			echo "\t</div></div>\n";
		}
		if ($footer->legal) {
			echo "\t<div id=\"legal\"><div>\n";
			echo "\t\t" . $footer->legal->toHTML() . "\n";
			echo "\t</div></div>\n";
		}
		echo "</div>\n";
	}
	/**
	 * @todo junk $myurl
	 */
	protected function disp_autoread($autoreads, $myurl, $privl) 
	{
?>
	<div id="autoread"><h2>Autoread List</h2>
		<ul>
<?php
		$length = count($autoreads);
		foreach($autoreads as $i => $ar) {
			$priority = $ar->priority;
			$class = 'autoreadlevel';
			if ($priority == $privl) {
				$class .= ' current';
			} else {
				$class .= ' notcurrent';
			}
			if ($i == 0) {
				$class .= ' first';
			}
			if ($i == $length - 1) {
				$class .= ' last';
			}
			echo "\t\t<li class=\"$class\">\n";
			echo "\t\t<div class=\"autoreadname\">\n";
			$link = $ar->link;
			echo "\t\t\t<a class=\"autoreadlink\" href=\"$link->href\">" . $ar->title . "</a>\n";
			// print the little X to mark all as read
			echo "\t\t\t<a class=\"markasread\" onclick =\"return confirm(" . "'Are you sure you\'d like to mark all the Plans on level " . $priority . " as read?')\" href=\"http://" . add_param($new_url, 'mark_as_read', 1) . "\">X</a>\n";
			echo "\t\t</div>\n";
			// now print the plans on this autoread level
			echo "\t\t\t<ul>\n";

			$end = "</li>\n";
			$innerlength = count($ar->contents);
			foreach($ar->contents as $j => $item) {
				$class = 'autoreadentry';
				if ($j == 0) {
					$class .= ' first';
				}
				if ($j == $innerlength - 1) {
					$class .= ' last';
				}
				$front = "\t\t\t<li class=\"$class\">";
				echo $front . $item->toHTML() . $end;
			}
?>
			</ul>
<?php
			echo "\t\t</li>\n";
		}
?>
	</ul></div>
<?php
	}
	protected function disp_links($panel) 
	{
?>
	<div id="links"><h2>Links</h2>
		<ul>
<?php
		// Just use this for the first one, then change it
		$front = "\t\t<li class='first'>";
		$end = "</li>\n";
		// print out the links
		if ($panel->requiredlinks) for ($i = 0; ($l = $panel->requiredlinks[$i]); $i++) {
			if (strtolower($l->description) == 'log out') $logout = $l;
			else echo $front . $l->toHTML() . $end;
			$front = "\t\t<li>";
		}
		if ($panel->optionallinks) for ($i = 0; ($l = $panel->optionallinks[$i]); $i++) {
			echo $front . $l->toHTML() . $end;
		}
		if ($logout) echo "\t\t<li class='last'>" . $logout->toHTML() . "</li>\n";
?>
		</ul>
	</div>
<?php
	}

	protected static function id_and_class($id, $class) {
		$out = array();
		if ($id != null) {
			$out[] = 'id="'.$id.'"';
		}

		if (!is_array($class)) {
			$class = array($class);
		}
		$class = array_filter($class);
		$out[] = 'class="'.implode(' ', $class).'"';
		return implode(' ', $out);
	}

	protected function disp_widget($obj, $key = null) 
	{
		if ($obj instanceof EditBox) {
			$this->disp_editbox($obj);

		} else if ($obj instanceof Form) {
			$attrs = self::id_and_class($obj->identifier, array($obj->group, 'form'));
			$str = "<div $attrs>";
			$str .= $obj->toHTML(array($this, 'disp_widget_str')) . "\n";
			$str .= '</div>';
			print $str;

		} else if ($obj instanceof SubmitInput) {
			$attrs = 'type="submit" class="submitinput"';
			if ($obj->name) {
				$attrs .= " name=\"$obj->name\"";
			}
			echo "<button $attrs>$obj->value</button>";
		} else if ($obj instanceof FormItem) {

			if ($item->identifier) {
				$item_id = $item->identifier;
			} else {
				$item_id = $obj->parent_form->identifier . '_' . $obj->name;
			}
			$item_id = str_replace('[]', '', $item_id);
			// If it's possible there are multiple inputs with the same name, 
			// append a number to the end to make it unique
			if ($obj instanceof CheckboxInput || $obj instanceof RadioInput) {
				$num = (int)$this->element_ids[$item_id]++;
				$item_id = $item_id . $num;
			}

			if ($obj->title != null) {
				$title = '<label class="prompt_label" for="' . $item_id . '">';
				$title .= $obj->title;
				$title .= ' </label>';
			}
			$obj->html_attributes = " id=\"$item_id\"";
			$item .= $obj->toHTML();
			if ($obj->description != null) {
				$desc = '<span class="prompt_description">';
				$desc .= $obj->description;
				$desc .= '</span>';
			}

			$strbeg = "\t" . '<div class="form_prompt ' . strtolower(get_class($obj)) . '">';

			$strend = '</div>';
			if ($obj instanceof HiddenInput) {
				$str = $item;
			} else if ($obj instanceof TextInput || $obj instanceof TextareaInput || $obj instanceof PasswordInput) {
				$str = $strbeg . $title . $item . $desc . $strend;
			} else {
				$str = $strbeg . $item . $title . $desc . $strend;
			}
			print($str . "\n");

		} else if ($obj instanceof Hyperlink) {
			$obj->html_attributes = self::id_and_class($obj->identifier, $obj->group);
			print (strtolower($obj->toHTML()) . "\n");

		} else if ($obj instanceof Secret) {
			print("<div class='secret'>\n");
			print("\t<span class='secret_id'>$obj->secret_id</span>\n");
			print("\t<span class='date'>$obj->date</span>\n");
			print($obj->message);
			print("</div>");

		} else if ($obj instanceof PlanContent) {
			$this->disp_plan($obj);

		} else if ($obj instanceof PlanText) {
			print('<div class="plan_text">');
			print $obj->toHTML();
			print('</div>');

		} else if ($obj instanceof HeadingText) {
			print ('<h' . $obj->sublevel . '>' . $obj->message . '</h' . $obj->sublevel . '>');

		} else if ($obj instanceof RegularText) {
			print ("\t<span>" . $obj->toHTML() . "</span>\n");

		} else if ($obj instanceof Text) {
			$class = strtolower($obj->group);
			print ("\t<div class=\"$class\">\n");
			print ("\t<span class=\"title\">" . $obj->title . "</span>\n");
			print ("\t<span class=\"body\">" . $obj->toHTML() . "</span>\n");
			print ("\t</div>\n");

		} else if ($obj instanceof FormItemSet) {
			$attrs = self::id_and_class($obj->identifier, array($obj->group, 'formitemset'));
			print ("\n<div $attrs>");
			if ($obj->title != null) {
				$title = '<span class="promptset_label">';
				$title .= $obj->title;
				$title .= ' </span>';
				echo $title;
			}
			foreach($obj->contents as $widg) {
				$this->disp_widget($widg, null);
			}
			print ("\n</div>\n");
		} else if ($obj instanceof NotesBoard) {
			$attrs = self::id_and_class($obj->identifier, $obj->group);
			// Notes can have a table, who gives a shit?
			print ("\n<table $attrs>");
			print ('<tr class="heading"><th>Title</th><th>Newest Message</th><th># Posts</th><th>First</th><th>Last</th></tr>');
			foreach ($obj->contents as $i => $widg) {
				if ($i % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				print("\n<tr class=\"$class\"><td>");
				$this->disp_widget($widg->title);
				print("\n</td><td>");
?>
				<span class="long"><?php echo date(LONG_DATE_FORMAT, $widg->updated) ?></span>
				<span class="short"><?php echo date(SHORT_DATE_FORMAT, $widg->updated) ?></span>
<?php
				print("\n</td><td>");
				print($widg->posts);
				print("\n</td><td>");
				$this->disp_widget($widg->firstposter);
				print("\n</td><td>");
				$this->disp_widget($widg->lastposter);
				print("\n</td></tr>");
			}
			print ("\n</table>\n");
		} else if ($obj instanceof NotesPost) {
			print("\n<div class=\"notes_post\">");
			print("\n\t<div class=\"notes_post_header\">");
			print('<div class="post_id">' . $obj->id . '</div>');
			print('<div class="post_author">' . $this->disp_widget_str($obj->poster) . '</div>');
			print('<div class="post_date"><span class="long">' . date(LONG_DATE_FORMAT, $obj->date)
				. '</span><span class="short">' . date(SHORT_DATE_FORMAT, $obj->date) . '</span></div>');

			print('<div class="post_votes">');
			print("($obj->score) ($obj->votes Votes)");
			print('</div>');

			print("\n\t</div>");
			print("\n\t<div class=\"notes_post_content\">");
			print($obj->contents);
			print("\n\t</div>");
			print("\n</div>");
		} else if ($obj instanceof WidgetList) {
			$attrs = self::id_and_class($obj->identifier, $obj->group);
			print ("\n<ul $attrs>");
			foreach($obj->contents as $i => $widg) {
				if ($i % 2 == 0) {
					$class = 'even';
				} else {
					$class = 'odd';
				}
				if ($i == 0) {
					$class .= ' first';
				}
				if ($i == count($obj->contents) - 1) {
					$class .= ' last';
				}

				if ($class) {
					print ("\n<li class=\"$class\">");
				} else {
					print ("\n<li>");
				}

				$this->disp_widget($widg, null);
				print ("</li>");
			}
			print ("\n</ul>\n");
		} else if ($obj instanceof WidgetGroup) {
			$attrs = self::id_and_class($obj->identifier, $obj->group);
			print ("\n<div $attrs>");
			foreach($obj->contents as $widg) {
				$this->disp_widget($widg, null);
			}
			print ("\n</div>\n");
		} else if ($obj instanceof NotesNavigation) {
			$attrs = self::id_and_class($obj->identifier, array($obj->group, 'notes_nav'));
			echo "<div $attrs>\n";
			if ($obj->newest instanceof Hyperlink) {
				$obj->newest->description = '&lt;&lt;';
				$obj->newest->group = 'newest';
				$this->disp_widget($obj->newest, null);
			} else {
				echo '<span class="newest">&lt;&lt;</span>';
			}
			foreach (array('even_newer', 'newer', 'current', 'older', 'even_older') as $linkname) {
				if ($linkname == 'current') {
					echo '<span class="current">' . $obj->current->toHTML() . '</span>';
				} else if ($obj->$linkname instanceof Hyperlink) {
					echo $obj->$linkname->toHTML();
				} else {
					echo "<span class=\"$linkname disabled\"></span>";
				}
			}
			if ($obj->oldest instanceof Hyperlink) {
				$obj->oldest->description = '&gt;&gt;';
				$obj->newest->group = 'oldest';
				$this->disp_widget($obj->oldest, null);
			} else {
				echo '<span class="oldest">&gt;&gt;</span>';
			}
			echo "</div>\n";
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
		<li class="username"><span class="title">Username:</span> <span class="value"><?php echo $plan->username ?></span></li>
		<li class="lastupdated"><span class="title">Last Updated:</span> 
			<span class="value">
				<span class="long"><?php echo date(LONG_DATE_FORMAT, $plan->lastupdate) ?></span>
				<span class="short"><?php echo date(SHORT_DATE_FORMAT, $plan->lastupdate) ?></span>
			</span>
		</li>
		<li class="lastlogin"><span class="title">Last Login:</span> 
			<span class="value">
				<span class="long"><?php echo date(LONG_DATE_FORMAT, $plan->lastlogin) ?></span>
				<span class="short"><?php echo date(SHORT_DATE_FORMAT, $plan->lastlogin) ?></span>
			</span>
		</li>

		<li class="planname"><span class="title">Name:</span> <span class="value"><?php echo $plan->planname ?></span></li>
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
	<form action="<?php echo strtolower($box->action)
	?>" method="<?php echo strtolower($box->method)
?>"><div>
<textarea id="edit_textarea" rows="<?php echo $box->rows
?>" cols="<?php echo $box->columns
?>" 
		name="plan" onkeyup="javascript:checkPlanLength();">
<?php
print ($box->text->message);
?>
		</textarea><br />
		<input type="hidden" name="part" value="1" />
		<div id="edit_fill_meter">
			<div class="fill_bar"><div class="full_amount"></div></div>
			<div class="fill_percent">0%</div>
		</div>
<?php $this->disp_widget(new SubmitInput('Change Plan'), null); ?>
	</div></form></div>
<?php

	}
}
?>
