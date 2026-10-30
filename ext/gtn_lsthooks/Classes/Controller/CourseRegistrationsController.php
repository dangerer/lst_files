<?php

declare(strict_types=1);

namespace Gtn\GtnLsthooks\Controller;

use Gtn\GtnLsthooks\Domain\Repository\CourseRegistrationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;

final class CourseRegistrationsController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly CourseRegistrationRepository $registrationRepository
    ) {
    }

    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $view->setTitle('Schulungsanmeldungen');
        $view->assign('courses', $this->registrationRepository->findOverview());
        return $view->renderResponse('CourseRegistrations/Index');
    }
}
