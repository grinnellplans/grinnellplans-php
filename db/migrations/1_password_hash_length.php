<?php
class IncreaseHashLength extends Doctrine_Migration_Base {
    public function up() {
        $this->changeColumn('accounts', 'password', '34', 'string');
    }
    public function down() {
        $this->changeColumn('accounts', 'password', '20', 'string');
    }
}
