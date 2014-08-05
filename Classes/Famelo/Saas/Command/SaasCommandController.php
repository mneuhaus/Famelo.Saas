<?php
namespace Famelo\Saas\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Harvest".         *
 *                                                                        *
 *                                                                        */

use Famelo\Harvest\Command\Steps\CreatePullRequests;
use Famelo\Harvest\Services\Git;
use Famelo\Saas\Domain\Repository\PlanRepository;
use TYPO3\Flow\Annotations as Flow;

class SaasCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @Flow\Inject
	 * @var PlanRepository
	 */
	protected $planRepository;

	/**
	 * Renew Subscriptions
	 *
	 * @return void
	 */
	public function renewSubscriptionsCommand() {
		$query = $this->planRepository->createQuery();
		$query->matching($query->lessThan('cycleNext', new \DateTime()));
		foreach ($query->execute() as $plan) {
			$plan->updateBalance();
			$planImplementation = $plan->getImplementation();
			$transaction = $planImplementation->createTransaction($plan->getDueAmount(), 'renew', $plan->getCurrency());
			$transaction->setTenant($plan->getMainParty());
			$planImplementation->addTransaction($transaction);
		}
	}

}