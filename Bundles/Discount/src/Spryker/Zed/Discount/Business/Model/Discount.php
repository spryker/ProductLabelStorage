<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Discount\Business\Model;

use Generated\Shared\Transfer\DiscountCollectorTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Discount\Business\Distributor\DistributorInterface;
use Spryker\Zed\Discount\Communication\Plugin\DecisionRule\AbstractDecisionRule;
use Spryker\Zed\Discount\Dependency\Plugin\DiscountCalculatorPluginInterface;
use Spryker\Zed\Discount\Dependency\Plugin\DiscountCollectorPluginInterface;
use Spryker\Zed\Discount\Dependency\Plugin\DiscountDecisionRulePluginInterface;
use Spryker\Zed\Discount\DiscountConfig;
use Spryker\Zed\Discount\Persistence\DiscountQueryContainer;
use Spryker\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Orm\Zed\Discount\Persistence\SpyDiscountDecisionRule;
use Spryker\Zed\Messenger\Business\MessengerFacade;
use Generated\Shared\Transfer\MessageTransfer;

class Discount
{

    const KEY_DISCOUNTS = 'discounts';
    const KEY_ERRORS = 'errors';

    /**
     * @var DiscountQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var DecisionRuleEngine
     */
    protected $decisionRule;

    /**
     * @var QuoteTransfer
     */
    protected $quoteTransfer;

    /**
     * @var DiscountCollectorPluginInterface[]
     */
    protected $collectorPlugins;

    /**
     * @var DiscountCalculatorPluginInterface[]
     */
    protected $calculatorPlugins;

    /**
     * @var DiscountConfig
     */
    protected $discountSettings;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @var DistributorInterface
     */
    protected $distributor;

    /**
     * @var MessengerFacade
     */
    protected $messengerFacade;

    /**
     * @var DiscountDecisionRulePluginInterface[]
     */
    protected $decisionRulePlugins;

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param DiscountQueryContainer $queryContainer
     * @param DecisionRuleInterface $decisionRule
     * @param CalculatorInterface $calculator
     * @param DistributorInterface $distributor
     * @param MessengerFacade $messengerFacade
     * @param DiscountDecisionRulePluginInterface[] $decisionRulePlugins
     */
    public function __construct(
        QuoteTransfer $quoteTransfer,
        DiscountQueryContainer $queryContainer,
        DecisionRuleInterface $decisionRule,
        CalculatorInterface $calculator,
        DistributorInterface $distributor,
        MessengerFacade $messengerFacade,
        array $decisionRulePlugins
    ) {
        $this->queryContainer = $queryContainer;
        $this->decisionRule = $decisionRule;
        $this->quoteTransfer = $quoteTransfer;
        $this->calculator = $calculator;
        $this->distributor = $distributor;
        $this->decisionRulePlugins = $decisionRulePlugins;
        $this->messengerFacade = $messengerFacade;
    }

