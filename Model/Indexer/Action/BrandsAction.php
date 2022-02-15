<?php

namespace Bluethink\BrandsIndexer\Model\Indexer\Action;

use Bluethink\BrandsIndexer\Model\ResourceModel\Brands as BrandsResource;
use Divante\VsbridgeIndexerCms\Model\Indexer\DataProvider\CmsContentFilter;
use Magebit\StaticContentProcessor\Helper\Resolver;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;

/**
 * Class BrandsAction
 */
class BrandsAction
{
    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var CmsContentFilter
     */
    private $cmsContentFilter;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var BrandsResource
     */
    private $resourceModel;

    /**
     * @param AreaList $areaList
     * @param BrandsResource $brandsResource
     * @param FilterProvider $filterProvider
     * @param CmsContentFilter $cmsContentFilter
     * @param Resolver $resolver
     */
    public function __construct(
        AreaList $areaList,
        BrandsResource $brandsResource,
        FilterProvider $filterProvider,
        CmsContentFilter $cmsContentFilter,
        Resolver $resolver
    ) {
        $this->areaList = $areaList;
        $this->filterProvider = $filterProvider;
        $this->resourceModel = $brandsResource;
        $this->cmsContentFilter = $cmsContentFilter;
        $this->resolver = $resolver;
    }

    /**
     * @param $storeId
     * @param array $optionIds
     * @return \Generator
     */
    public function rebuild($storeId = 1, array $optionIds = [])
    {
        $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);
        $lastId = 0;

        do {
            $brands = $this->resourceModel->loadBrands($storeId, $optionIds, $lastId);

            foreach ($brands as $brandData) {
                $brandData['option_setting_id'] = (int)$brandData['option_setting_id'];
                $lastId = $brandData['option_setting_id'];

                $description = $this->processBrandsData($brandData['description'] ? $brandData['description'] : '', (int) $storeId);
                $shortDescription = $this->processBrandsData($brandData['short_description'] ? $brandData['short_description'] : '', (int) $storeId);

                $brandData['value'] = (int)$brandData['value'];
                $brandData['store_id'] = (int)$brandData['store_id'];
                $brandData['is_featured'] = (int)$brandData['is_featured'];
                $brandData['description'] = $this->resolver->resolve($description, (int) $storeId);
                $brandData['top_cms_block_id'] = (int)$brandData['top_cms_block_id'];
                $brandData['bottom_cms_block_id'] = (int)$brandData['bottom_cms_block_id'];
                $brandData['slider_position'] = (int)$brandData['slider_position'];
                $brandData['short_description'] = $this->resolver->resolve($shortDescription, (int) $storeId);
                $brandData['is_show_in_slider'] = (int)$brandData['is_show_in_slider'];

                yield $lastId => $brandData;
            }
        } while (!empty($brands));
    }


    /**
     * Filters m2 cms content
     *
     * @param string $string
     * @param int $storeId
     * @return mixed|null
     */
    protected function processBrandsData(string $string, int $storeId)
    {
        $filterData = [[ 'content' => $string]];
        $processed = $this->cmsContentFilter->filter($filterData, $storeId, 'block');

        if (isset($processed[0]['content'])) {
            return $processed[0]['content'];
        }

        return null;
    }
}
