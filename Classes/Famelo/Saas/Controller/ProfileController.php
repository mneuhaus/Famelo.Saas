<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Factory\TeamFactory;
use TYPO3\Expose\Controller\EditController;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;

/**
 * @Flow\Scope("singleton")
 */
class ProfileController extends EditController {
	/**
	 * @var \Famelo\Saas\Domain\Service\TransactionService
	 * @Flow\Inject
	 */
	protected $transactionService;

	/**
	 * @var \Famelo\Saas\Services\NodeTargetUriService
	 * @Flow\Inject
	 */
	protected $nodeTargetUriService;

	/**
	 * @return void
	 */
	public function initializeIndexAction() {
		if (isset($this->arguments['objects'])) {
			$this->arguments['objects']->setDataType('Doctrine\Common\Collections\Collection<' . $this->request->getArgument('type') . '>');
			$this->arguments['objects']->getPropertyMappingConfiguration()->allowAllProperties();
		}
	}

	/**
	 * Edit object
	 *
	 * @param string $type
	 * @param \Doctrine\Common\Collections\Collection $objects
	 * @return void
	 */
	public function indexAction($type = NULL, $objects = NULL) {
		$type = '\Famelo\Saas\Domain\Model\User';
		$objects = array($this->transactionService->getUser());
		$this->view->assign('className', $type);
		$this->view->assign('objects', $objects);
		$this->view->assign('callbackAction', 'update');
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function updateAction($type) {
		$objects = $this->request->getInternalArgument('__objects');
		foreach ($objects as $object) {
			$this->persistenceManager->update($object);
			$this->persistenceManager->update($object->getTeam());
			$this->persistenceManager->update($object->getAccount());
			$this->persistenceManager->update($object->getName());
		}
		$this->persistenceManager->persistAll();
		$this->flashMessageContainer->addMessage(new Message('Account has been updated.'));
		$this->redirectToUri($this->nodeTargetUriService->getUri('mein-konto'));
	}
}

?>