    /**
     * @return array
     */
    public function calculate()
    {
        $result = $this->retrieveDiscountsToBeCalculated();
        $discountsToBeCalculated = $result[self::KEY_DISCOUNTS];
        $this->setValidationMessages($result[self::KEY_ERRORS]);

        $calculatedDiscounts = $this->calculator->calculate(
            $discountsToBeCalculated,
            $this->quoteTransfer,
            $this->distributor
        );

        $this->addDiscountsToQuote($this->quoteTransfer, $calculatedDiscounts);

        return $result;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param DiscountTransfer[] $discounts
     */
    protected function addDiscountsToQuote(QuoteTransfer $quoteTransfer, array $discounts)
    {
        $quoteTransfer->setVoucherDiscounts(new \ArrayObject());
        $quoteTransfer->setCartRuleDiscounts(new \ArrayObject());

        foreach ($discounts as $discount) {
            $discountTransferCopy = $discount[Calculator::KEY_DISCOUNT_TRANSFER];
            if (!empty($discountTransferCopy->getVoucherCode())) {
                $quoteTransfer->addVoucherDiscount($discountTransferCopy);
            } else {
                $quoteTransfer->addCartRuleDiscount($discountTransferCopy);
            }
        }
    }

    /**
     * @param array|string[] $couponCodes
     *
     * @return SpyDiscount[]
     */
    public function retrieveActiveCartAndVoucherDiscounts(array $couponCodes = [])
    {
        return $this->queryContainer->queryCartRulesIncludingSpecifiedVouchers($couponCodes)->find();
    }

    /**
     * @return array
     */
    protected function retrieveDiscountsToBeCalculated()
    {
        $discounts = $this->retrieveActiveCartAndVoucherDiscounts($this->getVoucherCodes());

        $discountsToBeCalculated = [];
        $decisionRuleValidationErrors = [];

        foreach ($discounts as $discountEntity) {
            $discountTransfer = $this->hydrateDiscountTransfer($discountEntity);
            $decisionRulePlugins = $this->getDecisionRulePlugins($discountEntity->getPrimaryKey());

            $result = $this->decisionRule->evaluate($discountTransfer, $this->quoteTransfer, $decisionRulePlugins);

            if ($result->isSuccess()) {
                $discountsToBeCalculated[] = $discountTransfer;
            } else {
                $decisionRuleValidationErrors = array_merge($decisionRuleValidationErrors, $result->getErrors());
            }
        }

        return [
            self::KEY_DISCOUNTS => $discountsToBeCalculated,
            self::KEY_ERRORS => $decisionRuleValidationErrors,
        ];
    }

    /**
     * @param int $idDiscount
     *
     * @return DiscountDecisionRulePluginInterface[]
     */
    protected function getDecisionRulePlugins($idDiscount)
    {
        $plugins = [];
        $decisionRules = $this->retrieveDecisionRules($idDiscount);
        foreach ($decisionRules as $decisionRuleEntity) {
            $decisionRulePlugin = $this->decisionRulePlugins[$decisionRuleEntity->getDecisionRulePlugin()];

            $decisionRulePlugin->setContext(
                [
                    AbstractDecisionRule::KEY_ENTITY => $decisionRuleEntity,
                ]
            );

            $plugins[] = $decisionRulePlugin;
        }

        return $plugins;
    }

    /**
     * @param int $idDiscount
     *
     * @return SpyDiscountDecisionRule[]
     */
    protected function retrieveDecisionRules($idDiscount)
    {
        $decisionRules = $this->queryContainer->queryDecisionRules($idDiscount)->find();

        return $decisionRules;
    }

    /**
     * @return array|string[]
     */
    protected function getVoucherCodes()
    {
        $voucherDiscounts = $this->quoteTransfer->getVoucherDiscounts();

        if (count($voucherDiscounts) === 0) {
            return [];
        }

        $voucherCodes = [];
        foreach ($voucherDiscounts as $voucherDiscount) {
            $voucherCodes[] = $voucherDiscount->getVoucherCode();
        }

        return $voucherCodes;
    }

    /**
     * @param SpyDiscount $discountEntity
     *
     * @return DiscountTransfer
     */
    protected function hydrateDiscountTransfer(SpyDiscount $discountEntity)
    {
        $discountTransfer = new DiscountTransfer();
        $discountTransfer->fromArray($discountEntity->toArray(), true);

        if ($discountEntity->getUsedVoucherCode() !== null) {
            $discountTransfer->setVoucherCode($discountEntity->getUsedVoucherCode());
        }

        foreach ($discountEntity->getDiscountCollectors() as $discountCollectorEntity) {
            $discountCollectorTransfer = new DiscountCollectorTransfer();
            $discountCollectorTransfer->fromArray($discountCollectorEntity->toArray(), false);
            $discountTransfer->addDiscountCollectors($discountCollectorTransfer);
        }

        return $discountTransfer;
    }

    /**
     * @param array|string[] $errors
     *
     * @return void
     */
    protected function setValidationMessages(array $errors = [])
    {
        foreach ($errors as $errorMessage) {
            $messageTransfer = new MessageTransfer();
            $messageTransfer->setValue($errorMessage);

            $this->messengerFacade->addErrorMessage($messageTransfer);
        }
    }

}
