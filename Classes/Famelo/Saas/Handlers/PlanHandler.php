<?php
namespace Famelo\Saas\Handlers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Famelo\Saas\Domain\Model\CreditUse;
use TYPO3\Expose\Security\PolicyMatcher;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\RequestMatcher;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\Context;
use TYPO3\Party\Domain\Model\AbstractParty;

/**
 *
 * @Flow\Scope("singleton")
 */
class PlanHandler implements HandlerInterface {
    /**
      * contains short name for this matcher used for reference in the eel expression
      */
    const NAME = 'plan';

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

  	/**
  	 * @param integer $amount
  	 * @return void
  	 */
  	public function withdrawCredit($amount, $reference) {
		$party = $this->securityContext->getParty();

        if (!$party instanceof AbstractParty) {
            return;
        }

        $plan = $party->getPlan();

        foreach ($plan->getCreditUses() as $creditUse) {
            if ($creditUse->getReference() == $reference) {
                return;
            }
        }

        $creditUse = new CreditUse();
        $creditUse->setReference($reference);
        $plan->addCreditUse($creditUse);

        $party->getPlan()->getImplementation()->withdrawCredit($amount);
  	}
}
?>