<?php
namespace Famelo\Saas\Core;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Expose\Security\PolicyMatcher;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\RequestMatcher;
use TYPO3\Flow\Security\Account;

/**
 *
 */
class RuleMatcher extends RequestMatcher {
	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @var array
	 */
	protected $ruleMatchers;

	/**
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $actionRequest
	 * @param \TYPO3\Flow\Mvc\RequestMatcher $parentMatcher
	 * @param \TYPO3\Flow\Aop\JoinPoint $joinPoint
	 */
	public function __construct(\TYPO3\Flow\Mvc\ActionRequest $actionRequest = NULL, $parentMatcher = NULL, $joinPoint = NULL) {
		parent::__construct($actionRequest, $parentMatcher, $joinPoint);
	}

	/**
	 * @param string $method
	 * @return boolean
	 */
	public function isMethod($method) {
		if ($this->method === $method) {
			$this->addWeight(100000);
			return TRUE;
		}

		return FALSE;
	}

	public function __call($method, $arguments) {
		if ($this->ruleMatchers === NULL) {
			$this->ruleMatchers = array();
			$classNames = $this->reflectionService->getAllImplementationClassNamesForInterface('Famelo\Saas\Rules\RulesInterface');
			foreach ($classNames as $className) {
				$shortName = $className::NAME;
				$this->ruleMatchers[$shortName] = $className;
			}
		}

		if (substr($method, 0, 3) !== 'get') {
			throw new Exception('The method you\'re trying to call does not exist: ' . $method);
		}

		$ruleMatcher = lcfirst(substr($method, 3));
		if (!isset($this->ruleMatchers[$ruleMatcher])) {
			throw new Exception('The Rule you\'re trying to call does not exist: ' . $ruleMatcher);
		}

		return new $this->ruleMatchers[$ruleMatcher]();
	}

	public function getRequest() {
		return $this->request;
	}
}
?>