<?php
namespace Famelo\Saas\Controller;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class LoginController extends \TYPO3\Flow\Mvc\Controller\ActionController {
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 *
	 *
	 * @return string
	 */
	public function indexAction() {
		// if ($this->authenticationManager->getSecurityContext()->getAccount() !== NULL) {
		// 	$this->redirectToUri('/');
		// }
		$output = NULL;

		foreach ($this->authenticationManager->getSecurityContext()->getAuthenticationTokens() as $token) {
			if ($token->isAuthenticated() === TRUE) {
				$account = $token->getAccount();
				$output .= $account->getAccountIdentifier() . ' (' . $account->getAuthenticationProviderName() . ')<br />';
			}
		}

		return $output;
	}

	/**
	 * Authenticates an account by invoking the Provider based Authentication Manager.
	 *
	 * On successful authentication redirects to the list of posts, otherwise returns
	 * to the login screen.
	 *
	 * @return void
	 * @throws \TYPO3\Flow\Security\Exception\AuthenticationRequiredException
	 */
	public function authenticateAction() {
		try {
			$this->authenticationManager->authenticate();
			$this->redirectToUri('/');
		} catch (\TYPO3\Flow\Security\Exception\AuthenticationRequiredException $exception) {
			$this->addFlashMessage('Wrong username or password.');
			throw $exception;
		}
	}

	/**
	 *
	 * @return void
	 */
	public function logoutAction() {
		$this->authenticationManager->logout();
		$this->redirectToUri('/');
	}
}

?>