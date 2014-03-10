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
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $actionRequest
	 * @param \TYPO3\Flow\Mvc\RequestMatcher $parentMatcher
	 * @param \TYPO3\Flow\Aop\JoinPoint $joinPoint
	 */
	public function __construct(\TYPO3\Flow\Mvc\ActionRequest $actionRequest = NULL, $parentMatcher = NULL, $joinPoint = NULL) {
		parent::__construct($actionRequest, $parentMatcher, $joinPoint);
	}

}
?>