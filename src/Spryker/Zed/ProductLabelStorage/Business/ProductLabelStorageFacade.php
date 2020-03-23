<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelStorage\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\ProductLabelStorage\Business\ProductLabelStorageBusinessFactory getFactory()
 * @method \Spryker\Zed\ProductLabelStorage\Persistence\ProductLabelStorageEntityManager getEntityManager()
 * @method \Spryker\Zed\ProductLabelStorage\Persistence\ProductLabelStorageRepository getRepository()
 */
class ProductLabelStorageFacade extends AbstractFacade implements ProductLabelStorageFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $productLabelIds
     *
     * @return void
     */
    public function publishLabelDictionary(array $productLabelIds)
    {
        $this->getFactory()->createProductLabelDictionaryStorageWriter()->publish($productLabelIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $productLabelIds
     *
     * @return void
     */
    public function unpublishLabelDictionary(array $productLabelIds)
    {
        $this->getFactory()->createProductLabelDictionaryStorageWriter()->unpublish($productLabelIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $productAbstractIds
     *
     * @return void
     */
    public function publishProductLabel(array $productAbstractIds)
    {
        $this->getFactory()->createProductLabelStorageWriter()->publish($productAbstractIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $productAbstractIds
     *
     * @return void
     */
    public function unpublishProductLabel(array $productAbstractIds)
    {
        $this->getFactory()->createProductLabelStorageWriter()->unpublish($productAbstractIds);
    }
}
