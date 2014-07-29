<?php
namespace Famelo\Saas\Handlers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Expose\Security\PolicyMatcher;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\RequestMatcher;
use TYPO3\Flow\Security\Account;

/**
 *
 * @Flow\Scope("singleton")
 */
class MessageHandler implements HandlerInterface {
    /**
      * contains short name for this matcher used for reference in the eel expression
      */
    const NAME = 'message';

	/**
	 * The flash messages. Use $this->flashMessageContainer->addMessage(...) to add a new Flash
	 * Message.
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\FlashMessageContainer
	 */
	protected $flashMessageContainer;

	/**
	 * @var array
	 */
	protected $duplicateCache = array();

	/**
	 * @param string $messageBody text of the FlashMessage
	 * @param string $messageTitle optional header of the FlashMessage
	 * @param array $messageArguments arguments to be passed to the FlashMessage
	 * @param integer $messageCode
	 * @return void
	 * @see \TYPO3\Flow\Error\Message
	 * @api
	 */
	public function error($messageBody, $messageTitle = '', array $messageArguments = array(), $messageCode = NULL) {
		$message = new \TYPO3\Flow\Error\Error($messageBody, $messageCode, $messageArguments, $messageTitle);

		if (in_array($message, $this->flashMessageContainer->getMessages())) {
			return;
		}

		$this->flashMessageContainer->addMessage($message);
	}

	/**
	 * @param string $messageBody text of the FlashMessage
	 * @param string $messageTitle optional header of the FlashMessage
	 * @param array $messageArguments arguments to be passed to the FlashMessage
	 * @param integer $messageCode
	 * @return void
	 * @see \TYPO3\Flow\Error\Message
	 * @api
	 */
	public function warning($messageBody, $messageTitle = '', array $messageArguments = array(), $messageCode = NULL) {
		$message = new \TYPO3\Flow\Error\Warning($messageBody, $messageCode, $messageArguments, $messageTitle);

		if (in_array($message, $this->flashMessageContainer->getMessages())) {
			return;
		}

		$this->flashMessageContainer->addMessage($message);
	}

	/**
	 * @param string $messageBody text of the FlashMessage
	 * @param string $messageTitle optional header of the FlashMessage
	 * @param array $messageArguments arguments to be passed to the FlashMessage
	 * @param integer $messageCode
	 * @return void
	 * @see \TYPO3\Flow\Error\Message
	 * @api
	 */
	public function notice($messageBody, $messageTitle = '', array $messageArguments = array(), $messageCode = NULL) {
		$message = new \TYPO3\Flow\Error\Notice($messageBody, $messageCode, $messageArguments, $messageTitle);

		if (in_array($message, $this->flashMessageContainer->getMessages())) {
			return;
		}

		$this->flashMessageContainer->addMessage($message);
	}

	/**
	 * @param string $messageBody text of the FlashMessage
	 * @param string $messageTitle optional header of the FlashMessage
	 * @param array $messageArguments arguments to be passed to the FlashMessage
	 * @param integer $messageCode
	 * @return void
	 * @see \TYPO3\Flow\Error\Message
	 * @api
	 */
	public function info($messageBody, $messageTitle = '', array $messageArguments = array(), $messageCode = NULL) {
		$message = new Message($messageBody, $messageCode, $messageArguments, $messageTitle);

		if (in_array($message, $this->flashMessageContainer->getMessages())) {
			return;
		}

		$this->flashMessageContainer->addMessage($message);
	}
}
?>