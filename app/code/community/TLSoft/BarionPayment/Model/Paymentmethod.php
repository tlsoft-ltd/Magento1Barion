<?php

class TLSoft_BarionPayment_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract
{
	protected $_code					= 'tlbarion';
	protected $_isGateway               = true;
	protected $_canCapture              = true; //capture
	protected $_canAuthorize            = false; //authorize
	protected $_canVoid					= false;
	protected $_canRefund               = false;//refund
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = false;
	protected $_canUseForMultishipping  = false;
	protected $_ordercurrency			= "";//order's currency code
	protected $_canRefundInvoicePartial = false;
	
	protected $_infoBlockType = 'tlbarion/info';
	protected $_formBlockType = 'tlbarion/form';

	public function canUseForCurrency($currencyCode)
    {//pénznem ellenõrzése
		if ($currencyCode == "HUF" || $currencyCode == "EUR" || $currencyCode == "USD"){
			$session=Mage::getSingleton('checkout/session');
			$session->setBarioncurrency($currencyCode);
			return true;
		}
		else {
			return false;
		}
    }
	
	public function canUseCheckout()
	{//ha nincs fent a certifikát fájl
		$helper=$this->otpHelper();
		$storeid = Mage::app()->getStore()->getStoreId();
		/*$cert=$helper->getOtpKeyFile($storeid);

		if (empty($cert)){
			return false;
		}else{
			return true;
		}*/
		
		return true;
	}
	
	public function initialize($paymentAction, $stateObject)
    {
		$state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }
	
	public function getOrderPlaceRedirectUrl()
	{//átirányítás az unicreditre-re vivõ oldalra
		return Mage::getUrl('tlbarion/redirection/redirect', array('_secure' => true));
	}
	
	public function otpHelper()
	{
		return Mage::helper('tlbarion');
	}
	
