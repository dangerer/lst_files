<?php
namespace GTN\GtnSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class SurveyTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \GTN\GtnSurvey\Domain\Model\Survey
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \GTN\GtnSurvey\Domain\Model\Survey();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getQuestionGroupsReturnsInitialValueForQuestionGroup()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getQuestionGroups()
        );
    }

    /**
     * @test
     */
    public function setQuestionGroupsForObjectStorageContainingQuestionGroupSetsQuestionGroups()
    {
        $questionGroup = new \GTN\GtnSurvey\Domain\Model\QuestionGroup();
        $objectStorageHoldingExactlyOneQuestionGroups = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneQuestionGroups->attach($questionGroup);
        $this->subject->setQuestionGroups($objectStorageHoldingExactlyOneQuestionGroups);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneQuestionGroups,
            'questionGroups',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addQuestionGroupToObjectStorageHoldingQuestionGroups()
    {
        $questionGroup = new \GTN\GtnSurvey\Domain\Model\QuestionGroup();
        $questionGroupsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $questionGroupsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($questionGroup));
        $this->inject($this->subject, 'questionGroups', $questionGroupsObjectStorageMock);

        $this->subject->addQuestionGroup($questionGroup);
    }

    /**
     * @test
     */
    public function removeQuestionGroupFromObjectStorageHoldingQuestionGroups()
    {
        $questionGroup = new \GTN\GtnSurvey\Domain\Model\QuestionGroup();
        $questionGroupsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $questionGroupsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($questionGroup));
        $this->inject($this->subject, 'questionGroups', $questionGroupsObjectStorageMock);

        $this->subject->removeQuestionGroup($questionGroup);
    }
}
