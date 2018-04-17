<?php


class Zevioo_ExportOrder_Block_Adminhtml_Export extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_export";
	$this->_blockGroup = "exportorder";
	$this->_headerText = Mage::helper("exportorder")->__("Export Manager");
	$this->_addButtonLabel = Mage::helper("exportorder")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}