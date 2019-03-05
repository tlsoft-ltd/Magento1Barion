<?php
class TLSoft_BarionPayment_Model_Paymentstatus extends Mage_Core_Model_Abstract
{
	public function getStatuses()
	{
		return array(
            "00"=>Mage::helper("tlbarion")->__('Closed Unsuccessfully'),
            "01"=>Mage::helper("tlbarion")->__('Processing'),
			"02"=>Mage::helper("tlbarion")->__('Closed Successfully')
        );
	}
}