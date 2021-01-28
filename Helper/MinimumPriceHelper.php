<?php


namespace Magento360\Base\Helper;


use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class MinimumPriceHelper  extends AbstractHelper
{

    /**
     * @var Product
     */
    private $_productFactory;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */

    private $customerSession;

    public function __construct(
        Context $context,
        Product $_productFactory,
        \Magento\Customer\Model\SessionFactory $customerSession)
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->_productFactory = $_productFactory;

    }

    public function getCustomerId(){
        if ($this->customerSession->create()->isLoggedIn()){
            return $this->customerSession->create()->getCustomer()->getGroupId();
        }
        return 0;
    }

    public function getSimpleProduct($simpleId){
        return $product = $this->_productFactory->load($simpleId);
    }
    public function getMinimumPrice($_product){
        $product_type_id = $_product->getTypeId();
        $minimumPrice = 0;

        if ($product_type_id == 'configurable' ) {

            $_children = $_product->getTypeInstance()->getUsedProducts($_product);
            $c= 0;
            foreach ($_children as $child) {
                $_simpleproduct = $this->getSimpleProduct($child->getId());
                $minPrice = $this->getSimpleProductMinimumPrice($_simpleproduct);
                $c++;
                if ($c == 1){
                    $minimumPrice = $_simpleproduct->getPrice();
                }
                if ($minPrice < $minimumPrice){
                    $minimumPrice = $minPrice;
                }
            }
        } elseif ($product_type_id == "simple") {
            $minimumPrice = $this->getSimpleProductMinimumPrice($_product);
        }elseif ($product_type_id == "virtual"){
            $minimumPrice = $this->getSimpleProductMinimumPrice($_product);
        }elseif ($product_type_id == "bundle"){
            $minimumPrice = $_product->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue();
           // $specialPrice = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();

        }
        elseif($product_type_id =='grouped'){
            $_children = $_product->getTypeInstance()->getAssociatedProducts($_product);
            $c= 0;
            foreach ($_children as $child) {
                $_simpleproduct = $this->getSimpleProduct($child->getId());
                $minPrice = $this->getSimpleProductMinimumPrice($_simpleproduct);
                $c++;
                if ($c == 1){
                    $minimumPrice = $_simpleproduct->getPrice();
                }
                if ($minPrice < $minimumPrice){
                    $minimumPrice = $minPrice;
                }
            }
        }
        return $minimumPrice;
    }
    public function getSimpleProductMinimumPrice($_product){
        $tier_price = $_product->getTierPrices();
        $minimumPrice = $_product->getPrice();


        if (!empty($_product->getSpecialPrice()) && $_product->getSpecialPrice() < $minimumPrice) {
            $minimumPrice = $_product->getSpecialPrice();
        }
        foreach ($tier_price as $price){
            if ($this->getCustomerId() == $price->getCustomerGroupId()) {
                if ($minimumPrice > $price->getValue()){
                    $minimumPrice = $price->getValue();
                }
            }
        }
        return $minimumPrice;
    }
}
