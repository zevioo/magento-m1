<?php
class Zevioo_ExportOrder_Model_Observer
{
	/*
	 	POST	https://api.zevioo.com/main.svc/custpurchase
	 	{	
		 	"USR":	"demo_api",	
			"PSW":	"LBq2CyXokm27kq",	
		 	"OID":	"100008298",	
			"PDT":	"2017-08-20	09:24:30",	
			"DDT":	"2017-08-27	09:59:32",	
		 	"EML":	"xxxxx@gmail.com",	
		 	"FN":	"John",	
			"LN":	"Doe",	
		 	"ITEMS":	[{	
		 	 	"CD":	"12345322",	
		 	 	"EAN":	"002255887799",	
				"IMG":	"https:\/\/www.xyz.com\/product\/5\/2\/5201641711071_01_1.jpg",	
		 	 	"NM":	"Chromacare	System	Silver	Pure	Shampoo	300ml",	
		 	 	"PRC":	8.20,	
		 	 	"QTY":	1	
		 	},	{	
		 	 	"CD":	"1236987",	
		 	 	"EAN":	"0033665588444",	
		 	 	"IMG":	"https:\/\/www.xyz.com\/product\/5\/2\/5202236922896_01.jpg",	
		 	 	"NM":	"Cream	Color	12.89	Extra	Blond	Πλατινέ	Περλέ	Σαντρέ",	
		 	 	"PRC":	7.02,	
		 	 	"QTY":	2	
		 	},	{	
		 	 	"CD":	"193828219",	
		 	 	"EAN":	"0066191953313",	
		 	 	"IMG":	"https:\/\/www.xyz.com\/product\/5\/2\/5202236920182_01.jpg",	
		 	 	"NM":	"Cream	Color	00.18	Ασημί",	
		 	 	"PRC":	12.21,	
		 	 	"QTY":	1	
		 	}]	
		}	

		POST	https://api.zevioo.com/main.svc/cnlpurchase
		{	
			"USR":	"demo_api",	
			"PSW":	"LBq2CyXokm27kq",	
			"OID":	"A12345",	
			"CDT":	"2017-10-19	13:24:30"	
		}	
	*/

	public function ExportSuccessOrder(Varien_Event_Observer $observer)
	{

		$orderIds = $observer->getData('order_ids');
		$helper = Mage::helper("exportorder");
		if(!$helper->getEnable()) return $this;
		
        foreach($orderIds as $_orderId){
           	$order = Mage::getModel('sales/order')->load($_orderId);

           	if($order->getData('export_api_status') != NULL) continue;
           	$billingaddress = $order->getBillingAddress();
			try {  /* parameters can be change according to requirment */
				$getShippingMethod = $order->getShippingMethod();
				$delivery_date  = "";
				$deliveryDateDelay = $helper->getDeliveryDateDelay($getShippingMethod);
				if($deliveryDateDelay ){
					$delivery_date = new DateTime($order->getData('created_at'));
					$delivery_date ->modify("+$deliveryDateDelay days");
					$delivery_date = $delivery_date ->format("Y-m-d H:m:s");
				}
				
            	$params['OID'] = $order->getIncrementId();
            	$params['PDT'] = $order->getData('created_at');
            	$params['DDT'] = $delivery_date;
            	$params['EML'] = $billingaddress->getData('email');
            	$params['FN'] = $order->getData('customer_firstname');
            	$params['LN'] = $order->getData('customer_lastname');
            	$params['PC'] =  $billingaddress->getData('postcode');
            	$orderItems = $order->getAllVisibleItems();
            	foreach ($orderItems as $item) {
            		$productId = $item->getProductId();
            		$product = Mage::getModel('catalog/product')->load($productId);
        		 	$product_small_image_path = Mage::helper('catalog/image')->init($product, 'small_image')->resize(135);
            		$order_item['CD'] = $productId;
                	$order_item['EAN'] = $product->getSku();
                	$order_item['IMG'] = $product_small_image_path;
                	$order_item['NM'] =  $item->getName(); 
                	$order_item['PRC'] = $item->getPrice();
                	$order_item['QTY'] = $item->getQtyOrdered(); 
					$params['ITEMS'][] = $order_item;
            	}
            	$orderApiUrl = "https://api.zevioo.com/main.svc/custpurchase";
       			$response = $helper->callApi($orderApiUrl,$params);
       			$orderSendModel = Mage::getModel("exportorder/export");
       			$orderSendModel
		           			->setData('order_send_data',$response)
		           			->setData('order_request_data',json_encode($params))
		           			->setData('created_at',Mage::getModel('core/date')->date('Y-m-d H:i:s'))
		           			->setStatus(0)->save();
       			$order ->setData('export_api_status','New Order Data Sent to Api')->save();
    			Mage::log('Order has been sent to zevioo '.$response);
           	} catch (Exception $e) {
               	Mage::logException($e);
           	}
       	}
      	
        return $this;
	}

	public function ExportCancelOrder(Varien_Event_Observer $observer)
	{
		$helper = Mage::helper("exportorder");
		if(!$helper->getEnable()) return $this;
		$order = $observer->getEvent()->getOrder();
		$order = Mage::getModel('sales/order')->load($order->getId());
	   	if($order->getData('export_api_status') == 'New Order Data Sent to Api') 
	   	{
			$params['OID'] = $order->getIncrementId();
			$params['CDT'] = $order->getData('updated_at');
			$orderApiUrl = "https://api.zevioo.com/main.svc/cnlpurchase";
			$response = $helper->callApi($orderApiUrl,$params);
			$orderSendModel = Mage::getModel("exportorder/export");
			$orderSendModel
		   			->setData('order_send_data',$response)
		   			->setData('order_request_data',json_encode($params))
		   			->setData('created_at',Mage::getModel('core/date')->date('Y-m-d H:i:s'))
		   			->setStatus(1)->save();
			$order ->setData('export_api_status','Cancel Order Data Sent to Api')->save();
			Mage::log('Order has been sent to zevioo '.$response);
		}
	}
		
}
