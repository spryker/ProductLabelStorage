<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductLabelStorage\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\ProductAbstractLabelStorageTransfer;
use Generated\Shared\Transfer\ProductLabelDictionaryStorageTransfer;
use Orm\Zed\ProductLabel\Persistence\Map\SpyProductLabelProductAbstractTableMap;
use Orm\Zed\ProductLabel\Persistence\Map\SpyProductLabelStoreTableMap;
use Spryker\Client\Kernel\Container;
use Spryker\Client\Queue\QueueDependencyProvider;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductLabelStorage
 * @group Business
 * @group Facade
 * @group ProductLabelStorageFacadeTest
 * Add your own group annotations below this line
 */
class ProductLabelStorageFacadeTest extends Unit
{
    protected const ID_PRODUCT_LABEL_DICTIONARY_STORAGE = 1;

    protected const STORE_NAME_DE = 'DE';
    protected const LOCALE_NAME_EN = 'en_US';

    /**
     * @var \SprykerTest\Zed\ProductLabelStorage\ProductLabelStorageBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->setDependency(QueueDependencyProvider::QUEUE_ADAPTERS, function (Container $container) {
            return [
                $container->getLocator()->rabbitMq()->client()->createQueueAdapter(),
            ];
        });
    }

    /**
     * @return void
     */
    public function testWriteProductLabelDictionaryStorageCollectionPersistsStorageEntity(): void
    {
        //Arrange
        $this->tester->cleanupProductLabelDictionaryStorage(static::ID_PRODUCT_LABEL_DICTIONARY_STORAGE);
        $productLabelDictionaryStorageBeforeCount = $this->tester->createProductLabelDictionaryStorageQuery()->count();

        //Act
        $this->tester->getFacade()->writeProductLabelDictionaryStorageCollection();

        //Assert
        $productLabelDictionaryStorageAfterCount = $this->tester->createProductLabelDictionaryStorageQuery()->count();
        $this->tester->assertGreaterThan(
            $productLabelDictionaryStorageBeforeCount,
            $productLabelDictionaryStorageAfterCount,
            'Product Label Dictionary Storage record count is less then expected value.'
        );
    }

    /**
     * @return void
     */
    public function testDeleteProductLabelDictionaryStorageCollectionEmptiesStorageTable(): void
    {
        //Arrange
        $this->tester->haveProductLabelDictionaryStorage([
            ProductLabelDictionaryStorageTransfer::STORE => static::STORE_NAME_DE,
            ProductLabelDictionaryStorageTransfer::LOCALE => static::LOCALE_NAME_EN,
        ]);

        //Act
        $this->tester->getFacade()->deleteProductLabelDictionaryStorageCollection();

        //Assert
        $productLabelDictionaryStorageAfterCount = $this->tester->createProductLabelDictionaryStorageQuery()->count();
        $this->tester->assertEquals(
            0,
            $productLabelDictionaryStorageAfterCount,
            'Product Label Dictionary Storage record count does not equals to an expected value.'
        );
    }

    /**
     * @return void
     */
    public function testWriteProductAbstractLabelStorageCollectionByProductAbstractLabelEventsPersistStorageEntity(): void
    {
        //Arrange
        $productLabelTransfer = $this->tester->haveProductLabel();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveProductLabelToAbstractProductRelation(
            $productLabelTransfer->getIdProductLabel(),
            $productAbstractTransfer->getIdProductAbstract()
        );

        $this->tester->cleanupProductAbstractLabelStorage($productAbstractTransfer->getIdProductAbstract());

        $eventTransfers = [
            (new EventEntityTransfer())->setId($productAbstractTransfer->getIdProductAbstract()),
        ];

        //Act
        $this->tester->getFacade()
            ->writeProductAbstractLabelStorageCollectionByProductAbstractLabelEvents($eventTransfers);

        //Assert
        $this->assertTrue(
            $this->tester->isProductAbstractLabelStorageRecordExists($productAbstractTransfer->getIdProductAbstract()),
            'Product abstract label storage record should exists.'
        );
    }

