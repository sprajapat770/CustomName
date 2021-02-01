<?php
namespace Magento360\CustomeName\Controller\Customer;

/**
 * Class Index
 * Magento360\CustomeName\Controller\Customer
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Execute action based on request and return result
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
