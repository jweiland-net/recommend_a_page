<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Tests\Unit\Mapper;

use DmitryDulepov\Realurl\Cache\CacheInterface;
use DmitryDulepov\Realurl\Cache\DatabaseCache;
use DmitryDulepov\Realurl\Cache\UrlCacheEntry;
use DmitryDulepov\Realurl\Configuration\ConfigurationReader;
use JWeiland\RecommendAPage\Mapper\UriMapper;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * UriMapperTest
 */
class UriMapperTest extends UnitTestCase
{
    /**
     * @var string
     */
    protected $httpHostBackup = '';

    /**
     * @var UriMapper
     */
    protected $subject;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = new UriMapper();
        $this->httpHostBackup = $_SERVER['HTTP_HOST'];
        $_SERVER['HTTP_HOST'] = 'jweiland.net';
    }

    /**
     * TearDown
     */
    public function tearDown()
    {
        unset($this->subject);
        $_SERVER['HTTP_HOST'] = $this->httpHostBackup;
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithInvalidStringReturnsGivenString()
    {
        self::assertSame(
            '/',
            $this->subject->getSpeakingUrl('test123')
        );
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithIntegerReturnsSlash()
    {
        self::assertSame(
            '/',
            $this->subject->getSpeakingUrl(12345)
        );
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithValidHostReturnsSlash()
    {
        self::assertSame(
            '/',
            $this->subject->getSpeakingUrl('jweiland.net')
        );
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithValidHostAndSchemaReturnsSlash()
    {
        self::assertSame(
            '/',
            $this->subject->getSpeakingUrl('http://www.jweiland.net')
        );
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithValidUrlReturnsSlash()
    {
        self::assertSame(
            '/',
            $this->subject->getSpeakingUrl('http://www.jweiland.net/?id=123&tx_news[id]=231&L=2')
        );
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithSpeakingUrlAndWithoutTrailingHtmlReturnsPath()
    {
        self::assertSame(
            'home/archive/2016/09/23/welcome/',
            $this->subject->getSpeakingUrl('http://www.jweiland.net/home/archive/2016/09/23/welcome/')
        );
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithDottedSpeakingUrlReturnsPath()
    {
        self::assertSame(
            'home/jochen.weiland/agb.html',
            $this->subject->getSpeakingUrl('http://www.jweiland.net/home/jochen.weiland/agb.html')
        );
    }

    /**
     * @test
     */
    public function getSpeakingUrlWithSpeakingUrlAndWithTrailingHtmlReturnsPath()
    {
        self::assertSame(
            'home/archive/2016/09/23/welcome.html',
            $this->subject->getSpeakingUrl('http://www.jweiland.net/home/archive/2016/09/23/welcome.html')
        );
    }

    /**
     * @test
     */
    public function getHttpHostWithHostReturnsHost()
    {
        self::assertSame(
            'test123',
            $this->subject->getHttpHost('test123')
        );
    }

    /**
     * @test
     */
    public function getHttpHostWithIntegerReturnsIntegerAsHost()
    {
        self::assertSame(
            '12345',
            $this->subject->getHttpHost(12345)
        );
    }

    /**
     * @test
     */
    public function getHttpHostWithDomainReturnsHost() {
        self::assertSame(
            'jweiland.net',
            $this->subject->getHttpHost('jweiland.net')
        );
    }

    /**
     * @test
     */
    public function getHttpHostWithUriReturnsHost()
    {
        self::assertSame(
            'www.jweiland.net',
            $this->subject->getHttpHost('http://www.jweiland.net/?id=2&L=2')
        );
    }

    /**
     * @test
     */
    public function getTypo3PidFromUriWithEmptyUriReturnsNull()
    {
        self::assertNull(
            $this->subject->getTypo3PidFromUri('')
        );
    }

    /**
     * @test
     */
    public function getTypo3PidFromUriWithInvalidHostReturnsNull()
    {
        self::assertNull(
            $this->subject->getTypo3PidFromUri('http://test123.net')
        );
    }

    /**
     * @test
     */
    public function getTypo3PidFromUriWithNonRealUrlCallsGetUidFromUriReturnsPid()
    {
        $this->subject = new UriMapper();
        self::assertSame(
            123,
            $this->subject->getTypo3PidFromUri('http://jweiland.net/?id=123')
        );
    }

    /**
     * @test
     */
    public function getTypo3PidFromUriWithRealUrlCallsGetPidFromRealUrlApiReturnsPid()
    {
        if (ExtensionManagementUtility::isLoaded('realurl')) {
            /** @var ConfigurationReader|\PHPUnit_Framework_MockObject_MockObject $configurationReader */
            $configurationReader = $this->createMock(ConfigurationReader::class);
            $configurationReader->expects($this->once())->method('get')->with(
                $this->equalTo('pagePath/rootpage_id')
            )->willReturn(23);

            /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject $objectManager */
            $objectManager = $this->createMock(ObjectManager::class);
            $objectManager->expects($this->once())->method('get')->with(
                $this->equalTo(ConfigurationReader::class),
                $this->equalTo(ConfigurationReader::MODE_DECODE)
            )->willReturn($configurationReader);

            /** @var UrlCacheEntry|\PHPUnit_Framework_MockObject_MockObject $urlCacheEntry */
            $urlCacheEntry = $this->createMock(UrlCacheEntry::class);
            $urlCacheEntry
                ->expects($this->once())
                ->method('getPageId')
                ->willReturn(123);

            /** @var CacheInterface|\PHPUnit_Framework_MockObject_MockObject $realUrlCache */
            $realUrlCache = $this->createMock(DatabaseCache::class);
            $realUrlCache
                ->expects($this->once())
                ->method('getUrlFromCacheBySpeakingUrl')
                ->with(
                    $this->logicalAnd(
                        $this->equalTo(23),
                        $this->isType('int')
                    ),
                    $this->equalTo('home/agb.html'),
                    $this->logicalAnd(
                        $this->equalTo(0),
                        $this->isType('int')
                    )
                )->willReturn($urlCacheEntry);

            /** @var UriMapper|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface $subject */
            $subject = $this->getAccessibleMock(UriMapper::class, ['dummy']);
            $subject->injectObjectManager($objectManager);
            $subject->_set('realUrlCache', $realUrlCache);
            self::assertSame(
                123,
                $subject->getTypo3PidFromUri('http://jweiland.net/home/agb.html')
            );
        }
    }
}
