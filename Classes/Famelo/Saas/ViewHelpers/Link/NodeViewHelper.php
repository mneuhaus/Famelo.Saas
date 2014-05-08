<?php
namespace Famelo\Saas\ViewHelpers\Link;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\Flow\Annotations as Flow;

class NodeViewHelper extends AbstractTagBasedViewHelper {
	/**
	 * @var \Famelo\Saas\Services\NodeTargetUriService
	 * @Flow\Inject
	 */
	protected $nodeTargetUriService;

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Render the link.
	 *
	 * @param string $path
	 * @param string $appendix
	 * @return string The rendered link
	 */
	public function render($path, $appendix = '') {
		$uri = $this->nodeTargetUriService->getUri($path);

		$this->tag->addAttribute('href', $uri . $appendix);
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);

		return $this->tag->render();
	}
}
