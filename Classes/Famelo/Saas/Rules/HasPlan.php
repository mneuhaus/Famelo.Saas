<?php
namespace Famelo\Saas\Rules;

use CleverAge\Ruler\RuleAbstract;
use Famelo\Saas\Domain\Model\Plan;
use Famelo\Saas\Domain\Model\SaasPartyInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Context;

class HasPlan extends RuleAbstract {
    protected $_failure_exception_class = '\Famelo\Saas\Rules\Exception\NoPlanException';

    protected $_failure_message = 'no plan';

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    public function __construct() {
    }

    /**
     * {@inheritdoc}
     */
    public function doIsSatisfied() {
        $party = $this->securityContext->getParty();

        if (!$party instanceof SaasPartyInterface) {
            return FALSE;
        }

        return $party->getPlan() instanceof Plan;
    }
}
