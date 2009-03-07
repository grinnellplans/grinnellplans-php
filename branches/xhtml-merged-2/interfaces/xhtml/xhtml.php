<?php
define('LONG_DATE_FORMAT', 'D F jS Y, g:i A');
define('SHORT_DATE_FORMAT', 'm-d-Y g:i A');
require_once('lib/savant/Savant3.php');

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
		$tpl = new Savant3();

		$tpl->page_title = $page->title;
		$tpl->stylesheets = $page->stylesheets;
		$tpl->scripts = $this->get_local_jsfiles($page);
		$tpl->body_id = 'planspage_' . strtolower($page->identifier);
		$tpl->body_class = strtolower($page->group);

		$tpl->mainpanel_template = $this->setup_mainpanel($page->mainpanel);
		$tpl->mainpanel_template = $this->setup_mainpanel($page->mainpanel);

		foreach ($page->contents as $w) {
			//echo get_class($w);
		}
		$tpl->contents = array_map(array($this, 'setup_widget'), $page->contents);

		$tpl->footer_template = $this->setup_footer($page->footer);

		$tpl->display('views/templates/XHTML/PlansPage.tpl.php');
	}
	protected function setup_mainpanel(MainPanel $panel) {
		$tpl = new Savant3();

		$tpl->linkhome_template = $this->setup_linkhome($panel->linkhome);
		$tpl->fingerbox_template = $this->setup_fingerbox($panel->fingerbox);
		$tpl->links_template = $this->setup_links($panel->links);
		$tpl->autoread_template = $this->setup_autoreads($panel->autoreads, $page->autoreadpriority);

		$tpl->setTemplate('views/templates/XHTML/mainpanel.tpl.php');
		return $tpl;
	}
	protected function setup_linkhome(Hyperlink $link) {
		$tpl = new Savant3();

		$tpl->href = htmlentities(html_entity_decode($link->href));
		$tpl->description = 'Home';

		$tpl->setTemplate('views/templates/XHTML/linkhome.tpl.php');
		return $tpl;
	}
	protected function setup_fingerbox(Form $finger) {
		$tpl = $this->setup_widget($finger);
		return $tpl;
	}
	protected function setup_links($links) {
		$tpl = $this->setup_widget($links);
		$tpl->setTemplate('views/templates/XHTML/Links.tpl.php');
		return $tpl;
	}
	protected function setup_autoreads(WidgetList $autoreads, $lvl) {
		$tpl = $this->setup_widget($autoreads);
		foreach($autoreads->contents as $i => $ar) {
			$t = $tpl->contents[$i];
			if ($t->priority == $lvl) {
				$t->list_attributes .= ' current';
			} else {
				$t->list_attributes .= ' notcurrent';
			}
		}
		$tpl->setTemplate('views/templates/XHTML/AutoReads.tpl.php');
		return $tpl;
	}
	protected function get_local_jsfiles($page) 
	{
		$jsfile_arr = array();
		// Populate the array with any files we need
		switch ($page->identifier) {
		case 'edit':
			$jsfile_arr[] = 'edit.js';
			break;
		}
		return $jsfile_arr;
	}
	protected function setup_footer($footer) 
	{
		$tpl = new Savant3();

		$tpl->doyouread_link_template = $this->setup_widget($footer->doyouread);
		$tpl->legal_template = $this->setup_widget($footer->legal);

		$tpl->setTemplate('views/templates/XHTML/Footer.tpl.php');
		return $tpl;
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
		if ($class) {
			$out[] = 'class="'.implode(' ', $class).'"';
		}
		return ' ' . implode(' ', $out);
	}

	protected function setup_widget(Widget $obj) {
		$tpl = new Savant3();

		if ($obj instanceof NotesTopic && $obj->summary) {
			$tpl->title_template = $this->setup_widget($obj->title);
			$tpl->updated = $obj->updated;
			$tpl->posts = $obj->posts;
			if (is_null($obj->firstposter)) {
				$tpl->firstposter_template = $this->setup_widget(new RegularText('User Deleted'));
			} else {
				$tpl->firstposter_template = $this->setup_widget($obj->firstposter);
			}
			if (is_null($obj->lastposter)) {
				$tpl->lastposter_template = $this->setup_widget(new RegularText('User Deleted'));
			} else {
				$tpl->lastposter_template = $this->setup_widget($obj->lastposter);
			}
			$tpl->setTemplate('views/templates/XHTML/NotesBoardTopic.tpl.php');
		} else if ($obj instanceof WidgetGroup) {

			$tpl->contents = array_map(array($this, 'setup_widget'), $obj->contents);

			if ($obj instanceof WidgetList) {
				foreach($tpl->contents as $i => $t) {
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
					$t->list_attributes = $class;
				}
			}

			if ($obj instanceof Form) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'form'));
				$tpl->method = strtolower($obj->method);
				$tpl->action = $obj->action;

				$tpl->setTemplate('views/templates/XHTML/Form.tpl.php');

				if ($obj instanceof EditBox) {
					$tpl->rows = $obj->rows;
					$tpl->columns = $obj->columns;
					$tpl->text = $obj->text->message;
					//TODO This line should be the responsibility of edit.php, not this object
					$tpl->otherinputs_template = $this->setup_widget(new HiddenInput('part', 1));
					$tpl->button_template = $this->setup_widget(new SubmitInput('Change Plan'));

					$tpl->setTemplate('views/templates/XHTML/EditBox.tpl.php');
				}
			} else if ($obj instanceof FormItemSet) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'formitemset'));
				$tpl->setTemplate('views/templates/XHTML/FormItemSet.tpl.php');
			} else if ($obj instanceof AutoRead) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'formitemset'));
				$tpl->level_link_template = $this->setup_widget($obj->link);
				// Setting the class this way is kinda sketchy, we should really handle classes better
				$tpl->level_link_template->tag_attributes .= ' class="autoreadlink"';
				$tpl->markasread_template = $this->setup_widget($obj->markasread_link);
				$tpl->markasread_template->tag_attributes .= " onclick =\"return confirm('Are you sure you\'d like to mark all the Plans on level " . $obj->priority . " as read?')\"";
				$tpl->setTemplate('views/templates/XHTML/AutoRead.tpl.php');
			} else if ($obj instanceof NotesBoard) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
				$tpl->setTemplate('views/templates/XHTML/NotesBoard.tpl.php');
			} else if ($obj instanceof WidgetList) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
				$tpl->setTemplate('views/templates/XHTML/WidgetList.tpl.php');
			} else if ($obj instanceof WidgetGroup) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
				$tpl->setTemplate('views/templates/XHTML/WidgetGroup.tpl.php');
			}
			return $tpl; //TODO remove

		} else if ($obj instanceof SubmitInput) {
			$tpl->tag_attributes = 'class="submitinput"';
			if ($obj->name) {
				$tpl->tag_attributes .= " name=\"$obj->name\"";
			}

			$tpl->text = $obj->value;

			$tpl->setTemplate('views/templates/std/FormButton.tpl.php');

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

			$tpl->label = $obj->title;
			$tpl->description = $obj->description;
			$tpl->prompt_id = $item_id;

			$tpl->type = $obj->type;
			$tpl->name = $obj->name;
			$tpl->value = $obj->value;

			if (isset($obj->checked)) {
				$tpl->checked = $obj->checked;
			}
			if (isset($obj->rows)) {
				$tpl->rows = $obj->rows;
			}
			if (isset($obj->cols)) {
				$tpl->cols = $obj->cols;
			}

			$tpl->tag_attributes .= ' class="form_prompt ' . strtolower(get_class($obj)) . '"';

			if ($obj instanceof HiddenInput) {
				$tpl->setTemplate('views/templates/std/FormInput.tpl.php');
			} else if ($obj instanceof TextInput || $obj instanceof TextareaInput || $obj instanceof PasswordInput) {
				$tpl->setTemplate('views/templates/XHTML/FormElement_title_prepend.tpl.php');
			} else {
				$tpl->setTemplate('views/templates/XHTML/FormElement.tpl.php');
			}

		} else if ($obj instanceof Hyperlink) {
			$tpl->href = htmlentities(html_entity_decode($obj->href));
			$tpl->description = $obj->description;
			$tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
			$tpl->setTemplate('views/templates/std/Hyperlink.tpl.php');

		} else if ($obj instanceof Secret) {
			$tpl->date = $obj->date;
			$tpl->secret_id = $obj->secret_id;
			$tpl->message = $obj->message;
			$tpl->setTemplate('views/templates/XHTML/Secret.tpl.php');

		} else if ($obj instanceof PlanContent) {
			$tpl->username = $obj->username;
			$tpl->lastupdate = $obj->lastupdate;
			$tpl->lastlogin = $obj->lastlogin;
			$tpl->planname = $obj->planname;
			$tpl->plan_template = $this->setup_widget($obj->text);

			if ($obj->addform) {
				$tpl->addform_template = $this->setup_widget($obj->addform);
			} else {
				$tpl->addform_template = new Savant3();
				$tpl->addform_template->setTemplate('views/templates/std/Empty.tpl.php');
			}

			$tpl->setTemplate('views/templates/XHTML/Plan.tpl.php');

		} else if ($obj instanceof PlanText) {
			$tpl->text = $obj->message;
			$tpl->setTemplate('views/templates/XHTML/PlanText.tpl.php');

		} else if ($obj instanceof HeadingText) {
			$tpl->tag = 'h' . $obj->sublevel;
			$tpl->text = $obj->message;
			$tpl->setTemplate('views/templates/std/GenericTag.tpl.php');

		} else if ($obj instanceof RegularText) {
			$tpl->tag = 'span';
			$tpl->text = $obj->message;
			$tpl->setTemplate('views/templates/std/GenericTag.tpl.php');

		} else if ($obj instanceof Text) {
			$tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
			$tpl->title = $obj->title;
			$tpl->message = $obj->message;
			$tpl->setTemplate('views/templates/XHTML/Text.tpl.php');

		} else if ($obj instanceof NotesPost) {
			$tpl->post_id = $obj->id;
			$tpl->date = $obj->date;
			$tpl->post_author_template = $this->setup_widget($obj->poster);
			$tpl->score = $obj->score;
			$tpl->votes = $obj->votes;
			$tpl->text = $obj->contents;

			$tpl->setTemplate('views/templates/XHTML/NotesPost.tpl.php');
		} else if ($obj instanceof NotesNavigation) {
			$tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'notes_nav'));
			if ($obj->newest instanceof Hyperlink) {
				$tpl->newest = $this->setup_widget($obj->newest);
				$tpl->newest->description = '&lt;&lt;';
			} else {
				$tpl->newest = new Savant3();
				$tpl->newest->setTemplate('views/templates/std/GenericTag.tpl.php');
				$tpl->newest->text = '&lt;&lt;';
				$tpl->newest->tag = 'span';
			}
			foreach (array('even_newer', 'newer', 'current', 'older', 'even_older') as $linkname) {
				if ($linkname == 'current') {
					$tpl->current = $this->setup_widget($obj->current);
				} else if ($obj->$linkname instanceof Hyperlink) {
					$tpl->$linkname = $this->setup_widget($obj->$linkname);
				} else {
					$tpl->$linkname = new Savant3();
					$tpl->$linkname->setTemplate('views/templates/std/GenericTag.tpl.php');
					$tpl->$linkname->text = '_';
					$tpl->$linkname->tag = 'span';
				}
			}
			if ($obj->oldest instanceof Hyperlink) {
				$tpl->oldest = $this->setup_widget($obj->oldest);
				$tpl->oldest->description = '&gt;&gt;';
			} else {
				$tpl->oldest = new Savant3();
				$tpl->oldest->setTemplate('views/templates/std/GenericTag.tpl.php');
				$tpl->oldest->text = '&gt;&gt;';
				$tpl->oldest->tag = 'span';
			}
			$tpl->setTemplate('views/templates/XHTML/NotesNavigation.tpl.php');
		}

		return $tpl;
	}

}
?>
