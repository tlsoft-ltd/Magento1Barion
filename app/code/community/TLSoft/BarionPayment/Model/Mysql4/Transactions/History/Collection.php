<?php
class TLSoft_BarionPayment_Model_Mysql4_Transactions_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	 public function _construct()
    {
        parent::_construct();
        $this->_init('tlbarion/transactions_history');
    }
	
	public function addToSort($attribute,$dir="ASC")
	{
		$this->getSelect()->order($attribute.' '.$dir);
		return $this;
	}
}