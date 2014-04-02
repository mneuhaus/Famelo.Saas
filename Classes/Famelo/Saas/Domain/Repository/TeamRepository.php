<?php
namespace Famelo\Saas\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;

/**
 *
 * @Flow\Scope("singleton")
 */
class TeamRepository extends \TYPO3\Flow\Persistence\Repository {
	public function findByCsv($row) {
		$query = $this->createQuery();

		$constraints = array();

		$identifiers = array(
			'name' => 'Debtor',
			'street' => 'Street',
			'city' => 'City',
			'zip' => 'Zip'
		);

		foreach ($identifiers as $internal => $external) {
			if (!empty($row[$external])) {
				$constraints[] = $query->equals($internal, $row[$external]);
			}
		}

		$query->matching($query->logicalAnd($constraints));

		return $query->execute();
	}
}
?>