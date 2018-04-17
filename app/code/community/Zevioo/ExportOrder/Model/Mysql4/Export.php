<?php
class Zevioo_ExportOrder_Model_Mysql4_Export extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("exportorder/export", "order_id");
    }
}