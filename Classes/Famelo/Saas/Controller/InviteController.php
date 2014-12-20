<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Model\InviteRequest;
use Famelo\Saas\Domain\Repository\InviteRequestRepository;
use Famelo\Soul\Controller\AbstractSoulController;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;

/**
 * @Flow\Scope("singleton")
 */
class InviteController extends AbstractSoulController {
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
			$request = new InviteRequest();
			$request->setEmail($email);
			$this->persistenceManager->add($request);
		}
	}
}
