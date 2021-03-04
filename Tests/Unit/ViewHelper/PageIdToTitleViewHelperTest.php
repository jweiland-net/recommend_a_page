<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Tests\Unit\ViewHelper;

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
        $objectManager->expects(self::never())->method('get');

        self::assertSame('', $this->subject->render(null));
    }

    /**
     * @test
     */
    public function renderWithNumberWillReturnPageTitle()
    {
        $pageId = 0;

        /** @var PageRepository|\PHPUnit_Framework_MockObject_MockObject $pageRepository */
        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository->expects(self::once())
            ->method('getPage')
            ->with($pageId)
            ->willReturn(['title' => 'PageTitle']);

        /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects(self::once())
            ->method('get')
            ->with(PageRepository::class)
            ->willReturn($pageRepository);

        $this->subject->injectObjectManager($objectManager);

        $this->subject->render($pageId);
    }
}
