<?php


namespace Magento360\CustomeName\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;
use Magento360\CustomeName\Api\Data\CustomNameInterface;
use Magento360\CustomeName\Model\CustomNameFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;


class OrderManagement
{

    private $customNameinterface;

    private $customNameFactory;

    private $customNameRepository;
    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    protected $searchCriteriaBuilder;

    protected $scopeConfig;

    public function __construct(
        CustomNameInterface $customNameInterface,
        CustomNameFactory $customNameFactory,
        CustomNameRepositoryInterface $customNameRepository,
        SessionFactory $sessionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customNameinterface = $customNameInterface;
        $this->customNameFactory = $customNameFactory;
        $this->customNameRepository = $customNameRepository;
        $this->sessionFactory = $sessionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Add reservation before place order
     *
     * In case of error during order placement exception add compensation
     *
     * @param OrderManagementInterface $subject
     * @param callable $proceed
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundPlace(
        OrderManagementInterface $subject,
        callable $proceed,
        OrderInterface $order
    ): OrderInterface {
        $order = $proceed($order);
        $items = $order->getItems();
        if (!empty($customer_id = $order->getCustomerId())) {
            foreach ($items as $item) {
                $options = $item->getProductOptions();
                if (isset($options['options']) && !empty($options['options'])) {
                    foreach ($options['options'] as $option) {
                        if ($option['label'] == "Purchaged Name") {
                        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id',$customer_id,'eq')->create();

                       $items = $this->customNameRepository->getList($searchCriteria);

                        $price = $this->scopeConfig->getValue('magento360/customname/price',ScopeInterface::SCOPE_STORE);

                        $quantity = $this->scopeConfig->getValue('magento360/customname/qty',ScopeInterface::SCOPE_STORE);
                        $offerquantity = $this->scopeConfig->getValue('magento360/customname/offer',ScopeInterface::SCOPE_STORE);


                        $qty = 0;
                        
                        foreach ($items->getItems() as $key => $val) {
                                $qty += $val->getQty();

                        if($option['option_value'] == $val->getValue()) && $item->getProductId() == $val->getProductId()){

                           $tb =  $this->customNameFactory->create()-load($val->getId());

                           $tb->setQty($val->getQty() + $item->getQty());
                           $tb->save();
                            
                        }else{
                            $this->customNameinterface->setCustomerId($customer_id);
                            $this->customNameinterface->setProductId($item->getProductId());
                            $this->customNameinterface->setValue($option['option_value']);
                            $this->customNameinterface->getValue();
                            $this->customNameRepository->save($this->customNameinterface);
                        }
                        }
                        }
                    }
                }
            }
        }
        return $order;
    }
}
