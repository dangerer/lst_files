<?php
namespace GTN\GtnSurvey\Tests\Unit\Controller;

/**
 * Test case.
 */
class SurveyControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \GTN\GtnSurvey\Controller\SurveyController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\GTN\GtnSurvey\Controller\SurveyController::class)
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
    public function listActionFetchesAllSurveysFromRepositoryAndAssignsThemToView()
    {

        $allSurveys = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $surveyRepository = $this->getMockBuilder(\GTN\GtnSurvey\Domain\Repository\SurveyRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $surveyRepository->expects(self::once())->method('findAll')->will(self::returnValue($allSurveys));
        $this->inject($this->subject, 'surveyRepository', $surveyRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('surveys', $allSurveys);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenSurveyToView()
    {
        $survey = new \GTN\GtnSurvey\Domain\Model\Survey();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('survey', $survey);

        $this->subject->showAction($survey);
    }
}
