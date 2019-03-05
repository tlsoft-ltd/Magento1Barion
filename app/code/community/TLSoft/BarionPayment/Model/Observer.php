<?php
class TLSoft_BarionPayment_Model_Observer
{
	
	public function checkTransactions()
	{
		$otppayment = Mage::getModel('tlbarion/paymentmethod');
		$transactions=$otppayment->getTransModel()->getCollection()
						->joinOrderFull()
						->addFieldToFilter('payment_status',array('eq'=>'01'))
						//->addFieldToFilter('main_table.created_at',array('date'=>true,'lteq'=>Varien_Date::formatDate(Mage::getModel('core/date')->timestamp(time())-60)))//30 perc a lejárat!
						->load();
		$otphelper = $otppayment->otpHelper();
		foreach ($transactions as $transaction){
			$order = Mage::getModel('sales/order')->load($transaction->getOrderId());
			$result = $otphelper->processTransResult($order,$transaction);
			if($result=='success'){
				$otphelper->processOrderSuccess($order);
			}
			elseif($result=='fail'){
				if ($order->getId()&&$order->getState()!=Mage_Sales_Model_Order::STATE_CANCELED) {
					$order->cancel()->save();
				}
			}
			else{
				continue;
			}
		}
	}
	
}