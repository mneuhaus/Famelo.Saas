<?php
namespace Famelo\Saas\Aop;

/* *
 * This script belongs to the MyCopnay.MyPackage.
 * */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\I18n\Locale;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * The FrontendI18nLocaleSwitchingAspect
 *
 * @Flow\Aspect
 */
class NodeCatcherAspect {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\I18n\Service
	 */
	protected $localizationService;

	/**
	 * @var \Famelo\Saas\Services\NodeTargetUriService
	 * @Flow\Inject
	 */
	protected $nodeTargetUriService;

	/**
	 * @Flow\Before("method(TYPO3\Neos\Controller\Frontend\NodeController->showAction())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current join point
	 * @return NodeInterface
	 */
	public function catchNode(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		/** @var NodeInterface $node */
		$node = $joinPoint->getMethodArgument('node');
		$this->nodeTargetUriService->setPageNode($node);
		$this->nodeTargetUriService->setUriBuilder(ObjectAccess::getProperty($joinPoint->getProxy(), 'uriBuilder', TRUE));
	}
}
