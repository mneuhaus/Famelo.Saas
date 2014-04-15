<?php
namespace Famelo\Saas\Neos\TypoScript;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Neos".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Neos\Domain\Service\NodeShortcutResolver;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TypoScript\Exception as TypoScriptException;

/**
 * A TypoScript Menu object
 */
class MenuImplementation extends \TYPO3\Neos\TypoScript\MenuImplementation {
	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * Prepare the menu item with state and sub items if this isn't the last menu level.
	 *
	 * @param NodeInterface $currentNode
	 * @return array
	 */
	protected function buildMenuItemRecursive(NodeInterface $currentNode) {
		$access = $currentNode->getProperty('access');
		if ($access !== NULL && $access !== '' && $access !== 'everyone') {
			if ($access === 'guests' && $this->securityContext->getAccount() !== NULL) {
				return NULL;
			}
			if ($access === 'authenticated' && $this->securityContext->getAccount() === NULL) {
				return NULL;
			}
			if (!in_array($access, array('guests', 'authenticated')) && $this->securityContext->hasRole($access) === FALSE) {
				return NULL;
			}
		}
		return parent::buildMenuItemRecursive($currentNode);
	}
}