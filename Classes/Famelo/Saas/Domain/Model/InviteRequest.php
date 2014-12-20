<?php
namespace Famelo\Saas\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Famelo\Saas\Annotations as Saas;
use Famelo\Soul\Domain\Model\AbstractFragment;
use Famelo\Soul\Domain\Model\Soul;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Algorithms;

/**
 * @Flow\Entity
 */
class InviteRequest extends Soul {

    const STATE_WAITING = 'waiting';
    const STATE_REJECTED = 'rejected';
    const STATE_INVITED = 'invited';
    const STATE_USED = 'used';

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $email;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var boolean
     */
    protected $emailVerified = FALSE;

    public function __construct() {
        parent::__construct();
        $this->createdAt = new \DateTime();
        $this->status = self::STATE_WAITING;
    }

    public function __toString() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param stringProvide information about a function parameter. $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return stringProvide information about a function parameter.
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param boolean $emailVerified
     */
    public function setEmailVerified($emailVerified) {
        $this->emailVerified = $emailVerified;
    }

    /**
     * @return boolean
     */
    public function getEmailVerified() {
        return $this->emailVerified;
    }
}