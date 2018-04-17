<?php

class Zevioo_ExportOrder_Block_Shipping extends Mage_Core_Block_Html_Select
{
    /**
     * Prepare HTML output
     *
     * @return Mage_Core_Block_Html_Select
     */
    public function _toHtml()
    {
        $options = Mage::getSingleton('adminhtml/system_config_source_shipping_allowedmethods')
            ->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }

        return parent::_toHtml();
    }

    /**
     * Set field name
     *
     * @param string $value
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}