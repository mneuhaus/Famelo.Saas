<?php
namespace Famelo\Saas\Services;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class NotificationService {

	/**
	 * @Flow\Inject(setting="Famelo.Saas.AdminEmail")
	 * @var string
	 */
	protected $adminEmail;

	public function notifyAdmin($subject, $message, $context = array()) {
		var_dump($this->adminEmail);
	}

	public function FunctionName() {
		$mail = new \Famelo\Messaging\Message();
		$mail->setFrom(array('mail@me.com' => 'Me :)'))
			->setTo(array('mail@you.com'))
			->setSubject('How are you?')
			->setMessage('My.Package:HelloWorld')
			->assign('someVariable', 'Hello World')
			->send();
	}
}
?>