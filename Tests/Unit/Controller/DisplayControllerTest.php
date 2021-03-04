<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Tests\Unit\Controller;

use JWeiland\RecommendAPage\Controller\DisplayController;
use JWeiland\RecommendAPage\Domain\Model\RecommendedPage;
use JWeiland\RecommendAPage\Domain\Repository\RecommendedPageRepository;
use TYPO3\CMS\Core\Tests\AccessibleObjectInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Fluid\View\TemplateView;

/**
 * DisplayControllerTest
 */
class DisplayControllerTest extends UnitTestCase
{
    /**
     * @var DisplayController|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $subject;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(DisplayController::class, ['dummy']);
    }

    /**
     * TearDown
     */
    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function showActionWillFindRecommendedPagesAndAssignThemToView()
    {
        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->id = 0;

        $recommendedPages = [new RecommendedPage(), new RecommendedPage()];

        /** @var TemplateView|\PHPUnit_Framework_MockObject_MockObject $view $view */
        $view = $this->createMock(TemplateView::class);

        $view->expects(self::once())
            ->method('assign')
            ->with('recommendations', $recommendedPages);

        $this->subject->_set('view', $view);

        /** @var RecommendedPageRepository|\PHPUnit_Framework_MockObject_MockObject $recommendedPageRepository */
        $recommendedPageRepository = $this->createMock(RecommendedPageRepository::class);
        $recommendedPageRepository->expects(self::once())
            ->method('__call')
            ->with(
                $this->identicalTo('findByReferrerPid'),
                $this->identicalTo([(int)$GLOBALS['TSFE']->id])
            )
            ->willReturn($recommendedPages);

        $this->subject->injectRecommendedPageRepository($recommendedPageRepository);

        $this->subject->showAction();
    }
}
