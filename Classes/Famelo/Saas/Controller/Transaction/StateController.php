<?php
namespace Famelo\Saas\Controller\Transaction;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use Famelo\Broensfin\Domain\Model\Claim;
use Famelo\Broensfin\Domain\Model\ClaimComment;
use Famelo\Broensfin\Domain\Model\ClaimState;
use TYPO3\Expose\Controller\ExposeControllerInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * Action to create a new Being
 *
 */
class StateController extends \TYPO3\Flow\Mvc\Controller\ActionController implements ExposeControllerInterface {
	/**
	 * @var \Famelo\Saas\Domain\Repository\TransactionRepository
	 * @Flow\Inject
	 */
	protected $transactionRepository;

	/**
	 * @return void
	 */
	public function initializeSetStateAction() {
		$this->arguments['objects']->setDataType('Doctrine\Common\Collections\Collection<' . $this->request->getArgument('type') . '>');
		$this->arguments['objects']->getPropertyMappingConfiguration()->allowAllProperties();
	}

	/**
	 *
	 * @param string $type
	 * @param \Doctrine\Common\Collections\Collection $objects
	 * @param string $state
	 * @return void
	 */
	public function setStateAction($type, $objects, $state) {
		foreach ($objects as $transaction) {
			$transaction->setState($state);
			$this->transactionRepository->update($transaction);
		}
		$this->redirect('index', 'List', 'TYPO3.Expose', array(
			'type' => $type
		));
	}

	/**
	 *
	 * @param \Famelo\Saas\Domain\Model\Team $team
	 * @return void
	 */
	public function suspendUserAction($team) {
		$team->setSuspended(TRUE);
		$this->persistenceManager->update($team);

		$mail = new \Famelo\Messaging\Message();
		$mail->setMessage('Famelo.Saas:Suspended')
			 ->assign('team', $team)
			 ->send();

		$this->redirect('index', 'List', 'TYPO3.Expose', array(
			'type' => '\Famelo\Broensfin\Domain\Model\Claim'
		));
	}

	/**
	 *
	 * @param \Famelo\Saas\Domain\Model\Team $team
	 * @return void
	 */
	public function unsuspendUserAction($team) {
		$team->setSuspended(FALSE);
		$this->persistenceManager->update($team);
		$this->redirect('index', 'List', 'TYPO3.Expose', array(
			'type' => '\Famelo\Broensfin\Domain\Model\Claim'
		));
	}
}

?>