    /**
     * @return void
     */
    public function testWriteProductAbstractLabelStorageCollectionByProductLabelProductAbstractEventsPersistStorageEntity(): void
    {
        //Arrange
        $productLabelTransfer = $this->tester->haveProductLabel();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveProductLabelToAbstractProductRelation(
            $productLabelTransfer->getIdProductLabel(),
            $productAbstractTransfer->getIdProductAbstract()
        );

        $this->tester->cleanupProductAbstractLabelStorage($productAbstractTransfer->getIdProductAbstract());

        $eventTransfers = [
            (new EventEntityTransfer())->setForeignKeys([
                SpyProductLabelProductAbstractTableMap::COL_FK_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
            ]),
        ];

        //Act
        $this->tester->getFacade()->writeProductAbstractLabelStorageCollectionByProductLabelProductAbstractEvents($eventTransfers);

        //Assert
        $this->assertTrue(
            $this->tester->isProductAbstractLabelStorageRecordExists($productAbstractTransfer->getIdProductAbstract()),
            'Product abstract label storage record should exists.'
        );
    }

    /**
     * @return void
     */
    public function testWriteProductAbstractLabelStorageCollectionByProductLabelStoreEventsPersistStorageEntity(): void
    {
        //Arrange
        $productLabelTransfer = $this->tester->haveProductLabel();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveProductLabelToAbstractProductRelation(
            $productLabelTransfer->getIdProductLabel(),
            $productAbstractTransfer->getIdProductAbstract()
        );

        $this->tester->cleanupProductAbstractLabelStorage($productAbstractTransfer->getIdProductAbstract());

        $eventTransfers = [
            (new EventEntityTransfer())->setForeignKeys([
                SpyProductLabelStoreTableMap::COL_FK_PRODUCT_LABEL => $productLabelTransfer->getIdProductLabel(),
            ]),
        ];

        //Act
        $this->tester->getFacade()->writeProductAbstractLabelStorageCollectionByProductLabelStoreEvents($eventTransfers);

        //Assert
        $this->assertTrue(
            $this->tester->isProductAbstractLabelStorageRecordExists($productAbstractTransfer->getIdProductAbstract()),
            'Product abstract label storage record should exists.'
        );
    }

    /**
     * @return void
     */
    public function testGetProductAbstractLabelStorageDataTransfersByIdsWillReturnSynchronizationDataTransfers(): void
    {
        //Arrange
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $productAbstractLabelTransfer = $this->tester->haveProductAbstractLabelStorage([
            ProductAbstractLabelStorageTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);
        $productAbstractLabelStorageIds = $this->tester->getProductAbstractLAbelStorageIdsByIdProductAbstract(
            $productAbstractLabelTransfer->getIdProductAbstract()
        );

        $filterTransfer = $this->createFilterTransfer();

        //Act
        $synchronizationDataTransfers = $this->tester->getFacade()->getProductAbstractLabelStorageDataTransfersByIds(
            $filterTransfer,
            $productAbstractLabelStorageIds
        );

        $this->assertCount(
            1,
            $synchronizationDataTransfers,
            'Number of synchronisation data transfers is not equals to an expected value.'
        );
    }

    /**
     * @return void
     */
    public function testGetProductLabelDictionaryStorageDataTransfersByIdsWillReturnSynchronizationDataTransfers(): void
    {
        //Arrange
        $productLabelDictionaryStorageTransfer = $this->tester->haveProductLabelDictionaryStorage([
            ProductLabelDictionaryStorageTransfer::STORE => static::STORE_NAME_DE,
            ProductLabelDictionaryStorageTransfer::LOCALE => static::LOCALE_NAME_EN,
        ]);

        $filterTransfer = $this->createFilterTransfer();

        //Act
        $synchronizationDataTransfers = $this->tester->getFacade()->getProductLabelDictionaryStorageDataTransfersByIds(
            $filterTransfer,
            [$productLabelDictionaryStorageTransfer->getIdProductLabelDictionaryStorage()]
        );

        $this->assertCount(
            1,
            $synchronizationDataTransfers,
            'Number of synchronisation data transfers is not equals to an expected value.'
        );
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return \Generated\Shared\Transfer\FilterTransfer
     */
    protected function createFilterTransfer(int $offset = 0, int $limit = 100): FilterTransfer
    {
        return (new FilterTransfer())
            ->setOffset($offset)
            ->setLimit($limit);
    }
}
