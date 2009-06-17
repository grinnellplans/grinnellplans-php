<?php
define('LONG_DATE_FORMAT', 'D F jS Y, g:i A');
define('SHORT_DATE_FORMAT', 'm-d-Y g:i A');
require_once('lib/savant/Savant3.php');
require_once('interfaces/base.php');

/**
 * Gives Plans a long-needed bump into the world of CSS.
 *
 * This version is fairly
 * sparse, for flexibility combined with readable and not-bloated code.  There
 * may be a more bloated version for fancy-schmancy stylesheets appearing later.
 */
class TablelessInterface extends BaseInterface {
	public function display_page(PlansPage $page)
	{
		$tpl = parent::setup_page($page);
		$tpl->display('views/templates/tableless/PlansPage.tpl.php');
	}
	protected function setup_mainpanel(PlansPage $page) {
		$tpl = parent::setup_mainpanel($page);
		$tpl->setTemplate('views/templates/tableless/mainpanel.tpl.php');
		return $tpl;
	}
	protected function setup_linkhome(Hyperlink $link) {
		$tpl = parent::setup_linkhome($link);
		$tpl->setTemplate('views/templates/tableless/linkhome.tpl.php');
		return $tpl;
	}
	protected function setup_links(WidgetList $links) {
		$tpl = $this->setup_widget($links);
		$tpl->setTemplate('views/templates/tableless/Links.tpl.php');
		return $tpl;
	}
	protected function setup_autoreads(WidgetList $autoreads, $lvl) {
		$tpl = parent::setup_autoreads($autoreads, $lvl);
		$tpl->setTemplate('views/templates/tableless/AutoReads.tpl.php');
		return $tpl;
	}
	protected function setup_footer($footer) 
	{
		$tpl = parent::setup_footer($footer);
		$tpl->setTemplate('views/templates/tableless/Footer.tpl.php');
		return $tpl;
	}

	protected function setup_widget(Widget $obj) {
		$tpl = parent::setup_widget($obj);

		if ($obj instanceof NotesTopic && $obj->summary) {
			$tpl->setTemplate('views/templates/tableless/NotesBoardTopic.tpl.php');
		} else if ($obj instanceof WidgetGroup) {
			if ($obj instanceof Form) {
				$tpl->setTemplate('views/templates/tableless/Form.tpl.php');
				if ($obj instanceof EditBox) {
					$tpl->setTemplate('views/templates/tableless/EditBox.tpl.php');
				}
			} else if ($obj instanceof FormItemSet) {
				$tpl->setTemplate('views/templates/tableless/FormItemSet.tpl.php');
			} else if ($obj instanceof AutoRead) {
				// Setting the class this way is kinda sketchy, we should really handle classes better
				$tpl->level_link_template->tag_attributes .= ' class="autoreadlink"';
				$tpl->markasread_template->tag_attributes .= " onclick =\"return confirm('Are you sure you\'d like to mark all the Plans on level " . $obj->priority . " as read?')\"";
				$tpl->setTemplate('views/templates/tableless/AutoRead.tpl.php');
			} else if ($obj instanceof NotesBoard) {
				$tpl->setTemplate('views/templates/tableless/NotesBoard.tpl.php');
			} else if ($obj instanceof NotesTopic) {
				$tpl->setTemplate('views/templates/tableless/NotesTopic.tpl.php');
			} else if ($obj instanceof WidgetList) {
				$tpl->setTemplate('views/templates/tableless/WidgetList.tpl.php');
			} else if ($obj instanceof WidgetGroup) {
				$tpl->setTemplate('views/templates/tableless/WidgetGroup.tpl.php');
			}

		} else if ($obj instanceof SubmitInput) {
			$tpl->tag_attributes = 'class="submitinput"';
			$tpl->setTemplate('views/templates/std/FormButton.tpl.php');

		} else if ($obj instanceof FormItem) {

			$tpl->tag_attributes .= ' class="form_prompt ' . strtolower(get_class($obj)) . '"';

			if ($obj instanceof HiddenInput) {
				$tpl->setTemplate('views/templates/std/FormInput.tpl.php');
			} else if ($obj instanceof TextInput || $obj instanceof TextareaInput || $obj instanceof PasswordInput) {
				$tpl->setTemplate('views/templates/tableless/FormElement_title_prepend.tpl.php');
			} else {
				$tpl->setTemplate('views/templates/tableless/FormElement.tpl.php');
			}

		} else if ($obj instanceof Secret) {
			$tpl->setTemplate('views/templates/tableless/Secret.tpl.php');

		} else if ($obj instanceof PlanContent) {
			if ($obj->addform) {
				$tpl->addform_template = $this->setup_widget($obj->addform);
			} else {
				$tpl->addform_template = new Plans_Savant3();
				$tpl->addform_template->setTemplate('views/templates/std/Empty.tpl.php');
			}
			$tpl->setTemplate('views/templates/tableless/Plan.tpl.php');

		} else if ($obj instanceof PlanText) {
			$tpl->setTemplate('views/templates/tableless/PlanText.tpl.php');

		} else if ($obj instanceof HeadingText) {
			// Do nothing, already handled

		} else if ($obj instanceof RegularText) {
			// Do nothing, already handled

		} else if ($obj instanceof Text) {
			$tpl->setTemplate('views/templates/tableless/Text.tpl.php');

		} else if ($obj instanceof NotesPost) {
			$tpl->setTemplate('views/templates/tableless/NotesPost.tpl.php');
		} else if ($obj instanceof NotesNavigation) {
			$tpl->setTemplate('views/templates/tableless/NotesNavigation.tpl.php');
		}

		return $tpl;
	}

}

global $my_interface_name;
$my_interface_name = 'tablelessInterface';

?>
