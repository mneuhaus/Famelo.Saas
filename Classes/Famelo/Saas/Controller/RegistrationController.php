<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Factory\TeamFactory;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;

/**
 * @Flow\Scope("singleton")
 */
class RegistrationController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 *
	 *
	 * @return string
	 */
	public function indexAction() {
		$factory = new TeamFactory();
		$this->view->assign('team', $factory->create());
	}

	/**
	 *
	 * @return string
	 */
	public function createAction() {
		$teams = $this->request->getInternalArgument('__objects');
		$factory = new TeamFactory();
		foreach ($teams as $team) {
			$factory->preSave($team);
			$this->persistenceManager->add($team);
			$this->persistenceManager->add($team->getMainUser()->getAccount());
		}
		$this->flashMessageContainer->addMessage(new Message('Account has been created.'));
		$this->redirectToUri('/de/login.html');
	}
}

?>