<?php
class TLSoft_BarionPayment_Model_Mysql4_Transactions_History extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('tlbarion/transactions_history','entity_id');
	}
	
	public function loadByTransId($object, $transid)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from($this->getMainTable());
		
		$select->where($this->getMainTable().'.transaction_id =?', $transid);
        
		$id=$this->_getReadAdapter()->fetchAll($select);
		
		if ($id)
			$this->load($object,$id);
		return $this;
    }
	
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        //$object->setCreatedAt(Varien_Date::now());
        return $this;
    }
	
}