<?php


namespace Magento360\CustomeName\Model;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento360\CustomeName\Model\CustomNameFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento360\CustomeName\Api\Data\CustomNameInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento360\CustomeName\Model\ResourceModel\CustomName\CollectionFactory;
use Magento360\CustomeName\Model\ResourceModel\CustomName as ObjectResourceModel;

/**
 * Class CustomNameRepository
 * Magento360\CustomeName\Model
 */
class CustomNameRepository implements \Magento360\CustomeName\Api\CustomNameRepositoryInterface
{

    protected $objectFactory;
    protected $objectResourceModel;
    protected $collectionFactory;
    protected $searchResultsFactory;

    /**
     * CarsRepository constructor.
     *
     * @param CustomNameFactory $objectFactory
     * @param ObjectResourceModel $objectResourceModel
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaInterface $searchResultsFactory
     */
    public function __construct(
        CustomNameFactory $objectFactory,
        ObjectResourceModel $objectResourceModel,
        CollectionFactory $collectionFactory,
        SearchResultsInterface $searchResultsFactory
    ) {
        $this->objectFactory        = $objectFactory;
        $this->objectResourceModel  = $objectResourceModel;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }


    /**
     * @param CustomNameInterface $object
     * @return CustomNameInterface
     * @throws CouldNotSaveException
     */
    public function save(CustomNameInterface $object)
    {
        try {
            $this->objectResourceModel->save($object);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $object;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {

        $object = $this->objectFactory->create();
        $this->objectResourceModel->load($object, $id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $object;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory;
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);
        return $searchResults;
    }

    /**
     * @param CustomNameInterface $object
     * @return mixed
     * @throws CouldNotDeleteException
     */
    public function delete(CustomNameInterface $object)
    {
        try {
            $this->objectResourceModel->delete($object);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException|CouldNotDeleteException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @param $customer_id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getByCustomerId($customer_id)
    {
        $object = $this->objectFactory->create();
        $objectId = $this->objectResourceModel->getByCustomerId($customer_id);
        if (!$objectId) {
            throw new NoSuchEntityException(
                __('Object with id "%1" does not exist.', $objectId)
            );
        }
        $object->load($objectId);
        $cachedobject = $object;
        return $cachedobject;
    }
    public function getValuesByCustomerId($customer_id)
    {
        $object = $this->objectFactory->create();
        $objectId = $this->objectResourceModel->getAllIdsByCustomerId($customer_id);
        if (!$objectId) {
            throw new NoSuchEntityException(
                __('Object with id "%1" does not exist.', $objectId)
            );
        }
        $object->load($objectId);
        $cachedobject = $object;
        return $cachedobject;
    }


}
