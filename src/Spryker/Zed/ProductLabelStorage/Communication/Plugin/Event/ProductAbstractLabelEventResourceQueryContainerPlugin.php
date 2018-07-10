<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelStorage\Communication\Plugin\Event;

use Orm\Zed\ProductLabel\Persistence\Map\SpyProductLabelProductAbstractTableMap;
use Spryker\Shared\ProductLabelStorage\ProductLabelStorageConfig;
use Spryker\Zed\EventBehavior\Dependency\Plugin\EventResourceQueryContainerPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductLabel\Dependency\ProductLabelEvents;
use Spryker\Zed\ProductLabel\Persistence\Propel\SpyProductLabelProductAbstractQuery;

/**
 * @method \Spryker\Zed\ProductLabelStorage\Persistence\ProductLabelStorageQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductLabelStorage\Business\ProductLabelStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductLabelStorage\Communication\ProductLabelStorageCommunicationFactory getFactory()
 */
class ProductAbstractLabelEventResourceQueryContainerPlugin extends AbstractPlugin implements EventResourceQueryContainerPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return string
     */
    public function getResourceName(): string
    {
        return ProductLabelStorageConfig::PRODUCT_ABSTRACT_LABEL_RESOURCE_NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int[] $ids
     *
     * @return \Spryker\Zed\ProductLabel\Persistence\Propel\SpyProductLabelProductAbstractQuery
     */
    public function queryData(array $ids = []): SpyProductLabelProductAbstractQuery
    {
        $query = $this->getQueryContainer()->queryProductLabelProductAbstractByIds($ids);

        if (empty($ids)) {
            $query->clear();
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return string
     */
    public function getEventName(): string
    {
        return ProductLabelEvents::PRODUCT_LABEL_PRODUCT_ABSTRACT_PUBLISH;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return string
     */
    public function getIdColumnName(): string
    {
        return SpyProductLabelProductAbstractTableMap::COL_FK_PRODUCT_ABSTRACT;
    }
}
