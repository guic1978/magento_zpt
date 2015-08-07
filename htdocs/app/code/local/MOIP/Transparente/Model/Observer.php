<?php

class Moip_Transparente_Model_Observer
{
    public function getMethod($observer) {
		$aplica = 0;
		$discountAmount = 0;
		$quote=$observer->getEvent()->getQuote();

		if($quote->getPayment()->getFormaPagamento() == 'BoletoBancario'){
			$type = 'Boleto Bancário';
			$valorinicial = $quote->getGrandTotal();
			if (Mage::getStoreConfig('moipall/pagamento_avancado/pagamento_boleto')){

                if ($valorinicial >= Mage::getStoreConfig('moipall/pagamento_avancado/boleto_valor'))
                {

                   if ($valorinicial >= Mage::getStoreConfig('moipall/pagamento_avancado/boleto_valor2') && (int)Mage::getStoreConfig('moipall/pagamento_avancado/boleto_valor2') != 0)
                    {

                        $valorinicial = $quote->getSubtotal();
                        $percentual_valor = (double)Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc2')/100;
                        $percentual = Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc2');
                        $discountAmount =  $valorinicial * $percentual_valor;
                        $aplica = 1;
                    }

                    else {

                        $valorinicial = $quote->getSubtotal();
                         $percentual_valor = (double)Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc')/100;
                        $percentual = Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc');
                        $discountAmount =  $valorinicial * $percentual_valor;
                        $aplica = 1;
                    }
                }


            }
		}else if($quote->getPayment()->getFormaPagamento() == 'DebitoBancario'){
			$type = 'Débito Bancário';
			if (Mage::getStoreConfig('moipall/pagamento_avancado/transf_desc')){
               if ($valorinicial >= Mage::getStoreConfig('moipall/pagamento_avancado/boleto_valor2') && (int)Mage::getStoreConfig('moipall/pagamento_avancado/boleto_valor2') != 0)
                	{

	                	    $valorinicial = $quote->getSubtotal();
                                            $percentual_valor = (double)Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc2')/100;
                                            $percentual = Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc2');
                                            $discountAmount =  $valorinicial * $percentual_valor;
                                            $aplica = 1;
                	} else {

	                           $valorinicial = $quote->getSubtotal();
                                        $percentual_valor = (double)Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc')/100;
                                        $percentual = Mage::getStoreConfig('moipall/pagamento_avancado/boleto_desc');
                                        $discountAmount =  $valorinicial * $percentual_valor;
                                        $aplica = 1;
            	                           }
            }
		}
		if($aplica){
				$total = $quote->getBaseSubtotal();
                            $quote->setSubtotal(0);
                            $quote->setBaseSubtotal(0);

                            $quote->setSubtotalWithDiscount(0);
                            $quote->setBaseSubtotalWithDiscount(0);

                            $quote->setGrandTotal(0);
                            $quote->setBaseGrandTotal(0);


                            $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                            foreach($quote->getAllAddresses() as $address)
                            {

                                $address->setSubtotal(0);
                                $address->setBaseSubtotal(0);

                                $address->setGrandTotal(0);
                                $address->setBaseGrandTotal(0);

                                $address->collectTotals();

                                $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
                                $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());
                                $quote->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount());
                                $quote->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount());
                                $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
                                $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
                                $quote->save();

                                $quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                                ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                                ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                                ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                                ->save();


                                if($address->getAddressType() == $canAddItems)
                                {
                                    $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount);
                                    $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount);
                                    $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount);
                                    $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount);

                                    // When Mageno Promo Code is used
                                    if($address->getDiscountDescription())
                                    {
                                        $address->setDiscountAmount(($address->getDiscountAmount() - $discountAmount));
                                        //$address->setDiscountAmount($total - $discountAmount);
                                        $address->setDiscountDescription($address->getDiscountDescription().' , '.$percentual.'% sobre subtotal');
                                        //$address->setBaseDiscountAmount($total - $discountAmount);
                                        $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
                                    }
                                    // When Subscribed Customer 5% discount is used
                                    else
                                    {
                                        //$address->setDiscountAmount(-($discountAmount));
                                        $address->setDiscountAmount($discountAmount);
                                        $address->setDiscountDescription($percentual.'% sobre subtotal');
                                        $address->setBaseDiscountAmount($discountAmount);
                                        //$address->setBaseDiscountAmount(-($discountAmount));
                                    }

                                    $address->save();
                                }
                            }

                            foreach($quote->getAllItems() as $item)
                            {
                                //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                                $rat = $item->getPriceInclTax() / $total;
                                $ratdisc = $discountAmount * $rat;

                                $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
                                $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();
                            }


		}

	}
    public function setStatus() {
            $time = time();
            $to = date('Y-m-d H:i:s', $time);
            $moip_boleto_vencimento = Mage::getStoreConfig('payment/moip_transparente_standard/vcmentoboleto') + 4;
            $time_boleto = '-'.(int)$moip_boleto_vencimento.' day';
            $from = date('Y-m-d H:i:s',(strtotime ( $time_boleto, strtotime ( $to) ) ));
            $to = date('Y-m-d H:i:s', $time);
            $moip_boleto_vencimento2 = Mage::getStoreConfig('payment/moip_transparente_standard/vcmentoboleto') + 3;
            $time_boleto2 = '-'.(int)$moip_boleto_vencimento2.' day';
            $to_end = date('Y-m-d H:i:s',(strtotime ( $time_boleto2, strtotime ( $to) ) ));
            Mage::log($from, null, 'MOIP_Cron.log', true);
            $order_collection =  Mage::getModel('sales/order')->getCollection()
                                                                                        ->addFieldToFilter('created_at', array('from' => $from, 'to' => $to_end))
                                                                                        ->addAttributeToFilter('status', 'holded');
            foreach($order_collection as $order){
                    $order =  Mage::getModel('sales/order')->load($order->getEntityId());

                    if($order->canUnhold()) {
                        $order->unhold()->save();
                    }

                    if(!$order->canCancel())
                        continue;
                    $link = Mage::getUrl('sales/order/reorder/');
                    $link = $link.'order_id/'.$order->getEntityId();
                    $comment = "Cancelado por tempo limite para a notificação de pagamento, caso já tenha feito o pagamento entre em contato com o nosso atendimento, se desejar poderá refazer o seu pedido acessando: ".$link;
                    $status = 'canceled';
                    $order->cancel();
                    $state = Mage_Sales_Model_Order::STATE_CANCELED;
                    $order->setState($state, $status, $comment, $notified = true, $includeComment = true);
                    $order->sendOrderUpdateEmail(true, $comment);
                    $order->save();
            }
    }


}
