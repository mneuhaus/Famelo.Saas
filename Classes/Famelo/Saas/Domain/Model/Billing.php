<?php
namespace Famelo\Saas\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use Famelo\Common\Annotations as Common;

/**
 * @Flow\Entity
 */
class Billing {

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     */
    protected $firstName;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     */
    protected $lastName;

    /**
     * @var string
     *
     */
    protected $company;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     */
    protected $address;

    /**
     * @var string
     *
     */
    protected $address2;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     */
    protected $city;

    /**
     * @var string
     *
     */
    protected $state;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     *
     */
    protected $zip;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     *
     */
    protected $country;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     *
     */
    protected $email;

    /**
     * @var \Famelo\Saas\Domain\Model\Plan
     * @ORM\OneToOne(mappedBy="billing")
     */
    protected $plan;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty")
     *
     */
    protected $phone;

    /**
     * @var string
     *
     */
    protected $fax;

    /**
     * Gets address2.
     *
     * @return string $address2
     */
    public function getAddress2() {
        return $this->address2;
    }

    /**
     * Sets the address2.
     *
     * @param string $address2
     */
    public function setAddress2($address2) {
        $this->address2 = $address2;
    }

    /**
     * Gets address.
     *
     * @return string $address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Sets the address.
     *
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * Gets city.
     *
     * @return string $city
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Sets the city.
     *
     * @param string $city
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * Gets company.
     *
     * @return string $company
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     * Sets the company.
     *
     * @param string $company
     */
    public function setCompany($company) {
        $this->company = $company;
    }

    /**
     * Gets country.
     *
     * @return string $country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * Sets the country.
     *
     * @param string $country
     */
    public function setCountry($country) {
        $this->country = $country;
    }

    /**
     * Gets email.
     *
     * @return string $email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Gets fax.
     *
     * @return string $fax
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * Sets the fax.
     *
     * @param string $fax
     */
    public function setFax($fax) {
        $this->fax = $fax;
    }

    /**
     * Gets firstName.
     *
     * @return string $firstName
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Sets the firstName.
     *
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    /**
     * Gets lastName.
     *
     * @return string $lastName
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Sets the lastName.
     *
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    /**
     * Gets phone.
     *
     * @return string $phone
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Sets the phone.
     *
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    /**
     * Gets plan.
     *
     * @return \Famelo\Saas\Domain\Model\Plan $plan
     */
    public function getPlan() {
        return $this->plan;
    }

    /**
     * Sets the plan.
     *
     * @param \Famelo\Saas\Domain\Model\Plan $plan
     */
    public function setPlan($plan) {
        $this->plan = $plan;
    }

    /**
     * Gets state.
     *
     * @return string $state
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Sets the state.
     *
     * @param string $state
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * Gets zip.
     *
     * @return string $zip
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     * Sets the zip.
     *
     * @param string $zip
     */
    public function setZip($zip) {
        $this->zip = $zip;
    }

}