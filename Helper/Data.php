<?php

namespace Magento360\CustomeName\Helper;

use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento360\CustomeName\Model\CustomNameFactory;

/**
 * Class Data
 * Magento360\CustomeName\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var SessionFactory
     */
    private $sessionFactory;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var \Magento360\CustomeName\Model\CustomName
     */
    private $customName;
    /**
     * @var Registry
     */
    private $registry;


    public function __construct(
        Context $context,
        SessionFactory $sessionFactory,
        CustomNameFactory $customName,
        Registry $registry,
        Session $session
    ) {
        parent::__construct($context);
        $this->sessionFactory = $sessionFactory;
        $this->session = $session;
        $this->customName = $customName;
        $this->registry = $registry;
    }

    public function getCustomer()
    {
        return $this->sessionFactory->create()->getCustomer();
    }
    public function getSingleCustomNameValue(){
        if ($this->sessionFactory->create()->isLoggedIn()){
            $tablecollection = $this->customName->create()->getCollection()
                ->addFieldToFilter('customer_id',$this->getCustomer()->getId())
                ->addFieldToFilter('product_id',$this->getCurrentProduct()->getId())
                ->addFieldToSelect('value')
                ->setOrder('entity_id')
                ->setCurPage(1)
                ->setPageSize(1);
            $value = '';

            foreach ($tablecollection as $data){
               $value =  $data->getValue();
            }
            return $value;
        }
        return '';
    }


    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
