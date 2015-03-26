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
	 * @return string
	 */
	public function indexAction() {
		$party = $this->securityContext->getParty();
		$this->view->assign('party', $party);
		foreach ($this->plans as $planName => $plan) {
			if ($party->getPlan() !== NULL) {
				$this->plans[$planName]['current'] = $party->getPlan()->getType() == $planName;
			}
		}
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
			$plan->getImplementation()->setup();
			$this->persistenceManager->add($plan);
			$this->persistenceManager->persistAll();
		} else {
			$plan->setType($planName);
			$plan->getImplementation()->setup();
			$this->persistenceManager->update($plan);
			$this->persistenceManager->persistAll();
		}

		if ($plan->getBalance() < 0) {
			$this->redirect('choose', 'Payment');
		} else {
			$interceptedRequest = $this->securityContext->getInterceptedRequest();
			if ($interceptedRequest !== NULL) {
				$this->securityContext->setInterceptedRequest(NULL);
				$this->redirectToRequest($interceptedRequest);
			} else {
				$this->redirect('index');
			}
		}
	}
}
?>