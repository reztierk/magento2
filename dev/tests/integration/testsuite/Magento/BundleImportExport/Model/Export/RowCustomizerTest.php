<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleImportExport\Model\Export;

/**
 * @magentoAppArea adminhtml
 */
class RowCustomizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\BundleImportExport\Model\Export\RowCustomizer
     */
    private $model;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(
            \Magento\BundleImportExport\Model\Export\RowCustomizer::class
        );
    }

    /**
     * @magentoDataFixture Magento/Bundle/_files/product.php
     */
    public function testPrepareData()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->objectManager->get(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        $select = $collection->getConnection()->select()
            ->from(['p' => $collection->getTable('catalog_product_entity')], ['sku', 'entity_id'])
            ->where('sku IN(?)', ['simple', 'custom-design-simple-product', 'bundle-product']);
        $ids = $collection->getConnection()->fetchPairs($select);
        $select = (string)$collection->getSelect();
        $this->model->prepareData($collection, array_values($ids));
        $this->assertEquals($select, (string)$collection->getSelect());
        $result = $this->model->addData([], $ids['bundle-product']);
        $this->assertArrayHasKey('bundle_price_type', $result);
        $this->assertArrayHasKey('bundle_shipment_type', $result);
        $this->assertArrayHasKey('bundle_sku_type', $result);
        $this->assertArrayHasKey('bundle_price_view', $result);
        $this->assertArrayHasKey('bundle_weight_type', $result);
        $this->assertArrayHasKey('bundle_values', $result);
        $this->assertContains('sku=simple,', $result['bundle_values']);
        $this->assertEquals([], $this->model->addData([], $ids['simple']));
    }
}
