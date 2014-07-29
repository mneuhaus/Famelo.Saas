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
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Mvc\RequestMatcher;
use TYPO3\Flow\Security\Account;

/**
 *
 */
class HandlerMatcher {

	/**
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 * @Flow\Inject
	 */
	protected $reflectionService;

	/**
	 * @var array
	 */
	protected $handlers;

	/**
	 * @var array
	 */
	protected $handlerInstances = array();

	/**
	 * @var ActionRequest
	 */
	protected $request;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @param ActionRequest $request
	 */
	public function __construct(ActionRequest $request = NULL, $response = NULL) {
		$this->request = $request;
		$this->response = $response;
	}

	public function __call($method, $arguments) {
		if ($this->handlers === NULL) {
			$this->handlers = array();
			$classNames = $this->reflectionService->getAllImplementationClassNamesForInterface('Famelo\Saas\Handlers\HandlerInterface');
			foreach ($classNames as $className) {
				$shortName = $className::NAME;
				$this->handlers[$shortName] = $className;
			}
		}

		if (substr($method, 0, 3) !== 'get') {
			throw new Exception('The method you\'re trying to call does not exist: ' . $method);
		}

		$handler = lcfirst(substr($method, 3));
		if (!isset($this->handlers[$handler])) {
			throw new Exception('The Rule you\'re trying to call does not exist: ' . $handler);
		}

		if (!isset($this->handlerInstances[$handler])) {
			$this->handlerInstances[$handler] = new $this->handlers[$handler]($this->request, $this->response);
		}

		return $this->handlerInstances[$handler];
	}

	public function postProcess() {
		foreach ($this->handlerInstances as $handlerInstance) {
			if (method_exists($handlerInstance, 'postProcess')) {
				$handlerInstance->postProcess();
			}
		}
	}
}
?>