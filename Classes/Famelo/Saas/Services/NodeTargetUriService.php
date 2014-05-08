<?php
namespace Famelo\Saas\Services;

use Famelo\Saas\Core\SaasMatcher;
use Famelo\Saas\Domain\Model\Transaction;
use TYPO3\Eel\Context;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class NodeTargetUriService {

	/**
	 * @var \TYPO3\Flow\I18n\Service
	 * @Flow\Inject
	 */
	protected $localizationService;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository
	 * @Flow\Inject
	 */
	protected $nodeDataRepository;

	/**
	 * @var \TYPO3\Flow\Mvc\Routing\UriBuilder
	 */
	protected $uriBuilder;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\Node
	 */
	protected $siteNode;

	public function setPageNode($node) {
		$this->siteNode = $node->getContext()->getCurrentSiteNode();
	}

	public function setUriBuilder($uriBuilder) {
		$this->uriBuilder = $uriBuilder;
	}

	public function getUri($targetPath) {
		$locale = $this->localizationService->getConfiguration()->getCurrentLocale();
		$targetPath = $this->siteNode->getNodeData()->normalizePath($targetPath);
		$targetNode = $this->nodeDataRepository->findOneByPath($targetPath, $this->siteNode->getWorkspace());
		$localeContext = $this->siteNode->getContext();
		$localizedNode = $localeContext->getNodeByIdentifier($targetNode->getIdentifier());

		return $this->uriBuilder
			->reset()
			->setCreateAbsoluteUri(true)
			->uriFor('show', array('node' => $localizedNode), 'Frontend\Node', 'TYPO3.Neos');
	}
}