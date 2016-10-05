<?php
namespace JWeiland\RecommendAPage\Tests\Unit\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use JWeiland\RecommendAPage\Controller\DisplayController;
use JWeiland\RecommendAPage\Domain\Model\RecommendedPage;
use JWeiland\RecommendAPage\Domain\Repository\RecommendedPageRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * DisplayControllerTest
 */
class DisplayControllerTest extends UnitTestCase
{
    /**
     * @var DisplayController
     */
    protected $subject;
    
    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = new DisplayController();
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
        $globalsTSFEBackup = $GLOBALS['TSFE']->id;
        $GLOBALS['TSFE']->id = 0;
        
        $recommendedPages = array(new RecommendedPage(), new RecommendedPage());
        
        /** @var RecommendedPageRepository|\PHPUnit_Framework_MockObject_MockObject $pageRepository */
        $recommendedPageRepository = $this->createMock(RecommendedPageRepository::class);
        $recommendedPageRepository->expects($this->once())
            ->method('findByIdentifier')
            ->with($this->identicalTo((int)$GLOBALS['TSFE']->id))
            ->willReturn($recommendedPages);
        
        $this->subject->injectRecommendedPageRepository($pageRepository);
        
        $this->subject->showAction();
    
        $GLOBALS['TSFE']->id = $globalsTSFEBackup;
    }
}
