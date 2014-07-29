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

	public function getPartyTransactions() {
		$party = $this->securityContext->getParty();

		if ($party instanceof SaasParty) {
			$plan = $party->getPlan();
			$query = $this->createQuery();
			$query->matching($query->equals('plan', $plan));
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