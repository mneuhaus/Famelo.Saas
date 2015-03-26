<?php
namespace Famelo\Saas\Plan;

use Famelo\Saas\Domain\Model\Transaction;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Error\Message;

/**
 */
class FlatrateImplementation extends AbstractImplementation {
    public function setup() {
        $now = new \DateTime();

        $previousCost = $this->plan->getCost();
        $currentCost = $this->configuration['cost'];

        $cycleNext = $this->plan->getCycleNext();
        $cycleStart = $this->plan->getCycleStart();
        $balance = $this->plan->getBalance();
        $credit = $this->plan->getCredit();

        if ($cycleNext === NULL) {
            $cycleNext = new \DateTime();
            $cycleStart = new \DateTime();
        }

        if ($cycleNext <= $now) {
            $cycleNext = $cycleNext->modify($this->plan->getCycle());
            $cycleCost = $currentCost;
            $balance -= $currentCost;
            $credit = $this->configuration['credit'];
        } else if ($previousCost !== $currentCost) {
            $balance -= ($currentCost - $previousCost);
            $cycleCost = $currentCost;
            $credit = $this->configuration['credit'];
        }

        $this->plan->setCycleNext($cycleNext);
        $this->plan->setCycleStart($cycleStart);
        $this->plan->setBalance($balance);
        $this->plan->setCredit($credit);
    }

    public function renew() {
        $this->setup();
    }
}
?>