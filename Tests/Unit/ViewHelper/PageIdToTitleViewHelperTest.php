<?php
namespace JWeiland\RecommendAPage\Tests\Unit\ViewHelper;

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

use JWeiland\RecommendAPage\ViewHelpers\PageIdToTitleViewHelper;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * PageIdToTitleViewHelperTest
 */
class PageIdToTitleViewHelperTest extends UnitTestCase
{
    /**
     * @var PageIdToTitleViewHelper
     */
    protected $subject;
    
    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = new PageIdToTitleViewHelper();
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
    public function renderWithNullWillReturnEmptyString()
    {
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects($this->never())->method('get');
        
        $this->assertSame('', $this->subject->render(null));
    }
    
    /**
     * @test
     */
    public function renderWithNumberWillReturnPageTitle()
    {
        $pageId = 0;
        
        /** @var PageRepository|\PHPUnit_Framework_MockObject_MockObject $pageRepository */
        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository->expects($this->once())
            ->method('getPage')
            ->with($pageId)
            ->willReturn(array('title' => 'PageTitle'));
        
        /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects($this->once())
            ->method('get')
            ->with(PageRepository::class)
            ->willReturn($pageRepository);
    
        $this->subject->injectObjectManager($objectManager);
        
        $this->subject->render($pageId);
    }
}
