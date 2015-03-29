<?php
namespace Famelo\Saas\Plan;

use Famelo\Saas\Domain\Model\Transaction;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;

/**
 */
class AbstractImplementation implements PlanImplementationInterface {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @var \Famelo\Saas\Domain\Model\Plan
	 */
	protected $plan;

	/**
	 * @var array
	 */
	protected $configuration;

	public function __construct($plan) {
		$this->plan = $plan;
		$this->configuration = $plan->getConfiguration();
	}

	public function setup() {

	}

	public function renew() {

	}

	public function createTransaction($amount, $paymentGateway, $currency = NULL, $invoiceId = NULL, $note = NULL) {
		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setCurrency($currency);
		$transaction->setPaymentGateway($paymentGateway);
		$transaction->setNote($note);
		$transaction->setInvoiceNumber($invoiceId);
		return $transaction;
	}

	public function addTransaction($transaction) {
		if ($transaction->getAmount() < 0) {
			if ($this->hasFunds($transaction) === FALSE) {
				throw new \Famelo\Saas\Exception\InsufficientFundsException();
			}
		}
		// $this->convertCurrency($transaction);
		$this->plan->addTransaction($transaction);
		$this->persistenceManager->add($transaction);
		$this->persistenceManager->update($this->plan);
		$this->persistenceManager->persistAll();
	}

	public function hasFunds($transaction) {
		return ($this->plan->getBalance() + $transaction->getAmount()) >= 0;
	}

	public function hasCredit($amount) {
		return $this->plan->getCredit() >= $amount;
	}

	public function withdrawCredit($amount) {
		$this->plan->removeCredit($amount);
		$this->persistenceManager->update($this->plan);
		$this->persistenceManager->persistAll();
	}
}
?>