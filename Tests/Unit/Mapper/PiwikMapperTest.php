<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Tests\Unit\Mapper;

use JWeiland\RecommendAPage\Mapper\PiwikMapper;
use JWeiland\RecommendAPage\Mapper\UriMapper;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * UriResolverUtilityTest
 */
class PiwikMapperTest extends UnitTestCase
{
    /**
     * @var PiwikMapper
     */
    protected $subject;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = new PiwikMapper();
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
    public function mapPiwikPidsToTypo3PidsWithNullReturnsEmptyArray()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|UriMapper $uriMapper */
        $uriMapper = $this->createMock(UriMapper::class);
        $uriMapper->expects(self::never())->method('getTypo3PidFromUri');
        $this->subject->injectUriMapper($uriMapper);
        self::assertSame(
            [],
            $this->subject->mapPiwikPidsToTypo3Pids(null)
        );
    }

    /**
     * @test
     */
    public function mapPiwikPidsToTypo3PidsWithIntegerReturnsEmptyArray()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|UriMapper $uriMapper */
        $uriMapper = $this->createMock(UriMapper::class);
        $uriMapper->expects(self::never())->method('getTypo3PidFromUri');
        self::assertSame(
            [],
            $this->subject->mapPiwikPidsToTypo3Pids(12345)
        );
    }

    /**
     * @test
     */
    public function mapPiwikPidsToTypo3PidsWithStringReturnsEmptyArray()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|UriMapper $uriMapper */
        $uriMapper = $this->createMock(UriMapper::class);
        $uriMapper->expects(self::never())->method('getTypo3PidFromUri');
        self::assertSame(
            [],
            $this->subject->mapPiwikPidsToTypo3Pids('test123')
        );
    }

    /**
     * @test
     */
    public function mapPiwikPidsToTypo3PidsWithEmptyArrayReturnsEmptyArray()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|UriMapper $uriMapper */
        $uriMapper = $this->createMock(UriMapper::class);
        $uriMapper->expects(self::never())->method('getTypo3PidFromUri');
        self::assertSame(
            [],
            $this->subject->mapPiwikPidsToTypo3Pids([])
        );
    }

    /**
     * @test
     */
    public function mapPiwikPidsToTypo3PidsWithPiwikPagesReturnsTypo3Pages()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|UriMapper $uriMapper */
        $uriMapper = $this->createMock(UriMapper::class);

        /** @var \PHPUnit_Framework_MockObject_MockObject|PageRepository $pageRepository */
        $pageRepository = $this->createMock(PageRepository::class);

        $piwikPages = [];

        $pagesDoNotRecommend = [0, 1, 1, 0, 1];
        $pagesHidden = [1, 0, 1, 0, 0];
        $pagesDeleted = [0, 1, 0, 0, 0];

        for ($i = 0; $i < 5; $i++) {
            $piwikPage = [];
            $piwikPage['idaction'] = $i;
            $piwikPage['name'] = 'test' . $i;
            $piwikPages[] = $piwikPage;

            $uriMapper->expects($this->at($i))->method('getTypo3PidFromUri')->with('test' . $i)->willReturn($i);

            $pageRepository->expects($this->at($i))
                ->method('getPage')
                ->with($i)
                ->willReturn([
                    'tx_recommend_a_page_do_not_recommend' => $pagesDoNotRecommend[$i],
                    'hidden' => $pagesHidden[$i],
                    'deleted' => $pagesDeleted[$i]
                ]);
        }

        $this->subject->injectUriMapper($uriMapper);
        $this->subject->injectPageRepository($pageRepository);

        $expectedResult = [
            3 => 3,
        ];

        self::assertSame(
            $expectedResult,
            $this->subject->mapPiwikPidsToTypo3Pids($piwikPages)
        );
    }
}
