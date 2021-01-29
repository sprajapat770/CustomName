<?php


namespace Magento360\CustomeName\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento360\CustomeName\Api\CustomNameRepositoryInterface;
use Magento360\CustomeName\Api\Data\CustomNameInterface;

class OrderManagement
{

    private $customNameinterface;

    private $customNameRepository;

    public function __construct(
        CustomNameInterface $customName,
        CustomNameRepositoryInterface $customNameRepository
    ) {

        $this->customNameinterface = $customName;
        $this->customNameRepository = $customNameRepository;
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
        if (!empty($customer_id = $order->getCustomerId())){
            foreach ($items as $item){
                $options = $item->getProductOptions();
                if (isset($options['options']) && !empty($options['options'])) {
                    foreach ($options['options'] as $option) {
                        $data = $this->customNameRepository->getValuesByCustomerId($customer_id);
                        if ($option['label'] == "Purchaged Name" ){
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
        return $order;
    }
}
