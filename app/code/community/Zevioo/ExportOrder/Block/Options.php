<?php
class Zevioo_ExportOrder_Block_Options extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer;

    /**
     * Fix for ignored "depends enabled"
     * See: https://magento.stackexchange.com/questions/15500/configuration-depends-with-front-and-backend-model
     */
    public function _toHtml()
    {
        return '<div id="' . $this->getElement()->getId(). '">' . parent::_toHtml() . '</div>';
    }

    public function _prepareToRender()
    {
        $this->addColumn('shipping_id', array(
            'label' => Mage::helper('exportorder')->__('Shipping Methods'),
            'renderer' => $this->_getRenderer(),
        ));
        $this->addColumn('delivery_delay', array(
            'label' => Mage::helper('exportorder')->__('Delivery Delay in Days'),
            'style' => 'width:100px',
            'class' => 'validate-zero-or-greater input-text',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('exportorder')->__('Add');
    }

    protected function _getRenderer()
    {   
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                'exportorder/shipping',
                '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer()
                ->calcOptionHash($row->getData('shipping_id')),
            'selected="selected"'
        );
    }
}