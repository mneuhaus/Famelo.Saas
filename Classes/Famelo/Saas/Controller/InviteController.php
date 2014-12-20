<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Model\Invitation;
use Famelo\Saas\Domain\Repository\InviteRequestRepository;
use Famelo\Soul\Controller\AbstractSoulController;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\Controller\ActionController;

/**
 * @Flow\Scope("singleton")
 */
class InviteController extends ActionController {
	/**
	 * @Flow\Inject
	 * @var InviteRequestRepository
	 */
	protected $inviteRequestRepository;

	public function indexAction($email) {

	}

	/**
	 *
	 * @param string Provide information about a function parameter.$email
	 * @return string
	 */
	public function requestAction($email) {
		if ($this->inviteRequestRepository->findOneByEmail($email) === FALSE) {
			$request = new Invitation();
			$request->setEmail($email);
			$this->persistenceManager->add($request);
		}
	}
}
