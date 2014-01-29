<?php
namespace Famelo\Saas\Tests\Functional;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Party\Domain;

/**
 */
class SaasTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	* @var boolean
	*/
	static protected $testablePersistenceEnabled = TRUE;

	/**
	* @var \TYPO3\Party\Domain\Repository\PartyRepository
	*/
	protected $partyRepository;

	/**
	* @var \TYPO3\Flow\Security\AccountRepository
	*/
	protected $accountRepository;

	/**
	* @var \TYPO3\Flow\Security\AccountFactory
	*/
	protected $accountFactory;

	/**
	*/
	public function setUp() {
		parent::setUp();
		$this->partyRepository = $this->objectManager->get('TYPO3\Party\Domain\Repository\PartyRepository');
		$this->accountRepository = $this->objectManager->get('TYPO3\Flow\Security\AccountRepository');
		$this->accountFactory = $this->objectManager->get('TYPO3\Flow\Security\AccountFactory');
	}

	/**
	 * @test
	 */
	public function planTest() {
		$person = new Domain\Model\Person();
		$person->setName(new Domain\Model\PersonName('', $firstName, $middleName, $lastName));

		$electronicAddress = new Domain\Model\ElectronicAddress();
		$electronicAddress->setType(Domain\Model\ElectronicAddress::TYPE_EMAIL);
		$electronicAddress->setIdentifier($emailAddress);
		$person->setPrimaryElectronicAddress($electronicAddress);

		$account = $this->accountFactory->createAccountWithPassword($emailAddress, $this->persistenceManager->getIdentifierByObject($person));
		$this->accountRepository->add($account);
		$person->addAccount($account);

		$this->partyRepository->add($person);
		$this->persistenceManager->persistAll();
		$this->assertEquals(1, $this->partyRepository->countAll());

		$this->persistenceManager->clearState();
		$foundPerson = $this->partyRepository->findByIdentifier($this->persistenceManager->getIdentifierByObject($person));

		$this->assertEquals($foundPerson->getName()->getFullName(), $person->getName()->getFullName());
		$this->assertEquals($foundPerson->getName()->getFullName(), $firstName . ' ' . $middleName . ' ' . $lastName);
		$this->assertEquals($foundPerson->getPrimaryElectronicAddress()->getIdentifier(), $emailAddress);
	}
}
