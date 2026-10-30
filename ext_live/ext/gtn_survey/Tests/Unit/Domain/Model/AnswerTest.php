<?php
namespace GTN\GtnSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class AnswerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \GTN\GtnSurvey\Domain\Model\Answer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \GTN\GtnSurvey\Domain\Model\Answer();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTextReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getText()
        );
    }

    /**
     * @test
     */
    public function setTextForStringSetsText()
    {
        $this->subject->setText('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'text',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getValueReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getValue()
        );
    }

    /**
     * @test
     */
    public function setValueForIntSetsValue()
    {
        $this->subject->setValue(12);

        self::assertAttributeEquals(
            12,
            'value',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSectionNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSectionName()
        );
    }

    /**
     * @test
     */
    public function setSectionNameForStringSetsSectionName()
    {
        $this->subject->setSectionName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'sectionName',
            $this->subject
        );
    }
}
