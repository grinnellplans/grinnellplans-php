<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseSubboard extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('subboard');
    $this->hasColumn('messageid', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'primary' => true, 'autoincrement' => true, 'length' => '2'));
    $this->hasColumn('threadid', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'default' => '0', 'notnull' => true, 'length' => '2'));
    $this->hasColumn('userid', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'default' => '0', 'notnull' => true, 'length' => '2'));
    $this->hasColumn('contents', 'string', 2147483647, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '2147483647'));
    $this->hasColumn('created', 'timestamp', 25, array('type' => 'timestamp', 'length' => '25'));
    $this->hasColumn('title', 'string', 128, array('type' => 'string', 'length' => '128'));
  }

}