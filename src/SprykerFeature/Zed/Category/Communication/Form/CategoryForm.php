<?php

namespace SprykerFeature\Zed\Category\Communication\Form;

use Generated\Zed\Ide\FactoryAutoCompletion\CategoryCommunication;
use SprykerEngine\Shared\Kernel\Factory\FactoryInterface;
use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use SprykerEngine\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use SprykerFeature\Zed\Ui\Dependency\Form\AbstractForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryForm extends AbstractForm
{

    const NAME = 'name';
    const IS_ACTIVE = 'is_active';
    const ID_CATEGORY = 'id_category';

    /**
     * @var FactoryInterface|CategoryCommunication
     */
    protected $factory;

    /**
     * @var int
     */
    protected $idLocale;

    /**
     * @param Request $request
     * @param FactoryInterface $factory
     * @param $idLocale
     * @param QueryContainerInterface $queryContainer
     */
    public function __construct(
        Request $request,
        FactoryInterface $factory,
        $idLocale,
        QueryContainerInterface $queryContainer = null
    ) {
        $this->factory = $factory;
        $this->idLocale = $idLocale;
        parent::__construct($request, $queryContainer);
    }

    /**
     * @return array
     */
    protected function getDefaultData()
    {
        if ($this->getIdCategory()) {
            return [
                self::ID_CATEGORY => $this->getIdCategory(),
                self::NAME => $this->stateContainer->getRequestValue(self::NAME),
                self::IS_ACTIVE => $this->stateContainer->getRequestValue(self::IS_ACTIVE),
            ];
        }

        return [
            self::IS_ACTIVE => true
        ];
    }

    public function addFormFields()
    {
        $this->addField(self::ID_CATEGORY);
        $this->addField(self::NAME)
            ->setConstraints(
                [
                    new Assert\Type([
                        'type' => 'string'
                    ]),
                    new Assert\NotBlank(),
                    $this->factory->createConstraintCategoryNameExists(
                        $this->queryContainer,
                        $this->getIdCategory(),
                        $this->getIdLocale()
                    ),
                ]
            );

        $this->addField(self::IS_ACTIVE)
            ->setConstraints(
                [
                    new Assert\Type(
                        ['type' => 'boolean']
                    )
                ]
            );
    }

    /**
     * @return int
     */
    protected function getIdCategory()
    {
        return $this->stateContainer->getRequestValue(self::ID_CATEGORY);
    }

    /**
     * @return int
     */
    protected function getIdLocale()
    {
        return $this->idLocale;
    }
}
