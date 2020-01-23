<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductLabelStorage\Storage\Dictionary;

use Generated\Shared\Transfer\ProductLabelDictionaryStorageTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Client\ProductLabelStorage\Dependency\Client\ProductLabelStorageToStorageClientInterface;
use Spryker\Client\ProductLabelStorage\Dependency\Service\ProductLabelStorageToSynchronizationServiceInterface;
use Spryker\Client\ProductLabelStorage\ProductLabelStorageConfig;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\ProductLabelStorage\ProductLabelStorageConfig as SharedProductLabelStorageConfig;

class LabelDictionary implements LabelDictionaryInterface
{
    /**
     * @var \Spryker\Client\ProductLabelStorage\Dependency\Client\ProductLabelStorageToStorageClientInterface
     */
    protected $storageClient;

    /**
     * @var \Spryker\Client\ProductLabelStorage\Storage\Dictionary\KeyStrategyInterface
     */
    protected $dictionaryKeyStrategy;

    /**
     * @var \Spryker\Client\ProductLabelStorage\Dependency\Service\ProductLabelStorageToSynchronizationServiceInterface
     */
    protected $synchronizationService;

    /**
     * @param \Spryker\Client\ProductLabelStorage\Dependency\Client\ProductLabelStorageToStorageClientInterface $storageClient
     * @param \Spryker\Client\ProductLabelStorage\Dependency\Service\ProductLabelStorageToSynchronizationServiceInterface $synchronizationService
     * @param \Spryker\Client\ProductLabelStorage\Storage\Dictionary\KeyStrategyInterface $dictionaryKeyStrategy
     */
    public function __construct(
        ProductLabelStorageToStorageClientInterface $storageClient,
        ProductLabelStorageToSynchronizationServiceInterface $synchronizationService,
        KeyStrategyInterface $dictionaryKeyStrategy
    ) {
        $this->storageClient = $storageClient;
        $this->dictionaryKeyStrategy = $dictionaryKeyStrategy;
        $this->synchronizationService = $synchronizationService;
    }

    /**
     * @param string $dictionaryKey
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer|null
     */
    public function findLabel($dictionaryKey, string $localeName, ?string $storeName = null)
    {
        $dictionary = $this->getDictionary($localeName, $storeName);

        if (!array_key_exists($dictionaryKey, $dictionary)) {
            return null;
        }

        return $dictionary[$dictionaryKey];
    }

    /**
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer[]
     */
    public function getDictionary(string $localeName, ?string $storeName = null)
    {
        static $labelDictionary = [];

        $strategy = get_class($this->dictionaryKeyStrategy);

        if (!isset($labelDictionary[$strategy])) {
            $labelDictionary[$strategy] = $this->initializeLabelDictionary($localeName, $storeName);
        }

        return $labelDictionary[$strategy];
    }

    /**
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryItemTransfer[]
     */
    protected function initializeLabelDictionary(string $localeName, ?string $storeName = null)
    {
        $labelDictionary = [];

        $productLabelDictionaryStorageTransfer = $this->readLabelDictionary($localeName, $storeName);
        foreach ($productLabelDictionaryStorageTransfer->getItems() as $productLabelDictionaryItemTransfer) {
            $dictionaryKey = $this->dictionaryKeyStrategy->getDictionaryKey($productLabelDictionaryItemTransfer);
            $labelDictionary[$dictionaryKey] = $productLabelDictionaryItemTransfer;
        }

        return $labelDictionary;
    }

    /**
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return \Generated\Shared\Transfer\ProductLabelDictionaryStorageTransfer
     */
    protected function readLabelDictionary($localeName, ?string $storeName = null)
    {
        $productLabelDictionaryStorageTransfer = new ProductLabelDictionaryStorageTransfer();

        $productLabelDictionaryStorageData = $this->getStorageData($localeName, $storeName);

        if (!$productLabelDictionaryStorageData) {
            return $productLabelDictionaryStorageTransfer;
        }

        $productLabelDictionaryStorageTransfer->fromArray($productLabelDictionaryStorageData, true);

        return $productLabelDictionaryStorageTransfer;
    }

    /**
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return array|null
     */
    protected function getStorageData(string $localeName, ?string $storeName = null)
    {
        if (ProductLabelStorageConfig::isCollectorCompatibilityMode()) {
            $productLabelConstantsClassName = '\Spryker\Shared\ProductLabel\ProductLabelConstants';
            $collectorStorageKey = sprintf(
                '%s.%s.resource.product_label_dictionary.%s',
                strtolower(Store::getInstance()->getStoreName()),
                strtolower($localeName),
                $productLabelConstantsClassName::RESOURCE_TYPE_PRODUCT_LABEL_DICTIONARY_IDENTIFIER
            );
            $collectorData = $this->storageClient->get($collectorStorageKey);
            $formatted = [
                'items' => $collectorData,
            ];

            return $formatted;
        }

        $storageKey = $this->getStorageKey($localeName, $storeName);
        $productLabelDictionaryStorageData = $this->storageClient->get($storageKey);

        return $productLabelDictionaryStorageData;
    }

    /**
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return string
     */
    protected function getStorageKey($localeName, ?string $storeName = null)
    {
        $synchronizationDataTransfer = new SynchronizationDataTransfer();
        $synchronizationDataTransfer
            ->setLocale($localeName);

        return $this->synchronizationService
            ->getStorageKeyBuilder(SharedProductLabelStorageConfig::PRODUCT_LABEL_DICTIONARY_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }
}
