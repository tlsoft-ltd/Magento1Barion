<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = $this;

/**
 * Start install setup
 */
$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS `{$this->getTable('tlbarion/transactions')}`;"
        . "CREATE TABLE `{$this->getTable('tlbarion/transactions')}`("
        . "`entity_id` int(10) unsigned NOT NULL auto_increment,"
		. "`order_id` int(10) unsigned NOT NULL default 0,"
		. "`real_orderid` varchar(32) NOT NULL,"
		. "`ccy` varchar(3) NOT NULL,"
		. "`application_id` varchar(36) NOT NULL,"
		. "`amount` decimal(12,2) NOT NULL,"
		. "`bariontransactionid` varchar(38) NOT NULL,"
		. "`store_id` int(10) unsigned NOT NULL default 0,"
		. "`created_at` datetime NOT NULL default '0000-00-00 00:00:00',"
		. "`payment_status` varchar(2) NOT NULL,"
        . "PRIMARY KEY(`entity_id`),"
		. "KEY `IDX_TLBARION_TRANSACTIONS_ORDER_ID` (`order_id`),"
		. "KEY `IDX_TLBARION_TRANSACTIONS_STORE_ID` (`store_id`)"
		.") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Barion Transactions Table';"
);

$installer->run("DROP TABLE IF EXISTS `{$this->getTable('tlbarion/transactions_history')}`;"
        . "CREATE TABLE `{$this->getTable('tlbarion/transactions_history')}`("
        . "`entity_id` int(10) unsigned NOT NULL auto_increment,"
		. "`transaction_id` int(10) unsigned NOT NULL,"
		. "`error_message` varchar(255),"
		. "`error_number` varchar(255),"
		. "`created_at` datetime NOT NULL default '0000-00-00 00:00:00',"
        . "PRIMARY KEY(`entity_id`),"
		. "CONSTRAINT `FK_TLBARION_TRANSACTIONS_HISTORY_TRANSACTION_ID` FOREIGN KEY (`transaction_id`) REFERENCES `{$installer->getTable('tlbarion/transactions')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE"
		.") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Barion Transaction History Table';"
);

$installer->endSetup();