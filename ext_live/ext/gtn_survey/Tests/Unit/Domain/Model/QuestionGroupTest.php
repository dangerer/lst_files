<?php
namespace GTN\GtnSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class QuestionGroupTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \GTN\GtnSurvey\Domain\Model\QuestionGroup
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \GTN\GtnSurvey\Domain\Model\QuestionGroup();
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
    public function getGroupTypeReturnsInitialValueForGroupType()
    {
        self::assertEquals(
            null,
            $this->subject->getGroupType()
        );
    }

    /**
     * @test
     */
    public function setGroupTypeForGroupTypeSetsGroupType()
    {
        $groupTypeFixture = new \GTN\GtnSurvey\Domain\Model\GroupType();
        $this->subject->setGroupType($groupTypeFixture);

        self::assertAttributeEquals(
            $groupTypeFixture,
            'groupType',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getQuestionsReturnsInitialValueForQuestion()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getQuestions()
        );
    }

    /**
     * @test
     */
    public function setQuestionsForObjectStorageContainingQuestionSetsQuestions()
    {
        $question = new \GTN\GtnSurvey\Domain\Model\Question();
        $objectStorageHoldingExactlyOneQuestions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneQuestions->attach($question);
        $this->subject->setQuestions($objectStorageHoldingExactlyOneQuestions);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneQuestions,
            'questions',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addQuestionToObjectStorageHoldingQuestions()
    {
        $question = new \GTN\GtnSurvey\Domain\Model\Question();
        $questionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $questionsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($question));
        $this->inject($this->subject, 'questions', $questionsObjectStorageMock);

        $this->subject->addQuestion($question);
    }

    /**
     * @test
     */
    public function removeQuestionFromObjectStorageHoldingQuestions()
    {
        $question = new \GTN\GtnSurvey\Domain\Model\Question();
        $questionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $questionsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($question));
        $this->inject($this->subject, 'questions', $questionsObjectStorageMock);

        $this->subject->removeQuestion($question);
    }

    /**
     * @test
     */
    public function getSectionsReturnsInitialValueForSection()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getSections()
        );
    }

    /**
     * @test
     */
    public function setSectionsForObjectStorageContainingSectionSetsSections()
    {
        $section = new \GTN\GtnSurvey\Domain\Model\Section();
        $objectStorageHoldingExactlyOneSections = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneSections->attach($section);
        $this->subject->setSections($objectStorageHoldingExactlyOneSections);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneSections,
            'sections',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addSectionToObjectStorageHoldingSections()
    {
        $section = new \GTN\GtnSurvey\Domain\Model\Section();
        $sectionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $sectionsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($section));
        $this->inject($this->subject, 'sections', $sectionsObjectStorageMock);

        $this->subject->addSection($section);
    }

    /**
     * @test
     */
    public function removeSectionFromObjectStorageHoldingSections()
    {
        $section = new \GTN\GtnSurvey\Domain\Model\Section();
        $sectionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $sectionsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($section));
        $this->inject($this->subject, 'sections', $sectionsObjectStorageMock);

        $this->subject->removeSection($section);
    }
}
