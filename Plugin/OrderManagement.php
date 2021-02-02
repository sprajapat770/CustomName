<?php


namespace Magento360\CustomeName\Plugin;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;
use Magento360\CustomeName\Api\Data\CustomNameInterface;
use Magento360\CustomeName\Model\CustomNameFactory;
use Magento\Customer\Model\SessionFactory;
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
        SearchCriteriaBuilder $searchCriteriaBuilder,
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
        $count = 0;
        if (!empty($customer_id = $order->getCustomerId())) {
            foreach ($items as $item) {
                $options = $item->getProductOptions();
                if (isset($options['options']) && !empty($options['options'])) {

                    foreach ($options['options'] as $option) {
                        if ($option['label'] == "Purchaged Name") {
                            $count++;
                            if ($count ==1){
                                $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customer_id, 'eq')->create();
                                $collection = $this->customNameRepository->getList($searchCriteria);
                            }
                            $qty = 0;
                            $dataValues = [];
                            $dataProducts = [];
                            $dataQty = [];

                            foreach ($collection->getItems() as $key => $val) {
                                $dataValues[$val->getId()] = $val->getValue();
                                $dataProducts[$val->getId()] = $val->getProductId();
                                $dataQty[$val->getId()] = $val->getProductId();
                            }

                            $productId = $item->getProductId();
                            $id = '';
                            if((in_array($option['option_value'],$dataValues) )&& (in_array($productId,$dataProducts))){
                                
                                foreach ($dataValues as $key =>$v){
                                    if (($option['option_value'] == $v) && $dataProducts[$key]==$productId){
                                        $id = $key;
                                    }
                                }

                                $tb =  $this->customNameFactory->create()->load($id);
                                    $tb->setQty( ($dataQty[$id] + $item->getQtyOrdered()));
                                   try {
                                       $tb->save();
                                   }catch (\Exception $e){

                                   }
                            }else{
                                    $this->customNameinterface->setCustomerId($customer_id);
                                    $this->customNameinterface->setProductId($item->getProductId());
                                    $this->customNameinterface->setValue($option['option_value']);
                                    $this->customNameinterface->setQty($item->getQtyOrdered());
                                    $this->customNameRepository->save($this->customNameinterface);
                                }
                            }
                        }
                    }
                }
            }
        return $order;
    }
}
