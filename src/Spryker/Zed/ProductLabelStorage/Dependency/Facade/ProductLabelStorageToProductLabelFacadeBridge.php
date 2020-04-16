<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelStorage\Dependency\Facade;

use Generated\Shared\Transfer\FilterTransfer;

class ProductLabelStorageToProductLabelFacadeBridge implements ProductLabelStorageToProductLabelFacadeInterface
{
    /**
     * @var \Spryker\Zed\ProductLabel\Business\ProductLabelFacadeInterface
     */
    protected $productLabelFacade;

    /**
     * @param \Spryker\Zed\ProductLabel\Business\ProductLabelFacadeInterface $productLabelFacade
     */
    public function __construct($productLabelFacade)
    {
        $this->productLabelFacade = $productLabelFacade;
    }

    /**
     * @param int[] $productAbstractIds
     *
     * @return \Generated\Shared\Transfer\ProductLabelProductAbstractTransfer[]
     */
    public function getProductLabelProductAbstractsByProductAbstractIds(array $productAbstractIds): array
    {
        return $this->productLabelFacade->getProductLabelProductAbstractsByProductAbstractIds($productAbstractIds);
    }

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     *
     * @return \Generated\Shared\Transfer\ProductLabelProductAbstractTransfer[]
     */
    public function getProductLabelProductAbstractsByFilter(FilterTransfer $filterTransfer): array
    {
        return $this->productLabelFacade->getProductLabelProductAbstractsByFilter($filterTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\ProductLabelLocalizedAttributesTransfer[]
     */
    public function getProductLabelLocalizedAttributes(): array
    {
        return $this->productLabelFacade->getProductLabelLocalizedAttributes();
    }
}
