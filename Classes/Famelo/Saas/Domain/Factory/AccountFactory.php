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
class AccountFactory {

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
	 * @param string $username The username of the user to be created.
	 * @param string $password Password of the user to be created
	 * @param array $roleIdentifiers A list of role identifiers to assign
	 * @return \TYPO3\Neos\Domain\Model\User The created user instance
	 */
	public function create($username = NULL, $password = NULL, $roleIdentifiers = NULL) {
		if (empty($roleIdentifiers)) {
			$roleIdentifiers = array('Famelo.Saas:Customer');
		}

		if ($username !== NULL && $password !== NULL) {
			$account = $this->accountFactory->createAccountWithPassword($username, $password, $roleIdentifiers, 'SaasProvider');
		} else {
			$account = new Account();
			foreach ($roleIdentifiers as $roleIdentifier) {
				$account->addRole($this->policyService->getRole($roleIdentifier));
			}
			$account->setAuthenticationProviderName('SaasProvider');
		}

		return $account;
	}

	public function preSave($account) {
		$account->addRole($this->policyService->getRole('Famelo.Saas:Customer'));
		$account->setAuthenticationProviderName('SaasProvider');
	}

}
