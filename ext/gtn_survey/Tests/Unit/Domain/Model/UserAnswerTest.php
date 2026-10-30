<?php
namespace GTN\GtnSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class UserAnswerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \GTN\GtnSurvey\Domain\Model\UserAnswer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \GTN\GtnSurvey\Domain\Model\UserAnswer();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getFeUserReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getFeUser()
        );
    }

    /**
     * @test
     */
    public function setFeUserForIntSetsFeUser()
    {
        $this->subject->setFeUser(12);

        self::assertAttributeEquals(
            12,
            'feUser',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAnswerReturnsInitialValueForAnswer()
    {
        self::assertEquals(
            null,
            $this->subject->getAnswer()
        );
    }

    /**
     * @test
     */
    public function setAnswerForAnswerSetsAnswer()
    {
        $answerFixture = new \GTN\GtnSurvey\Domain\Model\Answer();
        $this->subject->setAnswer($answerFixture);

        self::assertAttributeEquals(
            $answerFixture,
            'answer',
            $this->subject
        );
    }
}