	public function getOtpUrl()
	{//redirect url to k&h site
		$helper = $this->otpHelper();
		$order = $helper->getCurrentOrder();
		if(!$order->getId())
			return false;
		$storeid = $order->getStoreId();
		$email = $helper->getEmail($storeid);
		$session = Mage::getSingleton('checkout/session');
		$currency=$order->getOrderCurrencyCode();//GET Currency Code
		$ordertotal = round($order->getGrandTotal());

		$locale = $helper->checkLocalCode();
		$lastorderid = $order->getIncrementId();
		
		$products = array();
		$items = $order->getAllVisibleItems();
		$i=0;
		foreach($items as $item){
			$products[$i]["Name"]=$item->getName();
			$products[$i]["Description"]=$item->getName();
			$products[$i]["Quantity"]=round($item->getQtyOrdered());
			$products[$i]["Unit"]="db";
			$products[$i]["UnitPrice"]=round($item->getPriceInclTax());
			$products[$i]["ItemTotal"]=round($item->getRowTotalInclTax());
			$i++;
		}
		$shipping = $order->getShippingInclTax();
		if($shipping>0){
			$products[$i]["Name"]=$order->getShippingDescription();
			$products[$i]["Description"]=$order->getShippingDescription();
			$products[$i]["Quantity"]=1;
			$products[$i]["Unit"]="db";
			$products[$i]["UnitPrice"]=round($shipping);
			$products[$i]["ItemTotal"]=round($shipping);
		}

		$header = array("POSKey"=>$helper->getShopId($storeid),'PaymentType' => 'Immediate','PaymentWindow' => '00:30:00','GuestCheckOut' => true,'FundingSources' => Array('All'),'PaymentRequestId' => $lastorderid,'RedirectUrl' => Mage::getBaseUrl()."tlbarion/redirection/respond/",'OrderNumber'=>$lastorderid,"currency"=>$currency,"locale"=>"hu-HU","Transactions"=>array(array("POSTransactionId"=>$lastorderid,"Payee"=>$email,"Total"=>$ordertotal,"Items"=>$products)));//'CallbackUrl' => Mage::getBaseUrl()."tlbarion/redirection/respond/",
		
		$products="";

		$json = json_encode($header);

		$transid=$this->saveTrans(array('real_orderid'=>$lastorderid,'order_id'=>$order->getId(),'application_id'=>$helper->getShopId($storeid),'amount'=>$ordertotal,'ccy'=>$currency,'store_id'=>$storeid,'payment_status'=>"01",'created_at'=>now()));
		$this->saveTransHistory(array('transaction_id'=>$transid,'created_at'=>now()));
		$result = $helper->callCurl($json,$storeid);
		$resultarray = "";
		if($result!=false){
			$resultarray = json_decode($result,true);
			if(array_key_exists("PaymentId",$resultarray)){
				$this->updateTrans(array("bariontransactionid"=>$resultarray["Transactions"][0]["TransactionId"]),$transid);
				$url=$helper->getRedirectUrl($storeid);
				return $url."?id=".$resultarray["PaymentId"];
			}
			else{
				foreach($resultarray["Errors"] as $error){
				$this->saveTransHistory(array('created_at'=>now(),'transaction_id'=>$transid,"error_message"=>$error["Description"],"error_number"=>$error["ErrorCode"]));
				}
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	protected function getOrderTotal()
	{//rendelés végösszegének lekérése
		$helper = $this->otpHelper();
		$order=$helper->getCurrentOrder();
		$grandTotal = round($order->getGrandTotal());
		return $grandTotal;
	}
	
	public function getTransModel()
	{
		return Mage::getModel('tlbarion/transactions');
	}
	
	public function getTransHistoryModel()
	{
		return Mage::getModel('tlbarion/transactions_history');
	}
	
	public function saveTransHistory($history)
	{
		
		$tablesave = $this->getTransHistoryModel()
		->setData($history)
		->save();

	}
	
	private function saveTrans($transaction)
	{
		$tablesave = $this->getTransModel()->setData($transaction)->save();	
		return $tablesave->getEntityId();
	}
	
	public function updateTrans($transaction,$transid)
	{
		$helper = $this->otpHelper();
		$tablesave = $this->getTransModel()->load($transid);
		$tablesave->addData($transaction);
		try{
			$tablesave->save();
		}
		catch (Exception $e){
			Mage::log($transaction,null,$helper->_logfilename);
		}
		return;
	}
	
	public function capture(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$transaction = Mage::getModel('tlbarion/transactions')->loadByOrderId($order->getId());
		$payment->setTransactionId($transaction->getOrderId());
		$payment->setIsTransactionClosed(0);
		
		return $this;
	}
	
	public function processBeforeRefund($invoice, $payment){return $this;} //before refund
	
	/*public function refund(Varien_Object $payment, $amount)
	{
		$tid = $payment->getLastTransId();
		$transaction = $this->getTransModel()->loadByOrderId($tid);
		$transid=$transaction->getEntityId();
		$helper = $this->otpHelper();
		$order = Mage::getModel('sales/order')->load($transaction->getOrderId());
		$storeid = $order->getStoreId();
		$currency=$transaction->getCcy();//GET Currency Code
		$ordertotal = $transaction->getAmount();
		$ordertotalotp = round($ordertotal);
		
		$type = 'WEBSHOPFIZETESJOVAIRAS';
		
		$text = $helper->getShopId($storeid).'|'.$transaction->getRealOrderid().'|'.$ordertotalotp;
		$sign = $helper->sign($text,$storeid);
		
		$refundresult = true;
		
		if($sign==false){
			$refundresult = false;
		}

		$xmlarray = array('name' => 'StartWorkflow',
						  'value' => '',
							array(
								'name'	=> 'TemplateName',
								'value'	=> $type,
							),
							array(
								'name'	=> 'Variables',
								'value'	=> '',
									array(
										'name'	=> 'isClientCode',
										'value'	=> 'WEBSHOP',
									),
									array(
										'name'	=> 'isPOSID',
										'value'	=> $helper->getShopId($storeid),
									),
									array(
										'name'	=> 'isTransactionID',
										'value'	=> $transaction->getRealOrderid(),
									),
									array(
										'name'	=> 'isAmount',
										'value'	=> $ordertotalotp,
									),
							),
						);
		$xmlarray[1][]=$helper->setSignature($sign);//set signature
		$xmltext = $helper->createXML($xmlarray);
		$soap = $helper->createSoapClient();
		$this->saveTransHistory(array('transaction_id'=>$transid,'transaction_type'=>$type));
		$result = $helper->startWorkflowSynch($type,$xmltext,$soap);
		$resultarray = array();
		$resultarray = $helper->convertXML($result);
		if($resultarray['completed']==1 && $resultarray['timeout']==""){
			$resultarray = $helper->convertXML($resultarray['result']);
		}
		else{
			$refundresult = false;
		}
		if($resultarray['messagelist']['message'] == 'SIKER'){
			$response = $resultarray['resultset']['record']['responsecode'];
			if($response=="000"||$response=="001"||$response=="010"){
				$auth=$resultarray['resultset']['record']['authorizationcode'];
				$status='02';
			}
			else{
				$refundresult = false;
			}
		}
		else{
			$refundresult = false;
		}

		if ($refundresult==false){
			$errorCode = 'Invalid Data';
            $errorMsg = $helper->__('Error Processing the request');
			Mage::log($result,null,$helper->getLogfilename());
            Mage::throwException($errorMsg);
		}else{
			$this->saveTransHistory(array('transaction_id'=>$transid,'response'=>$response,'auth_number'=>$auth));
			$transid=$this->updateTrans(array('payment_status'=>$status),$transid);
			$refunded = array('real_orderid'=>$transaction->getRealOrderid(),'order_id'=>$transaction->getOrderId(),'pos_id'=>$helper->getShopId($storeid),'amount'=>'-'.$ordertotalotp,'ccy'=>$transaction->getCcy(),'store_id'=>$transaction->getStoreId(),'payment_status'=>$status,'created_at'=>Varien_Date::now(),'lang'=>$transaction->getLang());
			$this->saveTrans($refunded);
			$helper->_sendStatusMail($order);
		}
		
		return $this;
	} //refund api
	*/

	public function processCreditmemo($creditmemo, $payment){return $this;} //after refund
}
?>