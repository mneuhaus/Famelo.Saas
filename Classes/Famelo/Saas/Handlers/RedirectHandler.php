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
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Mvc\RequestMatcher;
use TYPO3\Flow\Security\Account;

/**
 *
 */
class RedirectHandler extends \TYPO3\Flow\Mvc\Routing\UriBuilder implements HandlerInterface {
    /**
      * contains short name for this matcher used for reference in the eel expression
      */
    const NAME = 'redirect';

	/**
	 * @var string
	 */
	protected $actionName = 'index';

	/**
	 * @var array
	 */
	protected $controllerArguments = array();

	/**
	 * @var string
	 */
	protected $controllerName;

	/**
	 * @var string
	 */
	protected $packageKey;

	/**
	 * @var string
	 */
	protected $subPackageKey;

	/**
	 * @var string
	 */
	protected $statusCode = 303;

	/**
	 * @var Response
	 */
	protected $response;

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
	}

	public function getUri() {
		return $this;
	}

	public function action($actionName) {
		$this->actionName = $actionName;
		return $this;
	}

	public function arguments($controllerArguments) {
		$this->controllerArguments = $controllerArguments;
		return $this;
	}

	public function controller($controllerName) {
		$this->controllerName = $controllerName;
		return $this;
	}

	public function package($packageKey) {
		$this->packageKey = $packageKey;
		return $this;
	}

	public function subPackage($subPackageKey) {
		$this->subPackageKey = $subPackageKey;
		return $this;
	}

	public function postProcess() {
		$uri = $this->uriFor($this->actionName, $this->controllerArguments, $this->controllerName, $this->packageKey, $this->subPackageKey);

		$escapedUri = htmlentities($uri, ENT_QUOTES, 'utf-8');
		$this->response->setContent('<html><head><meta http-equiv="refresh" content="0;url=' . $escapedUri . '"/></head></html>');
		$this->response->setStatus($this->statusCode);
		$this->response->setHeader('Location', (string)$uri);

		throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
	}
}
?>