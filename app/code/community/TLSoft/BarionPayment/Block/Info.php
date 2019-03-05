<?php

class TLSoft_BarionPayment_Block_Info extends Mage_Payment_Block_Info
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tlsoft/tlbarion/info.phtml');
    }
	
	public function toPdf()
    {
        $this->setTemplate('tlsoft/tlbarion/pdf/info.phtml');
        return $this->toHtml();
    }
	
	public function getTransactions()
	{
		$model=Mage::getModel('tlbarion/transactions');
		$collection=$model->getCollection();
		return $collection;
	}
}