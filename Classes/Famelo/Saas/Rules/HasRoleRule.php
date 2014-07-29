<?php
namespace Famelo\Saas\Rules;

use CleverAge\Ruler\RuleAbstract;
use TYPO3\Flow\Security\Context;
use TYPO3\Flow\Annotations as Flow;

class HasRoleRule extends RuleAbstract {
    /**
     * @var string
     */
    protected $roleIdentifier;

    protected $_failure_exception_class = '\Famelo\Saas\Rules\Exception\MissingRoleException';

    protected $_failure_message = 'missing role';

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    public function __construct($roleIdentifier) {
        $this->roleIdentifier = $roleIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function doIsSatisfied() {
        return $this->securityContext->hasRole($this->roleIdentifier);
    }
}
