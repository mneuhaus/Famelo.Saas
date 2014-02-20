<?php
namespace Famelo\Saas\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;

/**
 */
trait TeamQueryTrait {
	/**
	 * @return \TYPO3\Flow\Persistence\QueryInterface
	 */
	public function createQuery() {
		$query = parent::createQuery();
		// // $startDate = new \DateTime('last friday');
		// // $query->matching($query->greaterThan('created', $startDate));
		// $query->setOrderings(array(
		// 	'created' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING
		// ));

		// $customers = $this->customerRepository->findAll()->toArray();

		// $surveys = array();
		// foreach ($query->execute() as $survey) {
		// 	if (in_array($survey->getCustomer(), $customers)) {
		// 		$surveys[] = $survey;
		// 	}
		// }

		// $query = new \Famelo\Satisfy\Domain\ArrayQuery();
		// $query->setArray($surveys);
		return $query;
	}
}
?>