<?php
class MOIP_Transparente_Model_Api {

    const TOKEN_TEST = "3UNGHOJCLVXZVOYF85JGILKALZSROU2O";
    const KEY_TEST = "VX2MOP4AEXFQYHBYIWT0GINNVXFZO9TJCKJ6AWDR";
    const TOKEN_PROD = "FEE5P78NA6RZAHBNH3GLMWZFWRE7IU3D";
    const KEY_PROD = "Y8DIATTADUNVOSXKDN0JVDAQ1KU7UPJHEGPM7SBA";

    private $conta_transparente = null;

    public function getContaTransparente() {
        return $this->conta_transparente;
    }

    public function setContaTransparente($conta_transparente) {
        $this->conta_transparente = $conta_transparente;
    }

    public function getAmbiente() {
        return $this->ambiente;
    }

    public function setAmbiente($ambiente) {
        $this->ambiente = $ambiente;
    }

    public function getListaComissoesAvancadas($order, $meio) {
       $valor_comissao = 20/100;

       foreach ($order->getAllVisibleItems() as $key => $item)
        {
               $product = Mage::getModel('catalog/product')->load($item->getProductId());
               $comissao_valor= $item->getPrice()-($item->getPrice()*$valor_comissao);
                $comissoes[] = array (
                                                        'Razao' => 'Produto '.$item->getName().' x '.$item->getQtyToInvoice().' sku: '. $item->getSku(),
                                                        'LoginMoIP' => $product->getAttributeText('loginmoip'),
                                                        'ValorFixo' => $comissao_valor
                                                    );
        }

        return $comissoes;
    }

