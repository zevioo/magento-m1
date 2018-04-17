<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table order_export_information(
order_id int not null auto_increment,
order_send_data text,
status int(6),
primary key(order_id));
  
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 