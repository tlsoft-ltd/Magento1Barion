<?php
class TLSoft_BarionPayment_Model_Transactions_History extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
		parent::_construct();
        $this->_init('tlbarion/transactions_history');
    }
	
	public function loadByTransid($transid)
    {
        $this->_getResource()->loadByTransId($this,$transid);
        return $this;
    }
}