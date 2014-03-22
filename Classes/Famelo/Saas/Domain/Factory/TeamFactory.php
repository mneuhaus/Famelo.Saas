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

use Famelo\Saas\Domain\Model\Subscription;
use Famelo\Saas\Domain\Model\Transaction;
use Famelo\Saas\Domain\Model\User;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Account;

/**
 * A factory to conveniently create User models
 *
 */
class TeamFactory {
	/**
	 * @var string
	 * @Flow\Inject(setting="defaults.plan")
	 */
	protected $defaultPlan;

	/**
	 * @var string
	 * @Flow\Inject(setting="defaults.currency")
	 */
	protected $defaultCurrency;

	/**
	 * @var string
	 * @Flow\Inject(setting="defaults.balance")
	 */
	protected $defaultBalance;

	/**
	 * @var \Famelo\Saas\Domain\Repository\SubscriptionRepository
	 * @Flow\Inject
	 */
	protected $subscriptionRepository;

	/**
	 * Creates a Team
	 *
	 * @param string $name Name of the team
	 * @param string $username The username of the user to be created.
	 * @param string $password Password of the user to be created
	 * @param string $firstName First name of the user to be created
	 * @param string $lastName Last name of the user to be created
	 * @param array $roleIdentifiers A list of role identifiers to assign
	 * @return \TYPO3\Neos\Domain\Model\User The created user instance
	 */
	public function create($name = NULL, $username = NULL, $password = NULL, $email = NULL, $firstName = NULL, $lastName = NULL, array $roleIdentifiers = NULL) {
		$team = new \Famelo\Saas\Domain\Model\Team();
		$team->setName($name);

		$userFactory = new UserFactory();
		$user = $userFactory->create($username, $password, $email, $firstName, $lastName, $roleIdentifiers);
		$team->addUser($user);
		$user->setTeam($team);

		return $team;
	}

	public function preSave($team) {
		$userFactory = new UserFactory();
		$userFactory->preSave($team->getMainUser());

		if ($team->getSubscription() instanceof Subscription) {
			return;
		}

		$subscription = new Subscription();
		$subscription->setPlan($this->defaultPlan);
		$subscription->setCurrency($this->defaultCurrency);
		$subscription->setTeam($team);

		if ($this->defaultBalance > 0) {
			$transaction = new Transaction();
			$transaction->setCurrency($this->defaultCurrency);
			$transaction->setAmount($this->defaultBalance);
			$transaction->setNote('start balance');
			$subscription->addTransaction($transaction);
		}
		$this->subscriptionRepository->add($subscription);
		$team->setSubscription($subscription);
	}

}
