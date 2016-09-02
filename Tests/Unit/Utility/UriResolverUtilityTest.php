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
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * UriResolverUtilityTest
 */
class UriResolverUtilityTest extends UnitTestCase
{
    /**
     * Returns the current page id that was given by piwik
     *
     * @param string $uri
     *
     * @return string
     */
    public function prepareUriForPiwik($uri = '')
    {
        return explode('//', $uri)[1];
    }
    
    /**
     * @test
     */
    public function validateRemoveSchemeFromUrl()
    {
        $utility = new UriResolverUtility();
        $url = 'scheme://www.domain.tld';
    }
}