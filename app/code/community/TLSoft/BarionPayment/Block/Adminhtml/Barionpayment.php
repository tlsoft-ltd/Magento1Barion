<?php
class TLSoft_BarionPayment_Block_Adminhtml_Barionpayment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	
    public function __construct()
    {
		$this->_blockGroup = 'tlbarion';
        $this->_controller = 'adminhtml_barionpayment';
        $this->_headerText = Mage::helper('tlbarion')->__('Transactions');
        parent::__construct();
		$this->_removeButton('add');
	   
    }

    public function getCreateUrl()
    {
       return "";
    }

}
