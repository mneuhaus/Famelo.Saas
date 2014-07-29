<?php
namespace Famelo\Saas\Rules;

use Famelo\Saas\Domain\Model\Plan;
use Famelo\Saas\Domain\Model\SaasPartyInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Context;

class PlanRules implements RulesInterface {
    /**
      * contains short name for this matcher used for reference in the eel expression
      */
    const NAME = 'plan';

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    public function hasPlan() {
        $party = $this->securityContext->getParty();

        if (!$party instanceof SaasPartyInterface) {
            return FALSE;
        }

        return $party->getPlan() instanceof Plan;
    }

}
