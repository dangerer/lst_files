<?php
namespace GTN\GtnSurvey\Tests\Unit\Controller;

/**
 * Test case.
 */
class QuestionGroupControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \GTN\GtnSurvey\Controller\QuestionGroupController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\GTN\GtnSurvey\Controller\QuestionGroupController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllQuestionGroupsFromRepositoryAndAssignsThemToView()
    {

        $allQuestionGroups = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionGroupRepository = $this->getMockBuilder(\::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $questionGroupRepository->expects(self::once())->method('findAll')->will(self::returnValue($allQuestionGroups));
        $this->inject($this->subject, 'questionGroupRepository', $questionGroupRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('questionGroups', $allQuestionGroups);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenQuestionGroupToView()
    {
        $questionGroup = new \GTN\GtnSurvey\Domain\Model\QuestionGroup();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('questionGroup', $questionGroup);

        $this->subject->showAction($questionGroup);
    }
}
