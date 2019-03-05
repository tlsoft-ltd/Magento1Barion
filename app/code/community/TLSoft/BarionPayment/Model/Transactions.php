<?php
class TLSoft_BarionPayment_Model_Transactions extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
		parent::_construct();
        $this->_init('tlbarion/transactions');
    }
	
	public function loadByRealOrderid($transid)
    {
        $this->_getResource()->loadByRealOrderid($this,$transid);
        return $this;
    }
	
	public function loadByOrderId($orderid)
    {
        $this->_getResource()->loadByOrderId($this,$orderid);
        return $this;
    }
	
}