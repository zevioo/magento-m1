<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "export_api_status", array("type"=>"text"));
$installer->endSetup();
	 