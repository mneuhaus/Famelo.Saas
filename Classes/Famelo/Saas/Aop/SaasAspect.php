<?php
namespace Famelo\Saas\Aop;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Aspect
 */
class SaasAspect {
	/**
	 * @var \Famelo\Saas\Services\SaasService
	 * @Flow\Inject
	 */
	protected $saasService;

	/**
	 * @Flow\Before("method(.*\Controller\.*->.*Action(*))")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current join point
	 * @return void
	 */
	public function checkFunds(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$this->saasService->decideOnJoinPoint($joinPoint);
	}
}
