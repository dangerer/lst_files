<?php

namespace GTN\GtnSurvey\Domain\Model;



class Participant extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * company
     *
     * @var string
     */
    protected $company = '';

    /**
     * address
     *
     * @var string
     */
    protected $address = '';

    /**
     * postal
     *
     * @var int
     */
    protected $postal = 0;

    /**
     * city
     *
     * @var string
     */
    protected $city = '';

    /**
     * date
     *
     * @var \DateTime
     */
    protected $date = null;

    /**
     * contactPerson
     *
     * @var string
     */
    protected $contactPerson = '';

    /**
     * mail
     *
     * @var string


     */
    protected $mail = '';

    /**
     * Returns the company
     *
     * @return string $company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Sets the company
     *
     * @param string $company
     * @return void
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Returns the address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the address
     *
     * @param string $address
     * @return void
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns the postal
     *
     * @return int $postal
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * Sets the postal
     *
     * @param int $postal
     * @return void
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the date
     *
     * @return \DateTime $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the date
     *
     * @param \DateTime $date
     * @return void
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Returns the contactPerson
     *
     * @return string $contactPerson
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Sets the contactPerson
     *
     * @param string $contactPerson
     * @return void
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
    }

    /**
     * Returns the mail
     *
     * @return string $mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Sets the mail
     *
     * @param string $mail
     * @return void
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }
}