<?php
namespace Famelo\Saas\Backend\Controller;

use Famelo\Saas\Domain\Model\InviteRequest;
use Famelo\Saas\Domain\Repository\InviteRequestRepository;
use Flowpack\Expose\Controller\CrudController;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;

/**
 * @Flow\Scope("singleton")
 */
class InviteController extends CrudController {
	/**
	 * @var string
	 */
	protected $entity = 'Famelo\Saas\Domain\Model\InviteRequest';

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
