<?php
namespace Famelo\Saas\Security;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Authentication\Token\UsernamePassword;
use TYPO3\Flow\Security\Authentication\TokenInterface;
use TYPO3\Flow\Security\Exception\UnsupportedAuthenticationTokenException;

/**
 * An authentication provider that authenticates
 * TYPO3\Flow\Security\Authentication\Token\UsernamePassword tokens.
 * The accounts are stored in the Content Repository.
 */
class PersistedUsernamePasswordProvider extends \TYPO3\Flow\Security\Authentication\Provider\PersistedUsernamePasswordProvider {

	/**
	 * @var \TYPO3\Flow\Security\AccountRepository
	 * @Flow\Inject
	 */
	protected $accountRepository;

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\HashService
	 * @Flow\Inject
	 */
	protected $hashService;

	/**
	 * @var \TYPO3\Flow\Security\Context
	 * @Flow\Inject
	 */
	protected $securityContext;

	/**
	 * Returns the class names of the tokens this provider can authenticate.
	 *
	 * @return array
	 */
	public function getTokenClassNames() {
		return array('TYPO3\Flow\Security\Authentication\Token\UsernamePassword', 'TYPO3\Flow\Security\Authentication\Token\UsernamePasswordHttpBasic');
	}

	/**
	 * Checks the given token for validity and sets the token authentication status
	 * accordingly (success, wrong credentials or no credentials given).
	 *
	 * @param \TYPO3\Flow\Security\Authentication\TokenInterface $authenticationToken The token to be authenticated
	 * @return void
	 * @throws \TYPO3\Flow\Security\Exception\UnsupportedAuthenticationTokenException
	 */
	public function authenticate(TokenInterface $authenticationToken) {
		if (!($authenticationToken instanceof UsernamePassword)) {
			throw new UnsupportedAuthenticationTokenException('This provider cannot authenticate the given token.', 1217339840);
		}

		/** @var $account \TYPO3\Flow\Security\Account */
		$account = NULL;
		$credentials = $authenticationToken->getCredentials();

		if (is_array($credentials) && isset($credentials['username'])) {
			$providerName = $this->name;
			$accountRepository = $this->accountRepository;
			$this->securityContext->withoutAuthorizationChecks(function() use ($credentials, $providerName, $accountRepository, &$account) {
				$account = $accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($credentials['username'], $providerName);
			});
		}

		if (is_object($account)) {
			if ($account->getParty()->getTeam()->getSuspended() === TRUE) {
				$this->redirectToUri('/de/ihr-konto-wurde-gesperrt.html');
			}
			if ($this->hashService->validatePassword($credentials['password'], $account->getCredentialsSource())) {
				$authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
				$authenticationToken->setAccount($account);
			} else {
				$authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
			}
		} elseif ($authenticationToken->getAuthenticationStatus() !== TokenInterface::AUTHENTICATION_SUCCESSFUL) {
			$authenticationToken->setAuthenticationStatus(TokenInterface::NO_CREDENTIALS_GIVEN);
		}
	}

	/**
	 * Redirects to another URI
	 *
	 * @param mixed $uri Either a string representation of a URI or a \TYPO3\Flow\Http\Uri object
	 * @param integer $delay (optional) The delay in seconds. Default is no delay.
	 * @param integer $statusCode (optional) The HTTP status code for the redirect. Default is "303 See Other"
	 * @throws \TYPO3\Flow\Mvc\Exception\UnsupportedRequestTypeException If the request is not a web request
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 * @api
	 */
	protected function redirectToUri($uri, $delay = 0, $statusCode = 303) {
		$escapedUri = htmlentities($uri, ENT_QUOTES, 'utf-8');
		header('Location: ' . (string)$uri);
		echo '<html><head><meta http-equiv="refresh" content="' . intval($delay) . ';url=' . $escapedUri . '"/></head></html>';
		exit();
	}

}
