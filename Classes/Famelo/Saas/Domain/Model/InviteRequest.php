<?php
namespace Famelo\Saas\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use Famelo\Saas\Annotations as Saas;
use Famelo\Common\Annotations as Common;

/**
 * @Flow\Entity
 */
class InviteRequest {
    const STATE_WAITING = 'waiting';
    const STATE_REJECTED = 'rejected';
    const STATE_INVITED = 'invited';
    const STATE_USED = 'used';

    /**
     * @var string
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
     * @var integer
     * @Saas\GenerateValue(generator="integer", start="1", increment="1")
     */
    protected $number;

    /**
     * @var string
     */
    protected $inviteToken = '';

    public function __construct() {
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
     * @param integer $number
     */
    public function setNumber($number) {
        $this->number = $number;
    }

    /**
     * @return integer
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param string $inviteToken
     */
    public function setInviteToken($inviteToken) {
        $this->inviteToken = $inviteToken;
    }

    /**
     * @return string
     */
    public function getInviteToken() {
        return $this->inviteToken;
    }
}