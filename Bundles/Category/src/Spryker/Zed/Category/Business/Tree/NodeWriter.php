<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Category\Business\Tree;

use Generated\Shared\Transfer\NodeTransfer;
use Orm\Zed\Category\Persistence\SpyCategoryNode;
use Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface;

class NodeWriter implements NodeWriterInterface
{

    /**
     * @deprecated This is not in use anymore
     */
    const CATEGORY_URL_IDENTIFIER_LENGTH = 4;

    /**
     * @var \Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface $queryContainer
     */
    public function __construct(CategoryQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\NodeTransfer $categoryNodeTransfer
     *
     * @return int
     */
    public function create(NodeTransfer $categoryNodeTransfer)
    {
        $categoryNodeEntity = new SpyCategoryNode();
        $categoryNodeEntity->fromArray($categoryNodeTransfer->toArray());
        $categoryNodeEntity->save();

        $idCategoryNode = $categoryNodeEntity->getIdCategoryNode();
        $categoryNodeTransfer->setIdCategoryNode($idCategoryNode);

        return $idCategoryNode;
    }

    /**
     * @param int $idCategoryNode
     *
     * @return void
     */
    public function delete($idCategoryNode)
    {
        $nodeEntity = $this->queryContainer
            ->queryNodeById($idCategoryNode)
            ->findOne();

        if ($nodeEntity) {
            $nodeEntity->delete();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\NodeTransfer $categoryNodeTransfer
     *
     * @return void
     */
    public function update(NodeTransfer $categoryNodeTransfer)
    {
        $nodeEntity = $this->queryContainer
            ->queryNodeById($categoryNodeTransfer->getIdCategoryNode())
            ->findOne();

        if ($nodeEntity) {
            $nodeEntity->fromArray($categoryNodeTransfer->toArray());
            $nodeEntity->save();
        }
    }

}
