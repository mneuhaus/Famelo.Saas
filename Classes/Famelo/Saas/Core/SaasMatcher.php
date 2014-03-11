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
class SaasMatcher extends PolicyMatcher {
	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var string
	 */
	protected $className;

	/**
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $actionRequest
	 * @param \TYPO3\Flow\Mvc\RequestMatcher $parentMatcher
	 * @param \TYPO3\Flow\Aop\JoinPoint $joinPoint
	 * @param string $method
	 * @param string $className
	 */
	public function __construct(\TYPO3\Flow\Mvc\ActionRequest $actionRequest = NULL, $parentMatcher = NULL, $joinPoint = NULL, $method = NULL, $className = NULL) {
		parent::__construct($actionRequest, $parentMatcher, $joinPoint);
		$this->method = $method;
		$this->className = $className;
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

	/**
	 * @param string $className
	 * @return boolean
	 */
	public function isClassName($className) {
		if ($this->className === $className) {
			$this->addWeight(100000);
			return TRUE;
		}

		return FALSE;
	}

}
?>