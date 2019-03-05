<?php
class TLSoft_BarionPayment_Model_Mysql4_Transactions_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	 public function _construct()
    {
        parent::_construct();
        $this->_init('tlbarion/transactions');
    }
	
	public function addStoreFilter($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        $this->getSelect()
        ->where('main_table.store_id in (?)', array(0, $store));

        return $this;
    }
	
	public function joinOrder()
	{
		$this->getSelect()
		->joinLeft(array('order_table' => $this->getTable('sales/order')),
                'order_table.entity_id = main_table.order_id',array('realorderid'=>'increment_id'));
		return $this;
	}
	
	public function joinOrderFull()
	{
		$this->getSelect()
		->joinLeft(array('order_table' => $this->getTable('sales/order')),
                'order_table.entity_id = main_table.order_id',array('status'=>'status'));
		return $this;
	}

}