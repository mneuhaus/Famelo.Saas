<?php
namespace Famelo\Saas\Controller;

use Famelo\Saas\Domain\Model\Plan;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
/**
 * @Flow\Scope("singleton")
 */
class PlanController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * @Flow\Inject(setting="Plans", package="Famelo.Saas")
	 * @var array
	 */
	protected $plans;

	/**
	 * @Flow\Inject(setting="PaymentGateways", package="Famelo.Saas")
	 * @var array
	 */
	protected $paymentGateways;

	/**
	 *
	 * @param \Famelo\Saas\Domain\Model\User $user
	 * @return string
	 */
	public function indexAction($user) {
		$party = $this->securityContext->getParty();
		$this->view->assign('party', $party);
	}

	/**
	 *
	 * @return string
	 */
	public function chooseAction() {
		$party = $this->securityContext->getParty();
		$this->view->assign('party', $party);
		$this->view->assign('plans', $this->plans);
		$this->view->assign('col-width', 12 / count($this->plans));
	}

	/**
	 *
	 * @param string $planName
	 * @return string
	 */
	public function selectAction($planName) {
		$party = $this->securityContext->getParty();

		$plan = $party->getPlan();
		if (!$plan instanceof Plan) {
			$plan = new Plan();
			$plan->setType($planName);
			$plan->addParty($party);
			$this->persistenceManager->add($plan);
			$this->persistenceManager->persistAll();
		} else {
			$plan->setType($planName);
			$this->persistenceManager->update($plan);
			$this->persistenceManager->persistAll();
		}

		if ($plan->getDueAmount() > 0) {
			$this->redirect('choose', 'Payment');
		} else {
			$this->redirectToUri('/');
		}
	}
}
?>