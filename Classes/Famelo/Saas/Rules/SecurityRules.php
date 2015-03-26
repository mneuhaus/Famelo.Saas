<?php
namespace Famelo\Saas\Rules;

use Famelo\Saas\Domain\Model\Plan;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Context;
use TYPO3\Party\Domain\Model\AbstractParty;

class SecurityRules implements RulesInterface {
    /**
      * contains short name for this matcher used for reference in the eel expression
      */
    const NAME = 'security';

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    public function isAuthenticated() {
        $party = $this->securityContext->getParty();
        $account = $this->securityContext->getAccount();
        var_dump($party, $account);
        exit();
        return $this->securityContext->getAccount() !== NULL;
    }

}
