<?php
namespace Famelo\Saas\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Saas".           *
 *                                                                        *
 *                                                                        */

use Famelo\Saas\Domain\Model\Subscription;
use TYPO3\Flow\Annotations as Flow;

class AdminDashboardController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \Famelo\Broensfin\Domain\Repository\ClaimRepository
	 * @Flow\Inject
	 */
	protected $claimRepository;

	/**
	 * @var \Famelo\Saas\Domain\Repository\TeamRepository
	 * @Flow\Inject
	 */
	protected $teamRepository;

	/**
	 * @return void
	 */
	public function indexAction() {
		$states = array(
			'pending' => 0,
			'accepted' => 0,
			'rejected' => 0
		);
		foreach ($states as $key => $value) {
			$query = $this->claimRepository->createQuery();
			$query->matching(
				$query->equals('currentState.state', $key)
			);
			$states[$key] = $query->count();
		}

		$teams = array(
			'active' => 0,
			'suspended' => 0,
			'balance' => 0
		);
		foreach ($this->teamRepository->findAll() as $team) {
			if ($team->getSuspended() === TRUE) {
				$teams['suspended']++;
			} else {
				$teams['active']++;
			}
			if ($team->getSubscription() instanceof	Subscription) {
				$teams['balance'] += $team->getSubscription()->getBalance();
			}
		}
		$this->view->assign('states', $states);
		$this->view->assign('teams', $teams);
	}

}