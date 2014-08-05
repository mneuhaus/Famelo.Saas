<?php
namespace Famelo\Saas\Controller;
use Famelo\Saas\Domain\Model\Plan;
use Famelo\Saas\Domain\Model\Transaction;
use Famelo\Saas\Services\RedirectService;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
/**
 * @Flow\Scope("singleton")
 */
class PaymentController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * @Flow\Inject(setting="PaymentGateways")
	 * @var array
	 */
	protected $paymentGateways;

	/**
	 * @Flow\Inject
	 * @var RedirectService
	 */
	protected $redirectService;

	/**
	 *
	 * @return string
	 */
	public function chooseAction() {
		$party = $this->securityContext->getParty();
		$this->view->assign('party', $party);
		$this->view->assign('paymentGateways', $this->paymentGateways);
		$this->view->assign('col-width', 12 / count($this->paymentGateways));
	}

	/**
	 * @param string $paymentGatewayName
	 * @param float $amount
	 * @param array $card
	 * @return string
	 */
	public function payAction($paymentGatewayName, $amount = NULL, $card = array()) {
		$currency = 'EUR';
		$party = $this->securityContext->getParty();
		if ($amount === NULL) {
			$amount = $party->getPlan()->getDueAmount();
		}
		$planImplementation = $party->getPlan()->getImplementation();

		$transaction = $planImplementation->createTransaction($amount, $paymentGatewayName, $currency);
		$paymentGateway = $this->createPaymentGatewayInstance($paymentGatewayName);
		$payment = $this->createPayment($amount, $transaction, $currency, $card);

		if (method_exists($paymentGateway, 'purchase')) {
			$response = $paymentGateway->purchase($payment)->send();
		} else {
			$response = $paymentGateway->capture($payment)->send();
		}

		if ($response->isSuccessful()) {
			$this->flashMessageContainer->addMessage(new Message('Payment successful'));
			$transaction->setNote($response->getTransactionReference());
			$planImplementation->addTransaction($transaction);
			$this->redirectToOriginalRequest();
		} elseif ($response->isRedirect()) {
			$response->redirect();
		} else {
			$this->flashMessageContainer->addMessage(new Message('Payment failed: ' . $response->getMessage()));
		}

		$this->redirect('index', 'Transaction');
	}

	/**
	 * @param \Famelo\Saas\Domain\Model\Transaction $transaction
	 * @return string
	 */
	public function completeAuthorizeAction($transaction) {
		if ($transaction->getPlan() !== NULL) {
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
				$transaction->setState(Transaction::STATE_PAID);
				$this->transactionService->addTransaction($transaction);
				$this->transactionService->sendInvoice($transaction);
				$this->redirectToOriginalRequest();
			} else {
				$this->flashMessageContainer->addMessage(new Message('Payment failed: ' . $response->getMessage()));
			}
		} catch (\Exception $e) {
			$this->flashMessageContainer->addMessage(new Message('Sorry, there was an error processing your payment. Please try again later.'));
		}

		$this->redirect('index');
	}

	public function createPaymentGatewayInstance($paymentGateway) {
		$paymentGateway = $this->paymentGateways[$paymentGateway];
		$gateway = Omnipay::create($paymentGateway['gateway']);

		if (isset($paymentGateway['parameters'])) {
			$gateway->initialize($paymentGateway['parameters']);
		}
		return $gateway;
	}

	public function createPayment($amount, $transaction, $currency, $card) {
		$card = new CreditCard($card);
		$payment = array(
			'amount' => $amount,
			'card' => $card,
			'returnUrl' => $this->getReturnUrl($transaction),
			'cancelUrl' => $this->getCancelUrl(),
			'currency' => $currency
		);
		return $payment;
	}

	public function getReturnUrl($transaction) {
		$this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE);
		return $this->uriBuilder->uriFor('completeAuthorize', array('transaction' => $transaction->getIdentifier()));
	}

	public function getCancelUrl() {
		$this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE);
		return $this->uriBuilder->uriFor('index');
	}

	public function redirectToOriginalRequest() {
		$originalRequest = $this->redirectService->getInterceptedRequest();
		if ($originalRequest !== NULL) {
			$this->redirectService->setInterceptedRequest(NULL);
			$this->redirectToRequest($originalRequest);
		}
	}
}
?>