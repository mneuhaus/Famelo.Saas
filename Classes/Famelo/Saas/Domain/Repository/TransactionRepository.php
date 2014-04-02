<?php
namespace Famelo\Saas\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;

/**
 *
 * @Flow\Scope("singleton")
 */
class TransactionRepository extends \TYPO3\Flow\Persistence\Repository {
	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	public function getUserTransactions() {
		$user = $this->securityContext->getPartyByType('\Famelo\Saas\Domain\Model\User');

		if ($user instanceof \Famelo\Saas\Domain\Model\User) {
			$team = $user->getTeam();
		}

		if ($team instanceof \Famelo\Saas\Domain\Model\Team) {
			$subscription = $team->getSubscription();
			$query = $this->createQuery();
			$query->matching($query->equals('subscription', $subscription));
			$query->setOrderings(array(
				'created' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING
			));
			return $query->execute();
		}

		return NULL;
	}

	public function createInvoiceQuery() {
		$query = $this->createQuery();
		$query->matching($query->greaterThan('amount', 0));
		return $query;
	}
}
?>