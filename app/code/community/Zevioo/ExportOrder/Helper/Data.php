<?php
class Zevioo_ExportOrder_Helper_Data extends Mage_Core_Helper_Abstract
{

 	/*
	 	POST	https://api.zevioo.com/main.svc/custpurchase{	
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
	public function callApi($apiURL,$data){
		$data['USR'] = $this->getUsername();
		$data['PSW'] = $this->getPassword();
		$data_string = json_encode($data);
		Mage::log("raw data: $data_string",null,"apimanager.log");
		$ch = curl_init($apiURL); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		/*curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);*/
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			die('Couldn\'t send request: '.curl_error($ch).'\n');
		}
		else
		{
			$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($resultStatus == 200)
			{
				if ($this->getDebug()) Mage::log("raw data: $result",null,"apimanager.log");
				return  $result;
			}
			elseif($resultStatus == 401)
			{
				Mage::log( "Request failed: UNAUTHORISED: $resultStatus",null,"apimanager.log");
			}
			else
			{
				Mage::log( "Request failed: HTTP status code: $resultStatus",null,"apimanager.log");
			}
		}
		curl_close($ch);
		return $this;
	}
	public function getDeliveryDateDelay($shippingMethod)
	{
		$delay = Mage::getStoreConfig('export_api/module_config/delivery_shipping');
		$delay = unserialize($delay);
		
		if (is_array($delay) || sizeof($delay)) {
    		foreach ($delay as $row) {
		    	if ($shippingMethod == $row['shipping_id'])
		    		{ 
		    			return $row['delivery_delay'];
		    		}
		    }
		}
		return $delay = null;
	}
	public function getEnable()
    {
       return  Mage::app()->getWebsite()->getConfig('export_api/module_config/enable');
    }
	public function getDebug()
	{
		return	Mage::app()->getWebsite()->getConfig('export_api/api_config/debug');
	}
	public function getUsername()
	{
		return	Mage::app()->getWebsite()->getConfig('export_api/api_config/username');
	}
	public function getPassword()
	{
		return	Mage::app()->getWebsite()->getConfig('export_api/api_config/password');
	}
}
	 