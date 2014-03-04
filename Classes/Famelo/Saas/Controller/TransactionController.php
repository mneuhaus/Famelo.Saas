<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Model\Transaction;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Utility\Algorithms;

/**
 * @Flow\Scope("singleton")
 */
class TransactionController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \Famelo\Saas\Domain\Service\TransactionService
	 * @Flow\Inject
	 */
	protected $transactionService;

	/**
	 * @return string
	 */
	public function indexAction() {
		$id = '41f1793d-c78b-93e8-3aa9-9468ee0d1c26';
		$foo = $this->persistenceManager->getObjectByIdentifier($id, 'Famelo\Saas\Domain\Model\Transaction');
		d($foo);
		d($foo->getSubscription());
		exit();
		$this->view->assign('transactionService', $this->transactionService);
	}

	/**
	 * @param float $amount
	 * @param string $currency
	 */
	public function removeAction($amount, $currency = 'EUR') {
		if ($this->transactionService->hasFunds($amount) === FALSE) {
			$this->flashMessageContainer->addMessage(new Message('Insufficient funds'));
			$this->redirect('choosePayment');
		}
		$transaction = new Transaction();
		$transaction->setAmount(-$amount);
		$transaction->setCurrency($currency);
		$transaction->setNote('Simple Withdrawel');
		$this->transactionService->addTransaction($transaction);
		$this->redirect('index');
	}

	/**
	 * @param string plan
	 */
	public function subscribeAction($plan) {
		$this->transactionService->subscribe($plan);
		$this->redirect('index');
	}

	/**
	 * @param string plan
	 */
	public function unsubscribeAction($plan) {
		$this->transactionService->unsubscribe($plan);
		$this->redirect('index');
	}

	/**
	 * @return string
	 */
	public function choosePaymentAction() {
		$this->view->assign('paymentGateways', $this->transactionService->getPaymentGateways());
	}

	/**
	 * @param string $paymentGateway
	 * @param float $amount
	 * @param array $card
	 * @return string
	 */
	public function makePaymentAction($paymentGateway, $amount, $card = array()) {
		$currency = 'EUR';
		$transaction = $this->transactionService->createTransaction($amount, $paymentGateway, $currency);
		$paymentGateway = $this->transactionService->createPaymentGatewayInstance($paymentGateway);

		$card = new CreditCard($card);
		$payment = array(
			'amount' => $amount,
			'card' => $card,
			'returnUrl' => $this->getReturnUrl($transaction),
			'cancelUrl' => $this->getCancelUrl(),
			'currency' => $currency
		);
		$response = $paymentGateway->purchase($payment)->send();

		if ($response->isSuccessful()) {
			$this->flashMessageContainer->addMessage(new Message('Payment successful'));
			$transaction->setNote($response->getTransactionReference());
			$this->transactionService->addTransaction($transaction);
            $this->transactionService->sendInvoice($transaction);
		} elseif ($response->isRedirect()) {
			$response->redirect();
		} else {
			$this->flashMessageContainer->addMessage(new Message('Payment failed: ' . $response->getMessage()));
		}

		$this->redirect('index');
	}

	/**
	 * @param \Famelo\Saas\Domain\Model\Transaction $transaction
	 * @return string
	 */
	public function completeAuthorizeAction($transaction) {
		if ($transaction->getSubscription() !== NULL) {
			// Someone seems to be trying to hijack an transaction
			$this->redirect('index');
		}

		try {
			$paymentGateway = $this->transactionService->createPaymentGatewayInstance($transaction->getPaymentGateway());
			$payment = array(
				'amount' => $transaction->getAmount(),
				'returnUrl' => $this->getReturnUrl($transaction),
				'cancelUrl' => $this->getCancelUrl(),
				'currency' => $transaction->getCurrency()
			);
			$response = $paymentGateway->completePurchase($payment)->send();

			if ($response->isSuccessful()) {
				$this->flashMessageContainer->addMessage(new Message('Payment successful'));
				$transaction->setNote($response->getTransactionReference());
				$this->transactionService->addTransaction($transaction);
				$this->transactionService->sendInvoice($transaction);
			} else {
				$this->flashMessageContainer->addMessage(new Message('Payment failed: ' . $response->getMessage()));
			}
		} catch (\Exception $e) {
			$this->flashMessageContainer->addMessage(new Message('Sorry, there was an error processing your payment. Please try again later.'));
		}

		$this->redirect('index');
	}

	public function getReturnUrl($transaction) {
		$this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE);
		return $this->uriBuilder->uriFor('completeAuthorize', array('transaction' => $transaction->getIdentifier()));
	}

	public function getCancelUrl() {
		$this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE);
		return $this->uriBuilder->uriFor('index');
	}
}

?>
