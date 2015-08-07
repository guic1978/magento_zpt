<?php
class MOIP_Onestepcheckout_Block_Checkout_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
{

    public function getCountryHtmlSelect($type)
    {
            $select = $this->getLayout()->createBlock('core/html_select')
                            ->setName($type.'[country_id]')
                            ->setId($type.':country_id')
                            ->setTitle(Mage::helper('checkout')->__('Country'))
                            ->setClass('validate-select billing_country')
                            ->setValue("BR")
                            ->setOptions($this->getCountryOptions());
        return $select->getHtml();
    }

  public function getAddressesHtmlSelect22($type)
    {

        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline'),
                    'title' => 'Selecione o endereço de envio',
                );
            }

            $addressId = $this->getAddress()->getId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setTitle('Selecione seu endereço')
                ->setExtraParams('')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('Salvar um novo endereço'));

            return $select->getHtml();
        }
        return '';
    }
    public function MyStatus(){
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    public function Data(){
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $firstname = $customer->getFirstname();
            $lastname = $customer->getLastname();
            $taxvat = $customer->getTaxvat();
            $exibe_email = 0;
     } else {
        $fullname = "";
        $firstname = "";
        $lastname = "";
        $taxvat = "";
        $exibe_email = 1;
     }
     if($this->getAddress()){
            $firstname = $this->getAddress()->getFirstname();
            $lastname = $this->getAddress()->getLastname();
     }
     $_dob = $this->getLayout()->createBlock('customer/widget_dob');
     $_taxvat = $this->getLayout()->createBlock('customer/widget_taxvat');
     $_gender = $this->getLayout()->createBlock('customer/widget_gender');
     $data = array(
                             'customer' => $customer,
                              'firstname' => $firstname,
                              'lastname' => $lastname,
                              'region_select' => $region_select,
                              'taxvat' => $taxvat,
                              'exibe_email' => $exibe_email,
                              '_dob' => $_dob,
                              '_taxvat' => $_taxvat,
                              '_gender' => $_gender
                            );
     return $data;

    }

}
