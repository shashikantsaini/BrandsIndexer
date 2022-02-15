<?php declare(strict_types=1);

namespace Bluethink\BrandsIndexer\Plugin\Indexer\Save;

use Amasty\ShopbyBase\Model\OptionSetting;
use Bluethink\BrandsIndexer\Model\Indexer\BrandsProcessor;

/**
 * Class UpdateBrands
 */
class UpdateBrands
{
    /**
     * @var BrandsProcessor
     */
    private $brandsProcessor;

    /**
     * UpdateBrands constructor.
     *
     * @param BrandsProcessor $brandsProcessor
     */
    public function __construct(
        BrandsProcessor $brandsProcessor
    ) {
        $this->brandsProcessor = $brandsProcessor;
    }


    /**
     * @param OptionSetting $brands
     * @param OptionSetting $result
     * @return OptionSetting
     */
    public function afterAfterSave(OptionSetting $brands, OptionSetting $result)
    {
        $result->getResource()->addCommitCallback(function () use ($brands) {
            $this->brandsProcessor->reindexRow($brands->getId());
        });

        return $result;
    }

    /**
     * @param OptionSetting $brands
     * @param OptionSetting $result
     * @return OptionSetting
     */
    public function afterAfterDeleteCommit(OptionSetting $brands, OptionSetting $result)
    {
        $this->brandsProcessor->reindexRow($brands->getId());

        return $result;
    }
}
