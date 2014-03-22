<?php
namespace Famelo\Saas\ViewHelpers;

/*                                                                        *
 * This script belongs to the Flow package "TYPO3.Expose".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * @api
 */
class UserViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * @param string $userClassName
	 * @param string $as
	 * @return string Rendered string
	 * @api
	 */
	public function render($userClassName = '\Famelo\Saas\Domain\Model\User', $as = 'user') {
		$this->templateVariableContainer->add($as, $this->securityContext->getPartyByType($userClassName));
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		return $content;
	}
}

?>