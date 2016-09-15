<?php
namespace JWeiland\RecommendAPage\Tests\Unit\Utility;

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

use JWeiland\RecommendAPage\Utility\UriResolverUtility;
use PHPUnit\Framework\TestCase;

/**
 * UriResolverUtilityTest
 */
class UriResolverUtilityTest extends TestCase
{
    /**
     * @var UriResolverUtility
     */
    protected $subject = null;
    
    public function setUp() {
        $this->subject = new UriResolverUtility();
    }
    
    public function tearDown()
    {
        unset($this->subject);
    }
    
    /**
     * @test
     * @dataProvider getSpeakingUrlDataProvider
     *
     * @param array $expected
     * @param array $actual
     */
    public function getSpeakingUrlWithStringAsStringWithRemovedLeadingAndTrailingSlashes($expected, $actual)
    {
        $result = $this->subject->getSpeakingUrl($actual);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @test
     * @dataProvider getHttpHostDataProvider
     *
     * @param array $expected
     * @param array $actual
     */
    public function getHttpHostWithStringAsStringWithHttpHost($expected, $actual)
    {
        $result = $this->subject->getHttpHost($actual);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @test
     */
    public function getGetParamsWithStringAsArrayInExpectedValues()
    {
        $expectedArray = array(
            'uid' => 10,
            'l' => 2
        );
        $result = $this->subject->getGetParams('https://www.test.more.often/your/page.php?uid=10&l=2');
        $this->assertEquals($expectedArray, $result);
    }
    
    /**
     * Provides data for getSpeakingUrl()
     *
     * @return array
     */
    public function getSpeakingUrlDataProvider()
    {
        return array(
            array(
                '/',
                'http://www.test.your.page/'
            ),
            array(
                'more/often/',
                'https://w3.test.your.page/more/often?uid=10&l=2'
            )
        );
    }
    
    /**
     * Provides data for getHttpHost()
     *
     * @return array
     */
    public function getHttpHostDataProvider()
    {
        return array(
            array(
                'www.test.your.page',
                'http://www.test.your.page/'
            ),
            array(
                'test.your.page',
                'https://test.your.page/testindex.php?uid=213890'
            )
        );
    }
}