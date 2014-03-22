<?php
namespace Famelo\Saas\Services;

use Famelo\Saas\Core\SaasMatcher;
use Famelo\Saas\Domain\Model\Transaction;
use TYPO3\Eel\Context;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("session")
 */
class SaasService {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Eel\CompilingEvaluator
	 */
	protected $eelEvaluator;

	/**
	 * The flash messages. Use $this->flashMessageContainer->addMessage(...) to add a new Flash
	 * Message.
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\FlashMessageContainer
	 */
	protected $flashMessageContainer;

	/**
	 * @var \Famelo\Saas\Domain\Service\TransactionService
	 * @Flow\Inject
	 */
	protected $transactionService;

	/**
	 * @var \Famelo\Saas\Domain\Service\TransactionService
	 * @Flow\Inject(setting="paymentUrl")
	 */
	protected $paymentUrl;

	/**
	 * @var \TYPO3\Flow\Mvc\RequestInterface
	 */
	protected $interceptedRequest;


	public function decideOnJoinPoint($joinPoint) {
		$checks = $this->configurationManager->getConfiguration('Saas');

		$this->request = ObjectAccess::getProperty($joinPoint->getProxy(), 'request', TRUE);
		$this->response = ObjectAccess::getProperty($joinPoint->getProxy(), 'response', TRUE);
		$saasMatcher = new SaasMatcher($this->request, NULL, $joinPoint, $joinPoint->getMethodName(), $joinPoint->getClassName());
		$context = new Context($saasMatcher);

		foreach ($checks as $name => $check) {
			if ($this->eelEvaluator->evaluate($check['check'], $context) === TRUE) {
				$transaction = new Transaction();
				$transaction->setAmount(-$check['cost']);
				$transaction->setCurrency(isset($check['currency']) ? $check['currency'] : 'POINT');
				if ($this->transactionService->hasFunds($transaction) === FALSE) {
					$this->flashMessageContainer->addMessage(new Message('Insufficient funds'));
					$this->setInterceptedRequest($this->request);
					$this->redirectToUri($this->paymentUrl);
				}
			}

			if ($this->eelEvaluator->evaluate($check['bill'], $context) === TRUE) {
				if ($this->transactionService->hasFunds($transaction) === FALSE) {
					$this->flashMessageContainer->addMessage(new Message('Insufficient funds'));
					$this->setInterceptedRequest($this->request);
					$this->redirectToUri($this->paymentUrl);
				}
				if (isset($check['note'])) {
					$transaction->setNote($check['note']);
				}
				$this->transactionService->addTransaction($transaction);
			}
		}
	}

	/**
	 * Redirects to another URI
	 *
	 * @param mixed $uri Either a string representation of a URI or a \TYPO3\Flow\Http\Uri object
	 * @param integer $delay (optional) The delay in seconds. Default is no delay.
	 * @param integer $statusCode (optional) The HTTP status code for the redirect. Default is "303 See Other"
	 * @throws \TYPO3\Flow\Mvc\Exception\UnsupportedRequestTypeException If the request is not a web request
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 * @api
	 */
	protected function redirectToUri($uri, $delay = 0, $statusCode = 303) {
		$escapedUri = htmlentities($uri, ENT_QUOTES, 'utf-8');
		$this->response->setContent('<html><head><meta http-equiv="refresh" content="' . intval($delay) . ';url=' . $escapedUri . '"/></head></html>');
		$this->response->setStatus($statusCode);
		if ($delay === 0) {
			$this->response->setHeader('Location', (string)$uri);
		}
		throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
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