    public function generatePedido($data, $pgto) {
        if($pgto['credito_parcelamento'] == ""){
            $pgto['credito_parcelamento'] = 2;
        }
        $standard = Mage::getSingleton('transparente/standard');
        $parcelamento = $standard->getInfoParcelamento();
        $meio = $pgto["forma_pagamento"];
        $vcmentoboleto = $pgto["vcmentoboleto"];
        $forma_pgto = "";
        $validacao_nasp = $standard->getConfigData('validador_retorno');
        $url_retorno =  Mage::getBaseUrl()."Transparente/standard/success/validacao/".$validacao_nasp."/";
        $order = Mage::getModel('sales/order')->loadByIncrementId($data['id_transacao']);
        $increment = $data['id_transacao'];
        $data['id_transacao'] = $order->getID();
        $amout = $order->getGrandTotal();
        $valorcompra = $amout;
        $vcmentoboleto = $standard->getConfigData('vcmentoboleto');
        $vcmento = date('c', strtotime("+" . $vcmentoboleto . " days"));

        if($pgto['tipoderecebimento'] =="0"):
          $tipoderecebimento = "Parcelado";
        else:
           $tipoderecebimento = "Avista";
        endif;

        $tipo_parcelamento = Mage::getSingleton('transparente/standard')->getConfigData('jurostipo');

        $comissionamento = Mage::getStoreConfig('moipall/mktplace/comissionamento');





        $rand = rand(1000,9999999999);

        $id_proprio = $rand.$pgto['conta_transparente'].'_'.$data['id_transacao'];

    #    $xml = $this->generateXml($json);
        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><EnviarInstrucao/>');
        $InstrucaoUnica = $xml->addChild('InstrucaoUnica');
        $InstrucaoUnica->addAttribute('TipoValidacao', 'Transparente');
        $InstrucaoUnica->addChild('Razao', 'Pagamento do Pedido #'.$increment);
        $Valores = $InstrucaoUnica->addChild('Valores');
        $Valor = $Valores->addChild('Valor',  number_format($valorcompra, 2, '.', ''));
        $Valor->addAttribute('moeda', 'BRL');
        $Recebedor = $InstrucaoUnica->addChild('Recebedor');
        $Recebedor->addChild('LoginMoIP', $pgto['conta_transparente']);
        $Recebedor->addChild('Apelido', $pgto['apelido']);


        if($comissionamento){
            $Comissoes = $InstrucaoUnica->addChild('Comissoes');
                $Comissionamento = $Comissoes->addChild('Comissionamento');
                    $Comissionamento->addChild('Razao',  'Pagamento do Pedido #'.$data['id_transacao'].' da Loja '.$pgto['apelido']);
                    $Comissionado = $Comissionamento->addChild('Comissionado');
                        $Comissionado->addChild('LoginMoIP', Mage::getStoreConfig('moipall/mktplace/logincomissionamento'));
                    $Comissionamento->addChild('ValorPercentual', Mage::getStoreConfig('moipall/mktplace/porc_comissionamento'));
                 if(Mage::getStoreConfig('moipall/mktplace/pagadordataxa')){
                    $PagadorTaxa = $Comissoes->addChild('PagadorTaxa');
                    $PagadorTaxa->addChild('LoginMoIP',Mage::getStoreConfig('moipall/mktplace/logincomissionamento'));
                 }
        }
         /* suprimido para comissionamento simples
          $comissioes_avancadas = $this->getListaComissoesAvancadas($order, $pgto['forma_pagamento']);
        $Comissoes = $InstrucaoUnica->addChild('Comissoes');
         foreach ($comissioes_avancadas as $key => $value) {
                $Comissionamento = $Comissoes->addChild('Comissionamento');
                $Comissionamento->addChild('Razao',  $value['Razao']);
                $Comissionado =     $Comissionamento->addChild('Comissionado');
                                                 $Comissionado->addChild('LoginMoIP', $value['LoginMoIP']);
                $Comissionamento->addChild('ValorFixo', $value['ValorFixo']);
        }
         */
        $InstrucaoUnica->addChild('IdProprio', $id_proprio);
        $Pagador = $InstrucaoUnica->addChild('Pagador');
        $Pagador->addChild('Nome',$data['pagador_nome']);
        $Pagador->addChild('Email',$data['pagador_email']);
        $Pagador->addChild('IdPagador',$data['pagador_email']);
        $EnderecoCobranca = $Pagador->addChild('EnderecoCobranca');
        $EnderecoCobranca->addChild('Logradouro', $data['pagador_logradouro']);
        $EnderecoCobranca->addChild('Numero', 'n '.$data['pagador_numero']);
        $EnderecoCobranca->addChild('Complemento', $data['pagador_complemento']);
        $EnderecoCobranca->addChild('Bairro', $data['pagador_bairro']);
        $EnderecoCobranca->addChild('Cidade', $data['pagador_cidade']);
        $EnderecoCobranca->addChild('Estado', $data['pagador_estado']);
        $EnderecoCobranca->addChild('Pais', 'BRA');
        $EnderecoCobranca->addChild('CEP', $data['pagador_cep']);
        $EnderecoCobranca->addChild('TelefoneFixo', $data['pagador_ddd'] . $data['pagador_telefone']);
        $Parcelamentos = $InstrucaoUnica->addChild('Parcelamentos');
        if($tipo_parcelamento == 1){
                $max_parcelas = $parcelamento['c_ate1'];
                $min_parcelas = $parcelamento['c_de1'];
                $juros = $parcelamento['c_juros1'];
                if($max_parcelas == 12){
                  $Parcelamento = $Parcelamentos->addChild('Parcelamento');
                  $Parcelamento->addChild('MinimoParcelas',$min_parcelas);
                  $Parcelamento->addChild('MaximoParcelas',$max_parcelas);
                  $Parcelamento->addChild('Recebimento',$tipoderecebimento);
                  $Parcelamento->addChild('Juros',$juros);
                } else{
                       $Parcelamento = $Parcelamentos->addChild('Parcelamento');
                       $Parcelamento->addChild('MinimoParcelas',$min_parcelas);
                       $Parcelamento->addChild('MaximoParcelas',$max_parcelas);
                       $Parcelamento->addChild('Recebimento',$tipoderecebimento);
                       $Parcelamento->addChild('Juros',$juros);

                       $Parcelamento = $Parcelamentos->addChild('Parcelamento');
                       $Parcelamento->addChild('MinimoParcelas',$max_parcelas+1);
                       $Parcelamento->addChild('MaximoParcelas',12);
                       $Parcelamento->addChild('Recebimento',$tipoderecebimento);
                       $Parcelamento->addChild('Juros',1.99);
                }
        } else {
            for ($i=2; $i <= 12; $i++) {
                $Parcelamento = $Parcelamentos->addChild('Parcelamento');
                $juros_parcela = 's_juros'.$i;
                $Parcelamento->addChild('MinimoParcelas',$i);
                $Parcelamento->addChild('MaximoParcelas',$i);
                $Parcelamento->addChild('Recebimento',$tipoderecebimento);
                $Parcelamento->addChild('Juros',$parcelamento[$juros_parcela]);
                $Parcelamento->addChild('Repassar','true');
             }
        }
        $FormasPagamento = $InstrucaoUnica->addChild('FormasPagamento');
        $FormasPagamento->addChild('FormaPagamento', 'CartaoCredito' );
        $FormasPagamento->addChild('FormaPagamento', 'CartaoDebito' );
        $FormasPagamento->addChild('FormaPagamento', 'DebitoBancario' );
        $FormasPagamento->addChild('FormaPagamento', 'BoletoBancario' );
        $FormasPagamento->addChild('FormaPagamento', 'FinanciamentoBancario');
        if ($meio == "BoletoBancario"){
            $Boleto_xml = $InstrucaoUnica->addChild('Boleto');
            $Boleto_xml->addChild('Instrucao1', 'Pagamento do Pedido #'.$increment);
            $Boleto_xml->addChild('Instrucao2', 'NÃO RECEBER APÓS O VENCIMENTO');
            $Boleto_xml->addChild('Instrucao3', '+ Info em: '.Mage::getBaseUrl());
            $Boleto_xml->addChild('DataVencimento');
        }
        $InstrucaoUnica->addChild('URLNotificacao', $url_retorno);
        $request = $xml->asXML();
        $request = utf8_decode($request);
        $request = utf8_encode($request);
        #var_dump($request); die();
        return $request;
    }
    public function generateUrl($token) {
        if ($this->getAmbiente() == "teste")
            $url = $token;
        else
            $url = $token;
        return $url;
    }

