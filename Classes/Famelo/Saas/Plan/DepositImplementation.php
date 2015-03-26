<?php
namespace Famelo\Saas\Plan;

use Famelo\Saas\Domain\Model\Transaction;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Error\Message;

/**
 */
class DepositImplementation extends AbstractImplementation {
	public function setup() {
        $this->plan->removeBalance($this->configuration['cost']);
        $this->plan->addCredit($this->configuration['credit']);
    }
}
?>