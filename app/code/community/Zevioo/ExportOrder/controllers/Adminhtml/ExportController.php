<?php

class Zevioo_ExportOrder_Adminhtml_ExportController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('exportorder/export');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("exportorder/export")->_addBreadcrumb(Mage::helper("adminhtml")->__("Export  Manager"),Mage::helper("adminhtml")->__("Export Manager"));
				return $this;
		}
		public function indexAction() 
		{
			
			    $this->_title($this->__("ExportOrder"));
			    $this->_title($this->__("Manager Export"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("ExportOrder"));
				$this->_title($this->__("Export"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("exportorder/export")->load($id);
				if ($model->getId()) {
					Mage::register("export_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("exportorder/export");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Export Manager"), Mage::helper("adminhtml")->__("Export Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Export Description"), Mage::helper("adminhtml")->__("Export Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("exportorder/adminhtml_export_edit"))->_addLeft($this->getLayout()->createBlock("exportorder/adminhtml_export_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("exportorder")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("ExportOrder"));
		$this->_title($this->__("Export"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("exportorder/export")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("export_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("exportorder/export");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Export Manager"), Mage::helper("adminhtml")->__("Export Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Export Description"), Mage::helper("adminhtml")->__("Export Description"));


		$this->_addContent($this->getLayout()->createBlock("exportorder/adminhtml_export_edit"))->_addLeft($this->getLayout()->createBlock("exportorder/adminhtml_export_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("exportorder/export")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Export was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setExportData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setExportData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("exportorder/export");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('order_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("exportorder/export");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'export.csv';
			$grid       = $this->getLayout()->createBlock('exportorder/adminhtml_export_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'export.xml';
			$grid       = $this->getLayout()->createBlock('exportorder/adminhtml_export_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
