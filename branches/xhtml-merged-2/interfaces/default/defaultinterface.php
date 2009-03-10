<?php
define('DATE_FORMAT', 'D F jS Y, g:i A');
require_once('lib/savant/Savant3.php');
require_once('interfaces/base.php');

/**
 * "Modern" Interface
 * The default Plans interface. Table-icious. Ick.
 */
class LegacyDefaultInterface extends BaseInterface {
	protected $page;

	public function display_page(PlansPage $page)
	{
		$this->page = $page;
		$tpl = new Savant3();

		$tpl->page_title = $page->title;
		$tpl->stylesheets = $page->stylesheets;
		$tpl->scripts = $this->get_local_jsfiles($page);
		$tpl->body_id = 'planspage_' . strtolower($page->identifier);
		$tpl->body_class = strtolower($page->group);

		if ($page->group == 'Preferences' && $page->identifier != 'autoreadedit') {
			$tpl->center = true;
		} else {
			$tpl->center = false;
		}

		$tpl->mainpanel_template = $this->setup_mainpanel($page);

		foreach ($page->contents as $w) {
			//echo get_class($w);
		}
		$tpl->contents = array_map(array($this, 'setup_widget'), $page->contents);

		$tpl->footer_template = $this->setup_footer($page->footer);

		$tpl->display('views/templates/legacy/PlansPage.tpl.php');
	}
	protected function setup_mainpanel(PlansPage $page) {
		$tpl = new Savant3();

		$panel = $page->mainpanel;
		$tpl->panel = $panel;
		$tpl->links_template = $this->setup_links($panel->links);
		$tpl->autoread_template = $this->setup_autoreads($panel->autoreads, $page->autoreadpriority);

		$tpl->setTemplate('views/templates/legacy/Mainpanel.tpl.php');
		return $tpl;
	}
	protected function setup_links($links) {
		$tpl = $this->setup_widget($links);
		foreach ($tpl->contents as $t) {
			$t->description = strtolower($t->description);
			$t->tag_attributes = ' class="main"';
		}
		$tpl->setTemplate('views/templates/legacy/Links.tpl.php');
		return $tpl;
	}
	protected function setup_autoreads(WidgetList $autoreads, $lvl)
	{
		$tpl = new Savant3();

		foreach ($autoreads->contents as $ar) {
			$t = new Savant3();
			$tpl->contents[] = $t;
			$t->level_link = $ar->link;
			$t->markasread_link = $ar->markasread_link;
			if ($ar->priority == $lvl) {
				$t->current = true;
			} else {
				$t->current = false;
			}
			$t->priority = $ar->priority;
			$t->names = $ar->contents;

			$t->setTemplate('views/templates/legacy/AutoRead.tpl.php');
		}
		$tpl->setTemplate('views/templates/legacy/AutoReads.tpl.php');
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

		$tpl->setTemplate('views/templates/legacy/Footer.tpl.php');
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
		$tpl = parent::setup_widget($obj);

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

			/*
		case 'Form':
			// treat certain forms differently
			if ($value->identifier == 'autoreadlistform') {
				foreach($value->contents as $item) {
					$special = ($item->type == 'hidden' || $item->type == 'submit');
					disp_widget($item, null);
					if (!$special) print('<BR>');
				}
				break;
			}

			break;
			 */
			if ($obj instanceof Form) {
				//check, mostly
				foreach ($obj->contents as $i => $item) {
					if ($item instanceof SubmitInput) {
						$tpl->submit_button = $item;
					}
				}

				$tpl->setTemplate('views/templates/legacy/Form.tpl.php');

				if ($this->page->identifier == 'planname') {
					$tpl = $this->oneline_form($obj, $tpl);
				}
				if ($obj instanceof EditBox) {
					$tpl->setTemplate('views/templates/legacy/EditBox.tpl.php');
				}
			} else if ($obj instanceof FormItemSet) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, array($obj->group, 'formitemset'));
				foreach ($tpl->contents as $template) {
					$template->setTemplate('views/templates/legacy/FormElement_no_row.tpl.php');
				}
				$tpl->setTemplate('views/templates/legacy/FormItemSet.tpl.php');
			} else if ($obj instanceof NotesBoard) {
				$tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
				$tpl->setTemplate('views/templates/XHTML/NotesBoard.tpl.php');
			} else if ($obj instanceof WidgetList) {
				//check
				$tpl->tag_attributes = self::id_and_class($obj->identifier, $obj->group);
				$tpl->setTemplate('views/templates/XHTML/WidgetList.tpl.php');
				// The Preferences page
				if ($this->page->identifier == 'prefs') {
					$part2 = false;
					foreach ($obj->contents as $i => $item) {
						$t = $tpl->contents[$i];
						if ($part2) {
							$t->tag_attributes = ' class="lev2"';
						} else if ($item instanceof HeadingText) {
							$t->tag_attributes = ' class="main"';
							$t->tag = 'p';
							$t->setTemplate('views/templates/std/GenericTag.tpl.php');
							$part2 = true;
						} else {
							$t->tag_attributes = ' class="main"';
						}
					}
					$tpl->setTemplate('views/templates/legacy/WidgetList.tpl.php');
				}
				// The autoread management pages
				else if ($value->identifier == 'autoread_alphabet') {
					$tpl->setTemplate('views/templates/XHTML/WidgetGroup.tpl.php');
				}
			} else if ($obj instanceof WidgetGroup) {
				//check
				$tpl->setTemplate('views/templates/XHTML/WidgetGroup.tpl.php');
			}
			return $tpl; //TODO remove

		} else if ($obj instanceof SubmitInput) {
			// We handle this elsewhere, so print nothing here
			$tpl->setTemplate('views/templates/std/Empty.tpl.php');
		} else if ($obj instanceof FormItem) {

			if ($obj instanceof HiddenInput) {
				$tpl->setTemplate('views/templates/std/FormInput.tpl.php');
			} else if ($obj instanceof TextInput || $obj instanceof TextareaInput || $obj instanceof PasswordInput) {
				$tpl->setTemplate('views/templates/legacy/FormElement_title_prepend.tpl.php');
			} else {
				$tpl->setTemplate('views/templates/legacy/FormElement.tpl.php');
			}

		} else if ($obj instanceof Hyperlink) {
			//check
			$tpl->description = strtolower($obj->description);
			$tpl->setTemplate('views/templates/std/Hyperlink.tpl.php');

		} else if ($obj instanceof Secret) {
			$tpl->date = $obj->date;
			$tpl->secret_id = $obj->secret_id;
			$tpl->message = $obj->message;
			$tpl->setTemplate('views/templates/XHTML/Secret.tpl.php');

		} else if ($obj instanceof PlanContent) {
			//check
			if ($obj->addform) {
				$tpl->addform_present = true;
				$tpl->addform_template = $this->oneline_form($obj->addform, $tpl->addform_template);
				$tpl->addform_template->setTemplate('views/templates/legacy/addform.tpl.php');
			} else {
				$tpl->addform_present = false;
			}
			$tpl->setTemplate('views/templates/legacy/Plan.tpl.php');
		} else if ($obj instanceof PlanText) {
			//check
			$tpl->setTemplate('views/templates/legacy/PlanText.tpl.php');

		} else if ($obj instanceof HeadingText) {
			$tpl->tag = 'h' . ($obj->sublevel + 1);
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
				$tpl->navigable['newest'] = true;
			} else {
				$tpl->newest = new Savant3();
				$tpl->newest->setTemplate('views/templates/std/GenericTag.tpl.php');
				$tpl->newest->text = '&lt;&lt;';
				$tpl->newest->tag = 'span';
				$tpl->navigable['newest'] = false;
			}
			foreach (array('even_newer', 'newer', 'current', 'older', 'even_older') as $linkname) {
				if ($obj->$linkname instanceof Hyperlink || $linkname == 'current') {
					$tpl->$linkname = $this->setup_widget($obj->$linkname);
					$tpl->navigable[$linkname] = true;
				} else {
					$tpl->$linkname = new Savant3();
					$tpl->$linkname->setTemplate('views/templates/std/GenericTag.tpl.php');
					$tpl->$linkname->text = '_';
					$tpl->$linkname->tag = 'span';
					$tpl->navigable[$linkname] = false;
				}
			}
			if ($obj->oldest instanceof Hyperlink) {
				$tpl->oldest = $this->setup_widget($obj->oldest);
				$tpl->oldest->description = '&gt;&gt;';
				$tpl->navigable['oldest'] = true;
			} else {
				$tpl->oldest = new Savant3();
				$tpl->oldest->setTemplate('views/templates/std/GenericTag.tpl.php');
				$tpl->oldest->text = '&gt;&gt;';
				$tpl->oldest->tag = 'span';
				$tpl->navigable['oldest'] = false;
			}
			$tpl->setTemplate('views/templates/XHTML/NotesNavigation.tpl.php');
		}

		return $tpl;
	}

	/**
	 * Consistency be damned, we're putting these forms on one line!
	 *
	 * Yee-haw!
	 */
	private function oneline_form($form, $form_tpl) {
		$inputs = array();
		foreach ($form->contents as $o) {
			if ($o instanceof SubmitInput) {
				// do nothing
			} else if ($o instanceof FormItem) {
				$inputs[] = $o;
			} else if ($o instanceof FormItemSet) {
				foreach ($o->contents as $_o)
					$inputs[] = $_o;
			}
		}
		$form_tpl->inputs = $inputs;
		$form_tpl->setTemplate('views/templates/legacy/Form_oneline.tpl.php');
		return $form_tpl;
	}

}

global $my_interface_name;
$my_interface_name = 'LegacyDefaultInterface';
