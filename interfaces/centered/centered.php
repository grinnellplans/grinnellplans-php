<?php
require_once('interfaces/default/defaultinterface.php');

/**
 * Centered Interface
 * Old table-based Centered interface.
 */
class LegacyCenteredInterface extends LegacyDefaultInterface {

	public function setup_page(PlansPage $page)
	{
		$tpl = parent::setup_page($page);
		$tpl->rightpanel_template = $this->setup_rightpanel($page);
		$tpl->setTemplate('views/templates/legacy/PlansPage_centered.tpl.php');
		return $tpl;
	}
	protected function setup_rightpanel(PlansPage $page) {
		$tpl = new Plans_Savant3();
		$tpl->autoread_template = $this->setup_autoreads($page->mainpanel->autoreads, $page->autoreadpriority);
		$tpl->setTemplate('views/templates/legacy/Rightpanel.tpl.php');
		return $tpl;
	}
	protected function setup_mainpanel(PlansPage $page) {
		$tpl = new Plans_Savant3();

		$panel = $page->mainpanel;
		$tpl->panel = $panel;
		$tpl->links_template = $this->setup_links($panel->links);

		$tpl->setTemplate('views/templates/legacy/Mainpanel.tpl.php');
		return $tpl;
	}

}

global $my_interface_name;
$my_interface_name = 'LegacyCenteredInterface';
