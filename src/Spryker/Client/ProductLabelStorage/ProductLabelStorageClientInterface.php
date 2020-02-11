<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductLabelStorage;

interface ProductLabelStorageClientInterface
{
    /**
     * Specification:
     * - TODO: add specification
     *
     * @api
     *
     * @param int $idProductAbstract
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer[]
     */
    public function findLabelsByIdProductAbstract($idProductAbstract, $localeName);

    /**
     * Specification:
     * - Retrieves product labels by abstract product IDs and by locale.
     * - Returns array of ProductLabelDictionaryItemTransfers indexed by id of product abstract.
     *
     * @api
     *
     * @param int[] $productAbstractIds
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer[][]
     */
    public function getProductLabelsByProductAbstractIds(array $productAbstractIds, string $localeName): array;

    /**
     * Specification:
     * - TODO: add specification
     *
     * @api
     *
     * @param array $idProductLabels
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer[]
     */
    public function findLabels(array $idProductLabels, $localeName);

    /**
     * Specification:
     * - Retrieves label for given label name, locale and store name.
     * - Forward compatibility: only label assigned with passed $storeName will be returned.
     *
     * @api
     *
     * @param string $labelName
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer|null
     */
    public function findLabelByName($labelName, $localeName, ?string $storeName = null);
}
