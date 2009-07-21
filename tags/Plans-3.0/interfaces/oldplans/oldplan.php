<?php
require_once('interfaces/default/defaultinterface.php');

/**
 * "Old Term" Interface
 * Some relic of Plans of yore.
 */
class LegacyOldTermInterface extends LegacyDefaultInterface {

	protected function setup_links($links) {
		$tpl = parent::setup_links($links);
		$tpl->setTemplate('views/templates/legacy/Links_oldterm.tpl.php');
		return $tpl;
	}
	protected function setup_autoreads(WidgetList $autoreads, $lvl)
	{
		$tpl = parent::setup_autoreads($autoreads, $lvl);
		$tpl->setTemplate('views/templates/legacy/AutoReads_oldterm.tpl.php');
		return $tpl;
	}

}

global $my_interface_name;
$my_interface_name = 'LegacyOldTermInterface';
