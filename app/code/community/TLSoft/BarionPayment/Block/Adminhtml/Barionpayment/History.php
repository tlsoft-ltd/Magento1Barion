<?php
class TLSoft_BarionPayment_Block_Adminhtml_Barionpayment_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	
    public function __construct()
    {
		$this->_blockGroup = 'tlbarion';
        $this->_controller = 'adminhtml_barionpayment_history';
        $this->_headerText = Mage::helper('tlbarion')->__('Transactions History');
        parent::__construct();
		$this->_addButton('bacbutton', array(
            'label'     => Mage::helper('tlbarion')->__('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/barionpayment/index').'\')',
            'class'     => 'scalable back'
        ), 0, 100, 'header', 'header');
		$this->_removeButton('add');
	   
    }

    public function getCreateUrl()
    {
       return "";
    }

}
