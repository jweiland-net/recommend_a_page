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
    
    protected $testUri = 'https://www.test.more.often/your/page.php?uid=10&l=2';
    
    public function setUp() {
        $this->subject = new UriResolverUtility();
    }
    
    /**
     * @test
     */
    public function prepareUriForPiwikWithStringInStringWithOnlyHostAndPath()
    {
        $result = $this->subject->prepareUriForPiwik($this->testUri);
        $this->assertEquals('test.more.often/your/page.php', $result);
    }
    
    /**
     * @test
     */
    public function getPagePathWithStringInStringWithRemovedLeadingAndTrailingSlashes()
    {
        $result = $this->subject->getPagePath($this->testUri);
        $this->assertEquals('your/page.php', $result);
    }
    
    /**
     * @test
     */
    public function getGetParamsAsArrayWithStringInArrayOfParameters()
    {
        $expectedArray = array(
            'uid' => 10,
            'l' => 2
        );
        $result = $this->subject->getGetParams($this->testUri);
        $this->assertEquals($expectedArray, $result);
    }
}