    public function getParcelamentoComposto($valor) {
        $standard = Mage::getSingleton('transparente/standard');
        $parcelamento = $standard->getInfoParcelamento();
        $parcelas = array();
        $json_parcelas = array();
        $juros = array();
        $primeiro = 1;
        $max_div = $valor/(int)Mage::getSingleton('transparente/standard')->getConfigData('valorminimoparcela');
        $valor_juros= Mage::getSingleton('transparente/standard')->getConfigData('parcelamento_c_juros1');

        if($valor <= Mage::getSingleton('transparente/standard')->getConfigData('valorminimoparcela')){
            $json_parcelas[1] = array(
                                        'parcela' => Mage::helper('core')->currency($valor, true, false),
                                        'total_parcelado' => Mage::helper('core')->currency($valor, true, false),
                                        'juros' => 0
                                        );
            $json_parcelas = Mage::helper('core')->jsonEncode((object)$json_parcelas);
            return $json_parcelas;
        }

        if($parcelamento['c_ate1'] < $max_div){
            $max_div = $parcelamento['c_ate1'];
        }

            for ($i=1; $i <= $max_div; $i++) {
                if($i > 1){
                    $total_parcelado[$i] = $this->getJurosComposto($valor, $valor_juros, $i)*$i;
                    $parcelas[$i] = $this->getJurosComposto($valor, $valor_juros, $i);
                    $juros[$i] =  $valor_juros;
                }
                else {
                    $total_parcelado[$i] =  $valor;
                    $parcelas[$i] = $valor*$i;
                    $juros[$i] = 0;
                }
                if($i <= Mage::getSingleton('transparente/standard')->getConfigData('nummaxparcelamax')){
                    $json_parcelas[$i] = array(
                                                'parcela' => Mage::helper('core')->currency($parcelas[$i], true, false),
                                                'total_parcelado' =>  Mage::helper('core')->currency($total_parcelado[$i], true, false),
                                                'juros' => $juros[$i]
                                            );
                    $primeiro++;
                }
             }
             if($primeiro < 12 && $primeiro < ($valor/(int)Mage::getSingleton('transparente/standard')->getConfigData('valorminimoparcela')) )
             {
                 while ($primeiro <= 12) {
                    $total_parcelado[$primeiro] = "";
                    $parcelas[$primeiro] = $this->getJurosComposto($valor, '2.99', $primeiro);
                    $total_parcelado[$primeiro] =  $parcelas[$primeiro]*$primeiro;
                    $juros[$primeiro] = '2.99';

                    $json_parcelas[$primeiro] = array(
                                                'parcela' => Mage::helper('core')->currency($parcelas[$primeiro], true, false),
                                                'total_parcelado' =>  Mage::helper('core')->currency($total_parcelado[$primeiro], true, false),
                                                'juros' => '2.99'
                                            );
                    $primeiro++;
                 }
             }
        $json_parcelas = Mage::helper('core')->jsonEncode((object)$json_parcelas);
        return $json_parcelas;

    }

