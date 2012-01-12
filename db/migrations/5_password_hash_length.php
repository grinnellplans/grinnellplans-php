<?php
class IncreaseHashLengthSHA512 extends Doctrine_Migration_Base {
    public function up() {
        $this->changeColumn('accounts', 'password', '128', 'string');
    }
    public function down() {
        $this->changeColumn('accounts', 'password', '34', 'string');
    }
}
