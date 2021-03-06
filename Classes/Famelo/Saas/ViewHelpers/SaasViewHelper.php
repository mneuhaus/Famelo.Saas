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
class SaasViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * @param string $as
	 * @return string Rendered string
	 * @api
	 */
	public function render($as = 'transactionService') {
		$this->templateVariableContainer->add($as, $this->transactionService);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		return $content;
	}
}

?>