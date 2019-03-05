<?php

class TLSoft_BarionPayment_RedirectionController extends Mage_Core_Controller_Front_Action
{
	public function redirectAction()
	{
		$session = Mage::getSingleton('checkout/session');
        $session->setBarionPaymentPaymentmethodQuoteId($session->getQuoteId());
		$tlotp = Mage::getModel('tlbarion/paymentmethod');
		$otpurldata=$tlotp->getOtpUrl();
		if($otpurldata!=false){
			$session->setBarionUrlData($otpurldata);
			$this->getResponse()->setBody($this->getLayout()->createBlock('tlbarion/redirection')->toHtml());
		}
		else{
			$this->_redirect('tlbarion/redirection/cancel', array('_secure'=>true));
		}
	}
 
	public function respondAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$otppayment = Mage::getModel('tlbarion/paymentmethod');
		$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
		if($order->getId()){
			$otphelper = $otppayment->otpHelper();
			$otpdata=$otphelper->processTransResult();
			if ($otpdata=="success"){
					$otphelper->processOrderSuccess($order);
					$this->_redirect('tlbarion/redirection/success', array('_secure'=>true));
				}
			elseif($otpdata=="fail"){
				$this->_redirect('tlbarion/redirection/cancel', array('_secure'=>true));
			}
			else{
				$this->_redirect('tlbarion/redirection/cancel', array('_secure'=>true));
			}
		}
		else{
			$this->_redirect('tlbarion/redirection/cancel', array('_secure'=>true));
		}
	}
	
	public function successAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getBarionPaymentPaymentmethodQuoteId(true));
		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
		$otppayment = Mage::getModel('tlbarion/paymentmethod');
		$helper=$otppayment->otpHelper();
		//$success = $helper->__("Credit card payment was successful.");
		//$session->addSuccess($success);
		$this->_redirect('checkout/onepage/success', array('_secure'=>true));
	}
	
	public function cancelAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
		if ($order->getId()&&$order->getState()!=Mage_Sales_Model_Order::STATE_CANCELED) {
			$order->cancel()->save();
			$lastQuoteId=Mage::getSingleton('checkout/session')->getLastQuoteId();
			$quote = Mage::getModel('sales/quote')->load($lastQuoteId);
			$quote->setIsActive(true)->save();
		}
		/*$session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getBarionPaymentPaymentmethodQuoteId(true));*/
        $this->_redirect('checkout/onepage/failure');
	}
 
}
?>