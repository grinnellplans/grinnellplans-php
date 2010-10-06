<?php
require_once('functions-edit.php');

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Plans extends BasePlans
{
    public function setUp()
    {
        $this->hasMutator('edit_text', 'processText');
    }

    public function save(Doctrine_Connection $conn = null)
    {
      $conn = $conn ? $conn : $this->getTable()->getConnection();
      $conn->beginTransaction();
      try
      {
        $ret = parent::save($conn);
        $conn->commit();
        return $ret;
      }
      catch (Exception $e)
      {
         $conn->rollBack();
         throw $e;
      }
    }

    public function processText($text) {
        $text = $this->processDates($text);
        $this->_set('edit_text', $text);
        $html_text = cleanText($text);
        $this->_set('plan', $html_text);
    }

    protected function processDates($text) {
        $search = array('[date]', '[dnew]');
        $replace = array();
        $replace[] = '<b>' . date('l F j, Y. g:i A') . '</b>';
        $replace[] = '<b>' . date('F j, Y, l H:i') . '</b>';
        $text = str_replace($search, $replace, $text);
        return $text;
    }

}
