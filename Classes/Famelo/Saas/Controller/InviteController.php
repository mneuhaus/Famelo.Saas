<?php
namespace Famelo\Saas\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;

/**
 * @Flow\Scope("singleton")
 */
class InviteController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\Flow\Security\Policy\PolicyService
	 * @Flow\Inject
	 */
	protected $policyService;

	/**
	 *
	 * @param \Famelo\Saas\Domain\Model\User $user
	 * @return string
	 */
	public function indexAction($user) {
		$user->addAccount(new \TYPO3\Flow\Security\Account());
		$this->view->assign('team', $user->getTeam());
	}

	/**
	 *
	 * @return string
	 */
	public function updateAction() {
		$teams = $this->request->getInternalArgument('__objects');
		$role = $this->policyService->getRole('Famelo.Saas:Customer');
		foreach ($teams as $team) {
			$account = $team->getMainUser()->getAccount();
            $account->setAuthenticationProviderName('SaasProvider');
            $account->addRole($role);
			$this->persistenceManager->update($team);
			$this->persistenceManager->add($account);
		}
		$this->flashMessageContainer->addMessage(new Message('Account has been created.'));
		$this->redirectToUri('/de/mein-konto.html');
	}
}

?>