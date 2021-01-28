<?php


namespace Magento360\Base\Helper;


use Bss\CustomOptionAbsolutePriceQuantity\Model\TierPriceOptionValueFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class OptionsTierPrice extends AbstractHelper
{

    /**
     * @var TierPriceOptionValueFactory
     */
    private $tierPriceOptionValueFactory;

    /**
     * OptionsTierPrice constructor.
     * @param Context $context
     * @param TierPriceOptionValueFactory $tierPriceOptionValueFactory
     */
    public function __construct(
        Context $context,
        TierPriceOptionValueFactory $tierPriceOptionValueFactory)
    {
        parent::__construct($context);
        $this->tierPriceOptionValueFactory = $tierPriceOptionValueFactory;
    }

    public function   getOptionTierPriceCollection($optionId){
        $tierPriceModel = $this->tierPriceOptionValueFactory->create()->loadByOptionTyeId($optionId);
        if (empty($tierPriceModel['tier_price'])){
            return [];
        }
        return $this->jsonParser($tierPriceModel["tier_price"]);
    }

    public function jsonParser($data){
        return json_decode($data);
    }

}
