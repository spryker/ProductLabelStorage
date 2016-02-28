<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Library\BatchIterator;

use Propel\Runtime\ActiveQuery\ModelCriteria;

class PropelBatchIterator implements CountableIteratorInterface
{

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $chunkSize = 100;

    /**
     * @var \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected $query;

    /**
     * @var bool
     */
    protected $isValid = true;

    /**
     * @var array
     */
    protected $currentDataSet = [];

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param int $chunkSize
     */
    public function __construct(ModelCriteria $query, $chunkSize = 100)
    {
        $this->query = $query;
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return void
     */
    protected function loadData()
    {
        $this->query->setOffset($this->offset);
        $this->query->setLimit($this->chunkSize);

        $this->currentDataSet = $this->query->find();
        $this->isValid = $this->currentDataSet->count() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->currentDataSet;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function next()
    {
        $this->loadData();
        $this->offset += $this->chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->isValid;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function rewind()
    {
        $this->offset = 0;
        $this->loadData();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->query->setLimit(-1);
        $this->query->setOffset(-1);

        return $this->query->count();
    }

}
