<?php
namespace Famelo\Saas\Plan;

use Famelo\Saas\Domain\Model\Transaction;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Error\Message;

/**
 */
class AbstractImplementation implements PlanImplementationInterface {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	public function createTransaction($amount, $paymentGateway, $currency = NULL, $note = NULL) {
		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setCurrency($currency);
		$transaction->setPaymentGateway($paymentGateway);
		return $transaction;
	}

	public function addTransaction($transaction) {
		if ($this->hasFunds($transaction) === FALSE) {
			throw new \Famelo\Saas\Exception\InsufficientFundsException();
		}
		$this->convertCurrency($transaction);
		$this->transactionService->getPlan()->addTransaction($transaction);
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
		return ($this->transactionService->getPlan()->getBalance() + $transaction->getAmount()) > 0;
	}

	public function convertCurrency($transaction) {
		$subscriptionCurrency = $this->transactionService->getPlan()->getCurrency();
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