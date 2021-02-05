<?php

namespace Magento360\CustomeName\Helper;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;
use Magento360\CustomeName\Model\CustomNameFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
/**
 * Class Data
 * Magento360\CustomeName\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_CUSTOM_IS_ENABLED = 'magento360/customname/enabled';
     /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;


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
        CustomNameRepositoryInterface $list,
        ScopeConfigInterface $scopeConfig,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->sessionFactory = $sessionFactory;
        $this->customName = $customName;
        $this->registry = $registry;
        $this->_list = $list;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->sessionFactory->create()->getCustomer();
    }
    public function getSession()
    {
        return $this->sessionFactory->create();
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
    public function isModuleEnabled()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, ScopeInterface::SCOPE_STORE);
    }
    public function getAdminFee(){
        return (float) $this->_scopeConfig->getValue('magento360/customname/price',ScopeInterface::SCOPE_STORE);
    }
    public function getAdminQty(){
        return (float) $this->_scopeConfig->getValue('magento360/customname/qty',ScopeInterface::SCOPE_STORE);
    }
    public function getAdminOfferQty(){
        return (float) $this->_scopeConfig->getValue('magento360/customname/offer',ScopeInterface::SCOPE_STORE);
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
