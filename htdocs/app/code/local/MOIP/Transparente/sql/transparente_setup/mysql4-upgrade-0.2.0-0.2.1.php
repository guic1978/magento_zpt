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

$installer->run("ALTER TABLE  `".$tablePrefix."moip_transparente`
ADD  `cofre`  VARCHAR( 250 ) NULL AFTER  `status_token`,
ADD  `cofre_parcela`  VARCHAR( 30 ) NULL AFTER  `cofre`,
ADD  `aceita_cofre` VARCHAR( 30 )NULL AFTER  `cofre_parcela`");
$installer->startSetup();

$installer->endSetup();
?>
