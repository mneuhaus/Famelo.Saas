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

use TYPO3\Expose\Security\PolicyMatcher;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Http\Request;
use TYPO3\Flow\Mvc\RequestMatcher;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\Context;

/**
 *
 * @Flow\Scope("singleton")
 */
class RequestHandler implements HandlerInterface {
    /**
      * contains short name for this matcher used for reference in the eel expression
      */
    const NAME = 'request';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Context
     * @Flow\Inject
     */
    protected $securityContext;

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * @return void
	 */
	public function saveIntercepted() {
		$this->securityContext->setInterceptedRequest($this->request->getMainRequest());
	}
}
?>