<?php

class MOIP_Transparente_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('MOIP/transparente/info.phtml');
    }



    public function getTransparente()
    {
        return Mage::getSingleton('transparente/api');
    }


    public function getBoletoImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/boleto');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Bradesco.png');
        }
    }

    //imagens de transferencia
    public function getBBImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_bb');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/BancoDoBrasil.png');
        }
    }
    public function getBradescoImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_bradesco');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Bradesco.png');
        }
    }
    public function getItauImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_itau');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Itau.png');
        }
    }
    public function getBanrisulImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_banrisul');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Banrisul.png');
        }
    }

    //imagens de cartao
    public function getVisaImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_visa');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Visa.png');
        }
    }
    public function getMastercardImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_master');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Mastercard.png');
        }
    }
    public function getDinersImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_diners');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Diners.png');
        }
    }
    public function getAmericanExpressImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_american');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/AmericanExpress.png');
        }
    }
    public function getHipercardImage() {
        if (Mage::getStoreConfig('moipall/config/trocar_bandeira_cartao')) {
            return Mage::getBaseUrl('media') . "moip/alltransparente/". Mage::getStoreConfig('moipall/config/cartao_hipercard');
        }else {
            return $this->getSkinUrl('MOIP/transparente/imagem/Hipercard.png');
        }
    }

    public function getImagePg($param){
        $nome ="";
       switch ($param) {
        case "Visa":
            $nome = $this->getVisaImage();
            break;
        case "Mastercard":
            $nome = $this->getMastercardImage();
            break;
        case "AmericanExpress":
            $nome = $this->getAmericanExpressImage();
            break;
        case "Dinners":
            $nome = $this->getDinersImage();
            break;
        case "Hipercard":
            $nome = $this->getHipercardImage();
            break;
        default:
            $nome = $param;
            break;
        }
        return $nome;

    }
	private function getNomePagamento($param) {
		$nome = "";
		switch ($param) {
		case "BoletoBancario":
		    $nome = "Boleto Bancário";
		    break;
		case "DebitoBancario":
		    $nome = "Transferência Bancária";
		    break;
		case "CartaoCredito":
		    $nome = "Cartão de Crédito";
		    break;
		}
		return $nome;
	}

       public function toPdf()
    {
        $this->setTemplate('MOIP/transparente/info/pdf.phtml');
        return $this->toHtml();
    }

    protected function _prepareInfo()
    {

                $order_get = Mage::app()->getRequest()->getParam('order_id');
                $order = $this->getInfo()->getOrder();

                $customer_order = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $order =  $order->getIncrementId();

                $model = Mage::getModel('transparente/write');
                $result = $model->load($order, 'realorder_id');
                $dados = array();

                if((int)$customer_order->getTipopessoa()!= 1){
                    $dados['compra_pj'] = 1;
                    $dados['cnpj'] = $customer_order->getCnpj();
                    $dados['razaosocial'] = $customer_order->getRazaosocial();
                    $dados['nomefantasia'] = $customer_order->getNomefantasia();
                    $dados['insestadual'] = $customer_order->getInsestadual();
                }

                $dados['result_meio'] = $this->getNomePagamento($result->getMeio_pg());
                $dados['meio_pago'] = $this->getNomePagamento($result->getMeio_pg());
                $dados['realorder_id'] = $result->getRealorder_id();
                $dados['order_idtransparente'] = $result->getorder_idtransparente();
                $dados['boleto_line'] = $result->getBoleto_line();
                $dados['customer_id'] = $result->getCustomerId();
                $dados['brand'] = $result->getBrand();
                $dados['creditcard_parc'] = $result->getCreditcard_parc();
                $dados['first6'] = $result->getFirst6();
                $dados['last4'] = $result->getLast4();
                $dados['token'] = $result->getToken();
                $dados['image'] = $this->getImagePg($result->getBrandTransparente());
                $dados['url'] = $result->getUrlcheckout_pg();
            return $dados;
    }
}
