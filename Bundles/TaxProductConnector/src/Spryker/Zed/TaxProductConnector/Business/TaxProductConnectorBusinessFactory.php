<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxProductConnector\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\TaxProductConnector\Business\Plugin\TaxChangeTouchPlugin;
use Spryker\Zed\TaxProductConnector\Business\Product\ProductAbstractTaxSetMapper;
use Spryker\Zed\TaxProductConnector\Business\Product\ProductAbstractTaxWriter;
use Spryker\Zed\TaxProductConnector\TaxProductConnectorDependencyProvider;

/**
 * @method \Spryker\Zed\TaxProductConnector\TaxProductConnectorConfig getConfig()
 * @method \Spryker\Zed\TaxProductConnector\Persistence\TaxProductConnectorQueryContainer getQueryContainer()
 */
class TaxProductConnectorBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @return \Spryker\Zed\Tax\Dependency\Plugin\TaxChangePluginInterface
     */
    public function createTaxChangeTouchPlugin()
    {
        return new TaxChangeTouchPlugin(
            $this->getProductFacade(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return \Spryker\Zed\TaxProductConnector\Dependency\Facade\TaxProductConnectorToProductInterface
     */
    protected function getProductFacade()
    {
        return $this->getProvidedDependency(TaxProductConnectorDependencyProvider::FACADE_PRODUCT);
    }

    /**
     * @return \Spryker\Zed\TaxProductConnector\Business\Product\ProductAbstractTaxWriter
     */
    public function createProductAbstractTaxWriter()
    {
        return new ProductAbstractTaxWriter($this->getQueryContainer());
    }

    /**
     * @return \Spryker\Zed\TaxProductConnector\Business\Product\ProductAbstractTaxSetMapper
     */
    public function createProductAbstractTaxSetMapper()
    {
        return new ProductAbstractTaxSetMapper($this->getQueryContainer());
    }

}
