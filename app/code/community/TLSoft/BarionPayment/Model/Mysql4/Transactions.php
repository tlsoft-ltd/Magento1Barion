<?php
class TLSoft_BarionPayment_Model_Mysql4_Transactions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('tlbarion/transactions','entity_id');
	}
	
	public function loadByRealOrderid($object, $transid)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from($this->getMainTable());
		
		$select->where($this->getMainTable().'.real_orderid =?', $transid);
        
		$id=$this->_getReadAdapter()->fetchOne($select);
		
		if ($id)
			$this->load($object,$id);
		return $this;
    }
	
	public function loadByOrderId($object, $orderid)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from($this->getMainTable());
		
		$select->where($this->getMainTable().'.order_id =?', $orderid)
				->order($this->getMainTable().'.entity_id DESC');
        
		$id=$this->_getReadAdapter()->fetchOne($select);
		
		if ($id)
			$this->load($object,$id);
		return $this;
    }
	
}