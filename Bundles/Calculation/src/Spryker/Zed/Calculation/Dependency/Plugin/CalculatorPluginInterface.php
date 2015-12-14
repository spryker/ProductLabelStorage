<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Calculation\Dependency\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;

interface CalculatorPluginInterface
{

    /**
     * @param QuoteTransfer $quoteTransfer
     */
    public function recalculate(QuoteTransfer $quoteTransfer);

}
