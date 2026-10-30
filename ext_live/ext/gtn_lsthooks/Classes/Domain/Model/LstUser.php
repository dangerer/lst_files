<?php
namespace Gtn\GtnLsthooks\Domain\Model;

class LstUser extends \In2code\Femanager\Domain\Model\User {

    /**
     * marketing_newsletter
     *
     * @var bool
     */
    protected $marketingNewsletter;

    /**
     * fe_language
     *
     * @var integer
     */
    protected $feLanguage;

    /**
     * @var string
     */
    protected $customerNumber;

    /**
     * Returns the marketingNewsletter
     *
     * @return bool $marketingNewsletter
     */
    public function getMarketingNewsletter() {
        return $this->marketingNewsletter;
    }

    /**
     * Sets the marketingNewsletter
     *
     * @param bool $marketingNewsletter
     * @return void
     */
    public function setMarketingNewsletter($marketingNewsletter) {
        $this->marketingNewsletter = $marketingNewsletter;
    }

    /**
     * @return int
     */
    public function getFeLanguage()
    {
        return $this->feLanguage;
    }

    /**
     * @param int $feLanguage
     */
    public function setFeLanguage(int $feLanguage)
    {
        $this->feLanguage = $feLanguage;
    }

    /**
     * @return string
     */
    public function getCustomerNumber()
    {
        return $this->customerNumber;
    }

    /**
     * @param string $customerNumber
     */
    public function setCustomerNumber(string $customerNumber)
    {
        $this->customerNumber = $customerNumber;
    }



}