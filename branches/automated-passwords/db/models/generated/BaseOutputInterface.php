<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseOutputInterface extends Doctrine_Record {
    public function setTableDefinition() {
        $this->setTableName('interface');
        $this->hasColumn('interface', 'integer', 1, array('type' => 'integer', 'unsigned' => '1', 'primary' => true, 'autoincrement' => true, 'length' => '1'));
        $this->hasColumn('path', 'string', 128, array('type' => 'string', 'length' => '128'));
        $this->hasColumn('descr', 'string', 255, array('type' => 'string', 'length' => '255'));
    }
}
