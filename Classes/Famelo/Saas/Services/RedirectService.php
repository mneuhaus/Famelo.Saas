<?php
namespace Famelo\Saas\Services;

use Famelo\Saas\Core\SaasMatcher;
use Famelo\Saas\Domain\Model\Transaction;
use TYPO3\Eel\Context;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Mvc\Routing\UriBuilder;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("session")
 */
class RedirectService {

	/**
	 * @var \TYPO3\Flow\Mvc\RequestInterface
	 */
	protected $interceptedRequest;

	/**
	 * @var UriBuilder
	 */
	protected $uriBuilder;

	/**
	 * @var ActionRequest
	 */
	protected $currentRequest;

	public function injectCurrentRequest($request) {
		$this->currentRequest = $request;
		$this->uriBuilder = new UriBuilder();
		$this->uriBuilder->setRequest($this->currentRequest);
	}

	/**
	 * Redirects to another URI
	 *
	 * @param mixed $uri Either a string representation of a URI or a \TYPO3\Flow\Http\Uri object
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 * @api
	 */
	public function redirectToUri($uri) {
		header('Location: ' . (string) $uri);
		throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
	}

	/**
	 * Redirects the request to another action and / or controller.
	 *
	 * Redirect will be sent to the client which then performs another request to the new URI.
	 *
	 * NOTE: This method only supports web requests and will throw an exception
	 * if used with other request types.
	 *
	 * @param string $actionName Name of the action to forward to
	 * @param string $controllerName Unqualified object name of the controller to forward to. If not specified, the current controller is used.
	 * @param string $packageKey Key of the package containing the controller to forward to. If not specified, the current package is assumed.
	 * @param array $arguments Array of arguments for the target action
	 * @param integer $delay (optional) The delay in seconds. Default is no delay.
	 * @param integer $statusCode (optional) The HTTP status code for the redirect. Default is "303 See Other"
	 * @param string $format The format to use for the redirect URI
	 * @return void
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 * @see forward()
	 * @api
	 */
	protected function redirect($actionName, $controllerName = NULL, $packageKey = NULL, array $arguments = NULL, $delay = 0, $statusCode = 303, $format = NULL) {
		if ($packageKey !== NULL && strpos($packageKey, '\\') !== FALSE) {
			list($packageKey, $subpackageKey) = explode('\\', $packageKey, 2);
		} else {
			$subpackageKey = NULL;
		}
		$this->uriBuilder->reset();
		if ($format === NULL) {
			$this->uriBuilder->setFormat($this->request->getFormat());
		} else {
			$this->uriBuilder->setFormat($format);
		}

		$uri = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->uriFor($actionName, $arguments, $controllerName, $packageKey, $subpackageKey);
		$this->redirectToUri($uri, $delay, $statusCode);
	}

	/**
	 * Redirects the request to another action and / or controller.
	 *
	 * Redirect will be sent to the client which then performs another request to the new URI.
	 *
	 * NOTE: This method only supports web requests and will throw an exception
	 * if used with other request types.
	 *
	 * @param ActionRequest $request The request to redirect to
	 * @param integer $delay (optional) The delay in seconds. Default is no delay.
	 * @param integer $statusCode (optional) The HTTP status code for the redirect. Default is "303 See Other"
	 * @return void
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 * @see forwardToRequest()
	 * @api
	 */
	protected function redirectToRequest(ActionRequest $request, $delay = 0, $statusCode = 303) {
		$packageKey = $request->getControllerPackageKey();
		$subpackageKey = $request->getControllerSubpackageKey();
		if ($subpackageKey !== NULL) {
			$packageKey .= '\\' . $subpackageKey;
		}
		$this->redirect($request->getControllerActionName(), $request->getControllerName(), $packageKey, $request->getArguments(), $delay, $statusCode, $request->getFormat());
	}

	/**
	 * Sets an action request, to be stored for later resuming after it
	 * has been intercepted by a security exception.
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $interceptedRequest
	 * @return void
	 * @Flow\Session(autoStart=true)
	 */
	public function setInterceptedRequest(ActionRequest $interceptedRequest = NULL) {
		$this->interceptedRequest = $interceptedRequest;
	}

	/**
	 * Returns the request, that has been stored for later resuming after it
	 * has been intercepted by a security exception, NULL if there is none.
	 *
	 * @return \TYPO3\Flow\Mvc\ActionRequest
	 * @Flow\Session(autoStart=true)
	 */
	public function getInterceptedRequest() {
		return $this->interceptedRequest;
	}
}
?>
