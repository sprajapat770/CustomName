<?php


namespace Magento360\CustomeName\Model;

use Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class CustomNameList
 * Magento360\CustomeName\Model
 */
class CustomNameList extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var CustomName
     */
    protected $_list;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var PersonalInfo
     */
    private $personalInfo;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * Customer data factory
     *
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * Customer
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customer;


    /**
     * Data object helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var \Magento\Backend\Model\Session
     */
    private $backendSession;


    public function __construct(
        ModuleManager $moduleManager,
        CustomName $list,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Backend\Model\Session $backendSession,
        PersonalInfo $personalInfo
    ) {
        $this->_list = $list;
        $this->moduleManager = $moduleManager;
        $this->personalInfo = $personalInfo;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerDataFactory = $customerDataFactory;
        $this->_backendSession = $backendSession;
    }

    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }

        $customerId = !empty($id = $this->getCustomer()->getId())? $id:'';

        $names = $this->_list->getCollection()->addFieldToFilter('customer_id', $customerId);

        $listnames = [];
        foreach ($names as $name) {
            $listnames[] = ['label' => $name->getValue(),
                'value' => $name->getEntityId()];
        }
        return $listnames;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = [];
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, [
                'value' => '',
                'label' => '',
            ]);
        }
        return $options;
    }

    public function getCustomer()
    {
        if (!$this->customer) {
            $this->customer = $this->customerDataFactory->create();
            $data = $this->_backendSession->getCustomerData();

            $this->dataObjectHelper->populateWithArray(
                $this->customer,
                !empty($data['account'])?$data['account'] :[],
                \Magento\Customer\Api\Data\CustomerInterface::class
            );
        }
        return $this->customer;
    }
}
