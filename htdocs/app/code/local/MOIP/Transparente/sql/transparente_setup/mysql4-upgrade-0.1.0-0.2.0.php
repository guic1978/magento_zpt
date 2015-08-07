<?php
/**
 * Transparente - Transparente Payment Module
 *
 * @title      Magento -> Custom Payment Module for Transparente (Brazil)
 * @category   Payment Gateway
 * @package    MOIP_Transparente
 * @author     Transparente Pagamentos S/a
 * @copyright  Copyright (c) 2013 Moip Soluções Web
 * @license    Licença válida por tempo indeterminado
 */
$installer = $this;
$installer->startSetup();
$tablePrefix = (string) Mage::getConfig()->getTablePrefix();

$installer->run("
CREATE TABLE IF NOT EXISTS `".$tablePrefix."moip_transparente` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `realorder_id` int(11) DEFAULT NULL,
  `meio_pg` longtext DEFAULT NULL,
  `key_payment` char(50) DEFAULT NULL,
  `order_idtransparente` char(50) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_idtransparente` char(50) DEFAULT NULL,
  `creditcard_parc` char(50) DEFAULT NULL,
  `creditcard_idtransparente` char(50) DEFAULT NULL,
  `brand_transparente` varchar(250) DEFAULT NULL,
  `first6` int(11) DEFAULT NULL,
  `last4` int(11) DEFAULT NULL,
  `boleto_line` char(100) DEFAULT NULL,
  `urlcheckout_pg` text DEFAULT NULL,
  `fees` char(50) DEFAULT NULL,
  `json_send` longtext DEFAULT NULL,
  `token` varchar(250) DEFAULT NULL,
  `status_token` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

$installer->startSetup();

$installer->endSetup();
?>
