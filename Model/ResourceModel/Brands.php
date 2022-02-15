<?php

namespace Bluethink\BrandsIndexer\Model\ResourceModel;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityMetadata;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\Store;

/**
 * Class Brands
 */
class Brands
{
    /**
     * @var MetadataPool
     */
    private $metaDataPool;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resource = $resourceConnection;
        $this->metaDataPool = $metadataPool;
    }

    /**
     * @param int $storeId
     * @param array $optionIds
     * @param int $fromId
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function loadBrands($storeId = 1, array $optionIds = [], $fromId = 0, $limit = 1000)
    {
        $metaData = $this->getBrandsMetaData();
        $select = $this->getConnection()->select()
            ->from(['main_table' => $metaData->getEntityTable()]);

        if (!empty($optionIds)) {
            $select->where('main_table.option_setting_id IN (?)', $optionIds);
        }

        $select->where(
            'main_table.store_id IN (?)',
            [Store::DEFAULT_STORE_ID, $storeId]
        );

        $select->group('option_setting_id');
        $select = $this->addTitleToCollection($select);

        $select->where('main_table.option_setting_id > ?', $fromId)
            ->limit($limit)
            ->order('main_table.option_setting_id');

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @return EntityMetadata|EntityMetadataInterface
     * @throws \Exception
     */
    private function getBrandsMetaData()
    {
        return $this->metaDataPool->getMetadata(OptionSettingInterface::class);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }

    /**
     * @param $select
     * @return mixed
     */
    private function addTitleToCollection($select)
    {
        $select->joinInner(
            ['amshopbybrand_option' => $this->getConnection()->getTableName('eav_attribute_option')],
            'main_table.value = amshopbybrand_option.option_id',
            []
        );
        $select->join(
            ['option' => $this->getConnection()->getTableName('eav_attribute_option_value')],
            'option.option_id = main_table.value'
        );
        $select->columns('IF(main_table.title = "", option.value, main_table.title) as title');
        $select->columns(
            'IF(main_table.meta_title = "", option.value, main_table.meta_title) as meta_title'
        );

        $select->group('option_setting_id');

        return $select;
    }
}
