<?php

namespace Magento360\CustomeName\Helper;

use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;
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

    protected $custom;

    protected $searchCriteriaBuilder;

    protected $_list;

    public function __construct(
        Context $context,
        SessionFactory $sessionFactory,
        CustomNameFactory $customName,
        Registry $registry,
        Session $session,
        CustomNameRepositoryInterface $list,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->sessionFactory = $sessionFactory;
        $this->session = $session;
        $this->customName = $customName;
        $this->registry = $registry;
        $this->_list = $list;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
       // $this->custom = $custom;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->sessionFactory->create()->getCustomer();
    }

//    public function getSingleCustomNameValue(){
//
//        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id',1,'eq')->create();
//
//        $items = $this->_list->getList($searchCriteria);
//
//        $value = '';
//
//        foreach ($items->getItems() as $key => $val) {
//            $value = $val->getValue();
//        }
//        return $value;
////        if ($this->sessionFactory->create()->isLoggedIn()){
////            //$d = $this->custom->getSectionData();
////
////            $tablecollection = $this->customName->create()->getCollection()
////                ->addFieldToFilter('customer_id',$this->getCustomer()->getId())
////                ->addFieldToFilter('product_id',$this->getCurrentProduct()->getId())
////                ->addFieldToSelect('value')
////                ->setOrder('entity_id')
////                ->setCurPage(1)
////                ->setPageSize(1);
////            $value = '';
////
////            foreach ($tablecollection as $data){
////               $value =  $data->getValue();
////            }
////            return $value;
////        }
////        return '';
//    }

    public function getAdminFee(){
        return $this->_scopeConfig->getValue('magento360/customname/price');
    }
    public function getAdminQty(){
        return $this->_scopeConfig->getValue('magento360/customname/qty');
    }
    public function getAdminOfferQty(){
        return $this->_scopeConfig->getValue('magento360/customname/offer');
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
