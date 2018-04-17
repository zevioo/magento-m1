<?php

class Zevioo_ExportOrder_Block_Adminhtml_Export_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("exportGrid");
				$this->setDefaultSort("order_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("exportorder/export")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("order_id", array(
				"header" => Mage::helper("exportorder")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "order_id",
				));
                
				$this->addColumn("order_send_data", array(
				"header" => Mage::helper("exportorder")->__("Order Send Data"),
				"index" => "order_send_data",
				));
				$this->addColumn("order_request_data", array(
				"header" => Mage::helper("exportorder")->__("Order Send Data"),
				"index" => "order_request_data",
				));
				$this->addColumn("created_at", array(
				"header" => Mage::helper("exportorder")->__("Order Send Data"),
				"index" => "created_at",
				));
				$this->addColumn('status', array(
				'header' => Mage::helper('exportorder')->__('status'),
				'index' => 'status',
				'type' => 'options',
				'width' => '120px',
				'options'=>Zevioo_ExportOrder_Block_Adminhtml_Export_Grid::getOptionArray1(),				
				));
				
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('order_id');
			$this->getMassactionBlock()->setFormFieldName('order_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_export', array(
					 'label'=> Mage::helper('exportorder')->__('Remove Export'),
					 'url'  => $this->getUrl('*/adminhtml_export/massRemove'),
					 'confirm' => Mage::helper('exportorder')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray1()
		{
            $data_array=array(); 
			$data_array[0]='Order Success';
			$data_array[1]='Order Cancel';
            return($data_array);
		}
		static public function getValueArray1()
		{
            $data_array=array();
			foreach(Zevioo_ExportOrder_Block_Adminhtml_Export_Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}