<?php
namespace Famelo\Saas\Command;

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

/**
 * The User Command Controller
 *
 * @Flow\Scope("singleton")
 */
class SaasCommandController extends \TYPO3\Flow\Cli\CommandController {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Party\Domain\Repository\PartyRepository
	 */
	protected $partyRepository;

	/**
	 * @Flow\Inject
	 * @var \Famelo\Saas\Domain\Factory\UserFactory
	 */
	protected $userFactory;

	/**
	 * Create a new user
	 *
	 * This command creates a new user which has access to the backend user interface.
	 * It is recommended to user the email address as a username.
	 *
	 * @param string $username The username of the user to be created.
	 * @param string $password Password of the user to be created
	 * @param string $firstName First name of the user to be created
	 * @param string $lastName Last name of the user to be created
	 * @param string $roles A comma separated list of roles to assign
	 * @return void
	 */
	public function createCommand($username, $password, $email, $firstName, $lastName, $roles = NULL) {
		$account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($username, 'SaasProvider');
		if ($account instanceof \TYPO3\Flow\Security\Account) {
			$this->outputLine('The username "%s" is already in use', array($username));
			$this->quit(1);
		}

		$roleIdentifiers = array();
		// if (empty($roles)) {
		// 	$roleIdentifiers = array('TYPO3.Neos:Editor');
		// } else {
		// 	$roleIdentifiers = \TYPO3\Flow\Utility\Arrays::trimExplode(',', $roles);
		// 	foreach ($roleIdentifiers as &$role) {
		// 		if (strpos($role, '.') === FALSE) {
		// 			$role = 'TYPO3.Neos:' . $role;
		// 		}
		// 	}
		// }

		try {
			$user = $this->userFactory->create($username, $password, $email, $firstName, $lastName, $roleIdentifiers);
			$this->partyRepository->add($user);
			$accounts = $user->getAccounts();
			foreach ($accounts as $account) {
				$this->accountRepository->add($account);
			}

			$this->outputLine('Created user "%s".', array($username));
		} catch (\TYPO3\Flow\Security\Exception\NoSuchRoleException $exception) {
			$this->outputLine($exception->getMessage());
			$this->quit(1);
		}

	}

}
