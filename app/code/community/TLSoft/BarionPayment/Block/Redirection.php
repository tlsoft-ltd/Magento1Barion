<?php
class TLSoft_BarionPayment_Block_Redirection extends Mage_Core_Block_Abstract
{
	protected function _toHtml()
	{
		$session = Mage::getSingleton('checkout/session');

		$barionurldata=$session->getBarionUrlData();

		$redirect="<meta HTTP-EQUIV='refresh' CONTENT='0;URL={$barionurldata}'>";
				
		$html = "<html>
		<head>{$redirect}</head><body>";
		$html.= Mage::helper("tlbarion")->__('You will be redirected to the Barion website in a few seconds.');
		$html.= "</body></html>";
		return $html;
		
	}
}
?>