     public function getParcelamentoSimples($valor) {
        $standard = Mage::getSingleton('transparente/standard');
        $parcelamento = $standard->getInfoParcelamento();
        $parcelas = array();
        $juros = array();
        $primeiro = 1;
        $max_div = (int)($valor/Mage::getSingleton('transparente/standard')->getConfigData('valorminimoparcela'));

        if($valor <= Mage::getSingleton('transparente/standard')->getConfigData('valorminimoparcela')){
            $json_parcelas[1] = array(
                                        'parcela' => Mage::helper('core')->currency($valor, true, false),
                                        'total_parcelado' => Mage::helper('core')->currency($valor, true, false),
                                        'juros' => 0
                                        );
            $json_parcelas = Mage::helper('core')->jsonEncode((object)$json_parcelas);
            return $json_parcelas;
        }
        if(Mage::getSingleton('transparente/standard')->getConfigData('nummaxparcelamax') > $max_div){
            $max_div = $max_div;
        } else {
            $max_div = Mage::getSingleton('transparente/standard')->getConfigData('nummaxparcelamax');
        }

        for ($i=1; $i <= $max_div; $i++) {
                $juros_parcela = 's_juros'.$i;

                if($i > 1){
                    $taxa = $parcelamento[$juros_parcela] / 100;
                    $valor_add = $valor * $taxa;
                    $total_parcelado[$i] =  $valor + $valor_add;
                    $parcelas[$i] =  ($valor  + $valor_add)/$i;
                    $juros[$i] = $parcelamento[$juros_parcela];
                }
                else {
                    $total_parcelado[$i] =  $valor;
                    $parcelas[$i] = $valor*$i;
                    $juros[$i] = 0;
                }
                if($i <= Mage::getSingleton('transparente/standard')->getConfigData('nummaxparcelamax')){
                    $json_parcelas[$i] = array(
                                                'parcela' => Mage::helper('core')->currency($parcelas[$i], true, false),
                                                'total_parcelado' =>  Mage::helper('core')->currency($total_parcelado[$i], true, false),
                                                'juros' => $juros[$i]
                                            );
                     }
             }
        $json_parcelas = Mage::helper('core')->jsonEncode((object)$json_parcelas);
        return $json_parcelas;

    }

    public function getParcelamento($valor) {

        $tipo_parcelamento = Mage::getSingleton('transparente/standard')->getConfigData('jurostipo');

        if($tipo_parcelamento == 1){
            $tipo = $this->getParcelamentoComposto($valor);
        } else {
            $tipo = $this->getParcelamentoSimples($valor);
        }

        return $tipo;

    }

    public function getJurosSimples($valor, $juros, $parcela) {

        return $valParcela;
    }

    public function getJurosComposto($valor, $juros, $parcela) {
        $principal = $valor;
        if($juros != 0){
            $taxa =  $juros/100;
            $valParcela = ($principal * $taxa)/(1 - (pow(1/(1+$taxa), $parcela)));
        } else {
            $valParcela = $principal/$parcela;
        }

        return $valParcela;
    }

}
