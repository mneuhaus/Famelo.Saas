<?php
namespace Famelo\Saas\Domain\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Neos".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Account;

/**
 * A factory to conveniently create User models
 *
 * @Flow\Scope("singleton")
 */
class UserFactory {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\AccountFactory
	 */
	protected $accountFactory;

    /**
     * @var \TYPO3\Flow\Security\Policy\PolicyService
     * @Flow\Inject
     */
    protected $policyService;

    /**
     * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

	/**
	 * Creates a User with the given information
	 *
	 * The User is not added to the repository, the caller has to add the
	 * User account to the AccountRepository and the User to the
	 * PartyRepository to persist it.
	 *
	 * @param string $username The username of the user to be created.
	 * @param string $password Password of the user to be created
	 * @param string $firstName First name of the user to be created
	 * @param string $lastName Last name of the user to be created
	 * @param array $roleIdentifiers A list of role identifiers to assign
	 * @return \TYPO3\Neos\Domain\Model\User The created user instance
	 */
	public function create($username = NULL, $password = NULL, $email = NULL, $firstName = NULL, $lastName = NULL, array $roleIdentifiers = NULL) {
		$user = new \Famelo\Saas\Domain\Model\User();
		if ($firstName !== NULL || $lastName !== NULL) {
			$name = new \TYPO3\Party\Domain\Model\PersonName('', $firstName, '', $lastName, '', $username);
			$user->setName($name);
			$this->persistenceManager->add($name);
		}
		if ($email !== NULL) {
			$user->setEmail($email);
		}

		$accountFactory = new AccountFactory();
		$account = $accountFactory->create($username, $password, $roleIdentifiers);
		$user->addAccount($account);
		$account->setParty($user);

		return $user;
	}

	public function preSave($user) {
		$accountFactory = new AccountFactory();
		$accountFactory->preSave($user->getAccount());
	}

}
