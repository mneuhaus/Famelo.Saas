<?php
namespace Famelo\Saas\Services;

use Famelo\Saas\Domain\Model\Subscription;
use Famelo\Saas\Domain\Model\Transaction;
use Omnipay\Omnipay;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;

/**
 * @Flow\Scope("singleton")
 */
class TransactionService {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\Flow\Persistence\Doctrine\PersistenceManager
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @var \Famelo\Saas\Domain\Repository\TransactionRepository
	 * @Flow\Inject
	 */
	protected $transactionRepository;

	/**
	 * @var \Famelo\Saas\Domain\Model\User
	 */
	protected $user;

	/**
	 * @var \Famelo\Saas\Domain\Model\Team
	 */
	protected $team;

	/**
	 * @var \Famelo\Saas\Domain\Model\Subscription
	 */
	protected $subscription;

	/**
	 * @var \Famelo\Saas\Plan\PlanImplementationInterface
	 */
	protected $implementation;

	/**
	 * @var boolean
	 */
	protected $initialized = FALSE;

	public function __construct(\TYPO3\Flow\Security\Context $securityContext) {
		$this->user = $securityContext->getPartyByType('\Famelo\Saas\Domain\Model\User');
	}

	public function initialize() {
		if ($this->initialized === TRUE) {
			return;
		}
		$this->initialized = TRUE;

		if ($this->user instanceof \Famelo\Saas\Domain\Model\User) {
			$this->team = $this->user->getTeam();
		}

		if ($this->team instanceof \Famelo\Saas\Domain\Model\Team) {
			$this->subscription = $this->team->getSubscription();
		}

		if ($this->subscription instanceof \Famelo\Saas\Domain\Model\Subscription) {
			$plan = $this->getPlan();
			$implementationClassName = $plan['implementation'];
			$this->implementation = new $implementationClassName($this);
		}
	}

	public function getTeam() {
		$this->initialize();
		return $this->team;
	}

	public function getUser() {
		$this->initialize();
		return $this->user;
	}

	public function getBalance() {
		$this->initialize();
		if ($this->subscription === NULL) {
			return 0;
		}
		return $this->subscription->getBalance();
	}

	public function getTransactions() {
		return $this->transactionRepository->getUserTransactions();
	}

	public function getSubscription() {
		$this->initialize();
		if ($this->team === NULL || $this->team->getSubscription() === NULL) {
			return NULL;
		}
		return $this->team->getSubscription();
	}

	public function getPlans() {
		$this->initialize();
		return $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Saas.plans');
	}

	public function getPlan() {
		$this->initialize();
		$plan = $this->subscription->getPlan();
		$plans = $this->getPlans();
		return $plans[$plan];
	}

	public function getPaymentGateways() {
		$this->initialize();
		return $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Saas.paymentGateways');
	}

	public function getPaymentGateway($paymentGateway) {
		$this->initialize();
		$paymentGateways = $this->getPaymentGateways();
		return $paymentGateways[$paymentGateway];
	}

	public function createPaymentGatewayInstance($paymentGateway) {
		$paymentGatewayConfiguration = $this->getPaymentGateway($paymentGateway);
		$gateway = Omnipay::create($paymentGatewayConfiguration['gateway']);

		if (isset($paymentGatewayConfiguration['parameters'])) {
			$gateway->initialize($paymentGatewayConfiguration['parameters']);
		}
		return $gateway;
	}

	public function addTransaction($transaction) {
		$this->initialize();
		$transaction = $this->implementation->addTransaction($transaction);
		$this->persistenceManager->update($this->subscription);
		$this->persistenceManager->persistAll();
	}

	public function createTransaction($amount, $paymentGateway, $currency) {
		$this->initialize();
		$transaction = $this->implementation->createTransaction($amount, $paymentGateway, $currency);
		$this->persistenceManager->add($transaction);
		$this->persistenceManager->persistAll();
		return $transaction;
	}

	/**
	 * @param float amount
	 */
	public function withdraw($amount) {
		$this->initialize();
		$this->implementation->withdraw($amount);
		$this->persistenceManager->update($this->subscription);
		$this->persistenceManager->persistAll();
	}

	/**
	 * @param \Famelo\Saas\Domain\Model\Transaction $transaction
	 */
	public function hasFunds($transaction) {
		$this->initialize();
		return $this->implementation->hasFunds($transaction);
	}

	/**
	 * @param string plan
	 */
	public function subscribe($plan) {
		$this->initialize();
		$plans = $this->getPlans();

		if (isset($plans[$plan]) === FALSE) {
			throw new \TYPO3\Flow\Exception('Unknown Plan: ' . $plan);
		}

		$subscription = $this->team->getSubscription();
		if ($subscription === NULL) {
			$subscription = new Subscription();
		}
		$subscription->setPlan($plan);
		$this->team->setSubscription($subscription);

		$this->persistenceManager->update($this->team);
		$this->persistenceManager->persistAll();
	}

	/**
	 * @param string plan
	 */
	public function unsubscribe($plan) {
		$this->initialize();
	}

	public function sendInvoice($transaction) {
		$query = $this->transactionRepository->createQuery();
		$query->setOrderings(array('created' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING));
		$query->matching($query->logicalAnd(
			$query->logicalNot($query->equals('subscription', NULL)),
			$query->logicalNot($query->equals('paymentGateway', NULL)),
			$query->logicalNot($query->equals('invoiceNumber', NULL))
		));
		$query->setLimit(1);
		$lastTransaction = $query->execute()->current();

		if ($lastTransaction === FALSE) {
			$invoiceNumber = 10000;
		} else {
			$invoiceNumber = intval($lastTransaction->getInvoiceNumber());
			$invoiceNumber++;
		}
        $transaction->setInvoiceNumber($invoiceNumber);

		$invoicePath = $transaction->getInvoicePath();
		\TYPO3\Flow\Utility\Files::createDirectoryRecursively(dirname($invoicePath));

        $document = new \Famelo\PDF\Document('Famelo.Saas:Invoice');
        $document->assign('transaction', $transaction);
        $document->save($invoicePath);

        $mail = new \Famelo\Messaging\Message();
        $mail->setMessage('Famelo.Saas:Invoice')
            	->assign('transaction', $transaction)
            	->send();

        $this->persistenceManager->update($transaction);
        $this->persistenceManager->persistAll();
	}
}
?>
