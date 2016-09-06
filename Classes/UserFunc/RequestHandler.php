<?php
namespace JWeiland\RecommendAPage\UserFunc;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * RequestHandler
 */
class RequestHandler
{
    /**
     * Converts the refUri to a pid
     *
     * @return int|null
     */
    public function convertRefUriToPid()
    {
        $referrerUri = GeneralUtility::getIndpEnv('HTTP_REFERER');
        
        /** @var UriResolverUtility $uriResolverUtility */
        $uriResolverUtility = GeneralUtility::makeInstance(UriResolverUtility::class);
        
        return $uriResolverUtility->getTYPO3PidFromUri($referrerUri);
    }
}