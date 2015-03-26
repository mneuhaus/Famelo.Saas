<?php
namespace Famelo\Saas\Tests\Functional;

use Famelo\Saas\Domain\Model\Plan;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Saas".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
class PlanImplementationTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = TRUE;

	public function getPlan($type) {
		$plan = new Plan();
		$plan->setConfiguration(array(
			'someFlatrate' => array(
				'name' => "Flatrate",
		        'cycle' => "1 month",
		        'cost' => 5.0,
		        'credit' => 5,
		        'implementation' => 'Famelo\\Saas\\Plan\\FlatrateImplementation'
			),
			'biggerFlatrate' => array(
				'name' => "Flatrate",
		        'cycle' => "1 month",
		        'cost' => 20.0,
		        'credit' => 10,
		        'implementation' => 'Famelo\\Saas\\Plan\\FlatrateImplementation'
			),
			'someDeposit' => array(
				'name' => "Deposit",
		        'cost' => 10.0,
		        'credit' => 5,
		        'implementation' => 'Famelo\\Saas\\Plan\\DepositImplementation'
			)
		));

		$plan->setType($type);
		$plan->getImplementation()->setup();
		$this->persistenceManager->add($plan);
		$this->persistenceManager->persistAll();

		return $plan;
	}

	/**
	 * @test
	 */
	public function flatrateImplementationTest() {
		$plan = $this->getPlan('someFlatrate');

		$this->assertEquals('1 month', $plan->getCycle());

		$now = new \DateTime();

		$this->assertEquals($now->format('d.m.Y'), $plan->getCycleStart()->format('d.m.Y'));
		$this->assertEquals($now->modify('1 month')->format('d.m.Y'), $plan->getCycleNext()->format('d.m.Y'));
		$this->assertEquals(-5.0, $plan->getBalance());

		// Pay off the debt
		$transaction = $plan->getImplementation()->createTransaction(5.0, 'some payment provider', 'EUR');
		$plan->getImplementation()->addTransaction($transaction);

		$this->assertEquals(0.0, $plan->getBalance());

		// check the credits
		$implementation = $plan->getImplementation();
		$this->assertEquals(5, $plan->getCredit());
		$implementation->withdrawCredit(1);
		$this->assertEquals(4, $plan->getCredit());

		$plan->setCycleNext($now);
		$plan->getImplementation()->renew();
		$this->assertEquals($now->modify('1 month')->format('d.m.Y'), $plan->getCycleNext()->format('d.m.Y'));
		$this->assertEquals(-5.0, $plan->getBalance());

		$this->assertEquals(5, $plan->getCredit());
	}

	/**
	 * @test
	 */
	public function flatrateUpgradeImplementationTest() {
		$plan = $this->getPlan('someFlatrate');

		// check the credits
		$implementation = $plan->getImplementation();
		$this->assertEquals(5, $plan->getCredit());

		$implementation->withdrawCredit(1);
		$this->assertEquals(4, $plan->getCredit());

		$plan->setType('biggerFlatrate');
		$plan->getImplementation()->setup();
		$this->assertEquals(10, $plan->getCredit());
	}

	/**
	 * @test
	 */
	public function depositImplementationTest() {
		// choose a deposit with 5 credits
		$plan = $this->getPlan('someDeposit');
		$this->assertEquals(-10.0, $plan->getBalance());
		$this->assertEquals(5, $plan->getCredit());

		// Pay off the debt
		$transaction = $plan->getImplementation()->createTransaction(10.0, 'some payment provider', 'EUR');
		$plan->getImplementation()->addTransaction($transaction);
		$this->assertEquals(0.0, $plan->getBalance());

		// check the credits
		$implementation = $plan->getImplementation();
		$this->assertEquals(TRUE, $implementation->hasCredit(5));
		$this->assertEquals(FALSE, $implementation->hasCredit(6));

		// add some more credit
		$plan->setType('someDeposit');
		$plan->getImplementation()->setup();
		$this->assertEquals(-10.0, $plan->getBalance());
		$this->assertEquals(TRUE, $implementation->hasCredit(10));

		$implementation->withdrawCredit(1);
		$this->assertEquals(FALSE, $implementation->hasCredit(10));
	}
}
?>