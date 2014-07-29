<?php
namespace Famelo\Saas\Core;

use Famelo\Saas\Core\HandlerMatcher;
use TYPO3\Eel\Context;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Aspect
 */
class RuleEnforcementAspect {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Eel\CompilingEvaluator
	 */
	protected $eelEvaluator;

	/**
	 * @Flow\Before("method(.*\Controller\.*->.*Action(*))")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current join point
	 * @return void
	 */
	public function decideOnJoinPoint(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$rules = $this->configurationManager->getConfiguration('Rules');

		$this->request = ObjectAccess::getProperty($joinPoint->getProxy(), 'request', TRUE);
		$this->response = ObjectAccess::getProperty($joinPoint->getProxy(), 'response', TRUE);
		$scopeMatcher = new ScopeMatcher($this->request, NULL, $joinPoint, $joinPoint->getMethodName(), $joinPoint->getClassName());
		$scopeContext = new Context($scopeMatcher);

		$handlerMatcher = new HandlerMatcher($this->request, $this->response);
		$handlerContext = new Context($handlerMatcher);

		foreach ($rules as $name => $rule) {
			if ($this->eelEvaluator->evaluate($rule['scope'], $scopeContext) === TRUE) {
				$ruleMatcher = new RuleMatcher($this->request, NULL, $joinPoint, $joinPoint->getMethodName(), $joinPoint->getClassName());
				$ruleContext = new Context($ruleMatcher);
				$result = $this->eelEvaluator->evaluate($rule['rules'], $ruleContext);

				if ($result === TRUE && isset($rule['onSuccess'])) {
					if (is_array($rule['onSuccess'])) {
						foreach ($rule['onSuccess'] as $onSuccess) {
							$this->eelEvaluator->evaluate($onSuccess, $handlerContext);
						}
					} else {
						$this->eelEvaluator->evaluate($rule['onSuccess'], $handlerContext);
					}
					$handlerMatcher->postProcess();
				}

				if ($result === FALSE && isset($rule['onFail'])) {
					if (is_array($rule['onFail'])) {
						foreach ($rule['onFail'] as $onFail) {
							$this->eelEvaluator->evaluate($onFail, $handlerContext);
						}
					} else {
						$this->eelEvaluator->evaluate($rule['onFail'], $handlerContext);
					}
					$handlerMatcher->postProcess();
				}
			}
		}
	}
}
?>
