<?php

class EmailLength extends Doctrine_Migration_Base
{
	public function up()
	{
		$this->changeColumn('accounts', 'email', '255', 'string');
	}

	public function down() {
		$this->changeColumn('accounts', 'email', '64', 'string');
	}
}
