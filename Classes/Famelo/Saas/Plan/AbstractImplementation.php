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

	public function __construct($plan) {
		$this->plan = $plan;
	}

	public function createTransaction($amount, $paymentGateway, $currency = NULL, $note = NULL) {
		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setCurrency($currency);
		$transaction->setPaymentGateway($paymentGateway);
		return $transaction;
	}

	public function addTransaction($transaction) {
		if ($transaction->getAmount() < 0) {
			if ($this->hasFunds($transaction) === FALSE) {
				throw new \Famelo\Saas\Exception\InsufficientFundsException();
			}
		}
		$this->convertCurrency($transaction);
		$this->plan->addTransaction($transaction);
		$this->persistenceManager->add($transaction);
		$this->persistenceManager->update($this->plan);
		$this->persistenceManager->persistAll();
	}

	public function withdraw($amount, $note = NULL, $currency = NULL) {
		$transaction = new Transaction();
		$transaction->setAmount(-$amount);
		$transaction->setCurrency($currency);
		$transaction->setNote($note);
		$this->addTransaction($transaction);
		return $transaction;
	}

	public function hasFunds($transaction) {
		$this->convertCurrency($transaction);
		return ($this->plan->getBalance() + $transaction->getAmount()) >= 0;
	}

	public function convertCurrency($transaction) {
		$subscriptionCurrency = $this->plan->getCurrency();
		if ($subscriptionCurrency === $transaction->getCurrency()) {
			return;
		}

		$exchangeRates = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Saas.exchangeRates');
		if ($transaction->getCurrency() == 'POINT') {
			$amount = $transaction->getAmount() * $exchangeRates[$subscriptionCurrency];
			$transaction->setAmount($amount);
			$transaction->setCurrency($subscriptionCurrency);
		}
	}
}
?>