<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseAvailLinks extends Doctrine_Record {
    public function setTableDefinition() {
        $this->setTableName('avail_links');
        $this->hasColumn('linknum', 'integer', 1, array('type' => 'integer', 'unsigned' => '1', 'primary' => true, 'autoincrement' => true, 'length' => '1'));
        $this->hasColumn('linkname', 'string', 128, array('type' => 'string', 'length' => '128'));
        $this->hasColumn('descr', 'string', 2147483647, array('type' => 'string', 'length' => '2147483647'));
        $this->hasColumn('html_code', 'string', 2147483647, array('type' => 'string', 'length' => '2147483647'));
        $this->hasColumn('static', 'string', 2147483647, array('type' => 'string', 'length' => '2147483647'));
    }
}
