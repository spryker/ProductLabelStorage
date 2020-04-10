<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelStorage\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\ProductLabelStorage\Business\Deleter\ProductAbstractLabelStorageDeleter;
use Spryker\Zed\ProductLabelStorage\Business\Deleter\ProductAbstractLabelStorageDeleterInterface;
use Spryker\Zed\ProductLabelStorage\Business\Deleter\ProductLabelDictionaryStorageDeleter;
use Spryker\Zed\ProductLabelStorage\Business\Deleter\ProductLabelDictionaryStorageDeleterInterface;
use Spryker\Zed\ProductLabelStorage\Business\Writer\ProductAbstractLabelStorageWriter;
use Spryker\Zed\ProductLabelStorage\Business\Writer\ProductAbstractLabelStorageWriterInterface;
use Spryker\Zed\ProductLabelStorage\Business\Writer\ProductLabelDictionaryStorageWriter;
use Spryker\Zed\ProductLabelStorage\Business\Writer\ProductLabelDictionaryStorageWriterInterface;
use Spryker\Zed\ProductLabelStorage\Dependency\Facade\ProductLabelStorageToProductLabelFacadeInterface;
use Spryker\Zed\ProductLabelStorage\ProductLabelStorageDependencyProvider;

/**
 * @method \Spryker\Zed\ProductLabelStorage\Persistence\ProductLabelStorageEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\ProductLabelStorage\ProductLabelStorageConfig getConfig()
 * @method \Spryker\Zed\ProductLabelStorage\Persistence\ProductLabelStorageRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductLabelStorage\Persistence\ProductLabelStorageQueryContainerInterface getQueryContainer()
 */
class ProductLabelStorageBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\ProductLabelStorage\Business\Writer\ProductLabelDictionaryStorageWriterInterface
     */
    public function createProductLabelDictionaryStorageWriter(): ProductLabelDictionaryStorageWriterInterface
    {
        return new ProductLabelDictionaryStorageWriter(
            $this->getProductLabelFacade(),
            $this->getRepository(),
            $this->getEntityManager()
        );
    }

    /**
     * @return \Spryker\Zed\ProductLabelStorage\Business\Deleter\ProductLabelDictionaryStorageDeleterInterface
     */
    public function createProductLabelDictionaryStorageDeleter(): ProductLabelDictionaryStorageDeleterInterface
    {
        return new ProductLabelDictionaryStorageDeleter($this->getEntityManager());
    }

    /**
     * @return \Spryker\Zed\ProductLabelStorage\Business\Writer\ProductAbstractLabelStorageWriterInterface
     */
    public function createProductAbstractLabelStorageWriter(): ProductAbstractLabelStorageWriterInterface
    {
        return new ProductAbstractLabelStorageWriter(
            $this->getEventBehaviorFacade(),
            $this->getProductLabelFacade(),
            $this->getRepository(),
            $this->getEntityManager()
        );
    }

    /**
     * @return \Spryker\Zed\ProductLabelStorage\Business\Deleter\ProductAbstractLabelStorageDeleterInterface
     */
    public function createProductAbstractLabelStorageDeleter(): ProductAbstractLabelStorageDeleterInterface
    {
        return new ProductAbstractLabelStorageDeleter($this->getEntityManager());
    }

    /**
     * @return \Spryker\Zed\ProductLabelStorage\Dependency\Facade\ProductLabelStorageToEventBehaviorFacadeInterface
     */
    public function getEventBehaviorFacade()
    {
        return $this->getProvidedDependency(ProductLabelStorageDependencyProvider::FACADE_EVENT_BEHAVIOR);
    }

    /**
     * @return \Spryker\Zed\ProductLabelStorage\Dependency\Facade\ProductLabelStorageToProductLabelFacadeInterface
     */
    public function getProductLabelFacade(): ProductLabelStorageToProductLabelFacadeInterface
    {
        return $this->getProvidedDependency(ProductLabelStorageDependencyProvider::FACADE_PRODUCT_LABEL);
    }
}
