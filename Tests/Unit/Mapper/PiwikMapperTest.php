<?php
namespace JWeiland\RecommendAPage\Tests\Unit\Mapper;

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
        $uriMapper->expects($this->never())->method('getTypo3PidFromUri');
        $this->subject->injectUriMapper($uriMapper);
        $this->assertSame(
            array(),
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
        $uriMapper->expects($this->never())->method('getTypo3PidFromUri');
        $this->assertSame(
            array(),
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
        $uriMapper->expects($this->never())->method('getTypo3PidFromUri');
        $this->assertSame(
            array(),
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
        $uriMapper->expects($this->never())->method('getTypo3PidFromUri');
        $this->assertSame(
            array(),
            $this->subject->mapPiwikPidsToTypo3Pids(array())
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
        
        $piwikPages = array();
        
        $pagesDoNotRecommend = array(0, 1, 1, 0, 1);
        $pagesHidden = array(1, 0, 1, 0, 0);
        $pagesDeleted = array(0, 1, 0, 0, 0);
        
        for ($i = 0; $i < 5; $i++) {
            $piwikPage = array();
            $piwikPage['idaction'] = $i;
            $piwikPage['name'] = 'test' . $i;
            $piwikPages[] = $piwikPage;
            
            $uriMapper->expects($this->at($i))->method('getTypo3PidFromUri')->with('test' . $i)->willReturn($i);
            
            $pageRepository->expects($this->at($i))
                ->method('getPage')
                ->with($i)
                ->willReturn(array(
                    'tx_recommend_a_page_do_not_recommend' => $pagesDoNotRecommend[$i],
                    'hidden' => $pagesHidden[$i],
                    'deleted' => $pagesDeleted[$i]
                ));
        }
        
        $this->subject->injectUriMapper($uriMapper);
        $this->subject->injectPageRepository($pageRepository);
        
        $expectedResult = array(
            3 => 3,
        );
        
        $this->assertSame(
            $expectedResult,
            $this->subject->mapPiwikPidsToTypo3Pids($piwikPages)
        );
    }
}