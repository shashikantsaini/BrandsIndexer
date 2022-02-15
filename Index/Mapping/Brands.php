<?php declare(strict_types=1);

namespace Bluethink\BrandsIndexer\Index\Mapping;

use Divante\VsbridgeIndexerCore\Api\Mapping\FieldInterface;
use Divante\VsbridgeIndexerCore\Api\MappingInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\DataObject;

/**
 * Class Brands
 */
class Brands implements MappingInterface
{

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var string
     */
    private $type;

    /**
     * Brands constructor.
     *
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getMappingProperties()
    {
        $properties = [
            'option_setting_id' => ['type' => FieldInterface::TYPE_INTEGER],
            'filter_code' => ['type' => FieldInterface::TYPE_TEXT],
            'value' => ['type' => FieldInterface::TYPE_INTEGER],
            'store_id' => ['type' => FieldInterface::TYPE_INTEGER],
            'url_alias' => ['type' => FieldInterface::TYPE_TEXT],
            'is_featured' => ['type' => FieldInterface::TYPE_INTEGER],
            'meta_title' => ['type' => FieldInterface::TYPE_TEXT],
            'meta_description' => ['type' => FieldInterface::TYPE_TEXT],
            'meta_keywords' => ['type' => FieldInterface::TYPE_TEXT],
            'title' => ['type' => FieldInterface::TYPE_TEXT],
            'description' => ['type' => FieldInterface::TYPE_TEXT],
            'image' => ['type' => FieldInterface::TYPE_TEXT],
            'top_cms_block_id' => ['type' => FieldInterface::TYPE_INTEGER],
            'bottom_cms_block_id' => ['type' => FieldInterface::TYPE_INTEGER],
            'slider_position' => ['type' => FieldInterface::TYPE_INTEGER],
            'slider_image' => ['type' => FieldInterface::TYPE_TEXT],
            'short_description' => ['type' => FieldInterface::TYPE_TEXT],
            'small_image_alt' => ['type' => FieldInterface::TYPE_TEXT],
            'is_show_in_slider' => ['type' => FieldInterface::TYPE_INTEGER],
        ];

        $mappingObject = new DataObject();
        $mappingObject->setData('properties', $properties);

        $this->eventManager->dispatch(
            'vsbridge_amasty_brands_mapping_properties',
            ['mapping' => $mappingObject]
        );

        return $mappingObject->getData();
    }
}
