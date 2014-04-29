<?php
namespace Famelo\Saas\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Form\Core\Model\FormDefinition;
use TYPO3\Flow\Error\Message;

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
	 * @var \Famelo\Saas\Domain\Repository\UserRepository
	 * @Flow\Inject
	 */
	protected $userRepository;

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
	 *
	 *
	 * @return string
	 */
	public function indexAction() {
		if ($this->authenticationManager->getSecurityContext()->getAccount() !== NULL) {
			$this->redirectToUri('/de/mein-konto.html');
		}

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
			$this->redirectToUri('/de/mein-konto.html');
		} catch (\TYPO3\Flow\Security\Exception\AuthenticationRequiredException $exception) {
			$this->addFlashMessage('Wrong username or password.', '', Message::SEVERITY_ERROR);
			$this->forward('index');
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

	public function newPasswordAction() {

	}

	/**
	 * @param string $email
	 */
	public function sendConfirmationAction($email) {
		$user = $this->userRepository->findOneByEmail($email);
		$timestamp = substr(sha1(date('d.m.Y H')), 0, 8);
		$resetToken = $this->hashService->appendHmac($timestamp);
		$user->setResetToken($resetToken);
		$this->userRepository->update($user);

		$resetUri = $uri = $this->uriBuilder->setCreateAbsoluteUri(TRUE)->uriFor('resetPassword', array('token' => $resetToken));
        $mail = new \Famelo\Messaging\Message();
        $mail->setMessage('Famelo.Saas:ResetPassword')
            	->assign('user', $user)
            	->assign('resetUri', $resetUri)
            	->send();
	}

	/**
	 * @param string $token
	 * @param string $password
	 * @param string $confirmation
	 */
	public function resetPasswordAction($token, $password = NULL, $confirmation = NULL) {
		$timestamp = substr(sha1(date('d.m.Y H')), 0, 8);
		$tokenTime = $this->hashService->validateAndStripHmac($token);
		$user = $this->userRepository->findOneByResetToken($token);
		if ($timestamp !== $tokenTime || $user === FALSE) {
			$this->redirect('invalidToken');
		}
		if ($password !== NULL){
			if ($password === $confirmation) {
				$user->getAccount()->setCredentialsSource($this->hashService->hashPassword($password));
				$this->accountRepository->update($user->getAccount());
				$this->addFlashMessage('Your password has been set.');
				$this->persistenceManager->persistAll();
				$this->redirect('index');
			} else {
				$this->view->assign('not-matching', TRUE);
			}
		}
		$this->view->assign('token', $token);
	}

	public function invalidTokenAction() {

	}
}

?>