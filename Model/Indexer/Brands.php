<?php

namespace Bluethink\BrandsIndexer\Model\Indexer;

use Magento\Framework\Indexer\ActionInterface as IndexerInterface;
use Magento\Framework\Mview\ActionInterface as MviewInterface;
use Divante\VsbridgeIndexerCore\Indexer\GenericIndexerHandler;
use Divante\VsbridgeIndexerCore\Indexer\StoreManager;
use Bluethink\BrandsIndexer\Model\Indexer\Action\BrandsAction as BrandIndexerAction;

/**
 * Class Brands
 */
class Brands implements IndexerInterface, MviewInterface
{
    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var GenericIndexerHandler
     */
    private $indexHandler;

    /**
     * @var BrandIndexerAction
     */
    private $brandIndexerAction;

    /**
     * @param GenericIndexerHandler $indexerHandler
     * @param StoreManager $storeManager
     * @param BrandIndexerAction $action
     */
    public function __construct(
        GenericIndexerHandler $indexerHandler,
        StoreManager $storeManager,
        BrandIndexerAction $action
    ) {
        $this->indexHandler = $indexerHandler;
        $this->storeManager = $storeManager;
        $this->brandIndexerAction = $action;
    }

    /**
     * @return void
     * @throws \Divante\VsbridgeIndexerCore\Exception\ConnectionUnhealthyException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function executeFull()
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $this->indexHandler->saveIndex($this->brandIndexerAction->rebuild($store->getId()), $store);
            $this->indexHandler->cleanUpByTransactionKey($store);
        }

    }

    /**
     * @param array $ids
     * @return void
     * @throws \Divante\VsbridgeIndexerCore\Exception\ConnectionUnhealthyException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @param $ids
     * @return void
     * @throws \Divante\VsbridgeIndexerCore\Exception\ConnectionUnhealthyException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($ids)
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $this->indexHandler->saveIndex($this->brandIndexerAction->rebuild($store->getId(), $ids), $store);
            $this->indexHandler->cleanUpByTransactionKey($store, $ids);
        }

    }

    /**
     * @param $id
     * @return void
     * @throws \Divante\VsbridgeIndexerCore\Exception\ConnectionUnhealthyException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }
}
