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

use DmitryDulepov\Realurl\Configuration\ConfigurationReader;
use DmitryDulepov\Realurl\Utility;
use JWeiland\RecommendAPage\Utility\UriResolverUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * RequestHandler
 */
class RequestHandler
{
    /**
     * Converts the refUri to a pid
     *
     * @param string $content
     * @param array $conf
     *
     * @return int|null
     */
    public function convertRefUriToPid($content, $conf)
    {
        $referrerUri = GeneralUtility::getIndpEnv('HTTP_REFERER');
        
        /** @var UriResolverUtility $uriResolverUtility */
        $uriResolverUtility = GeneralUtility::makeInstance(UriResolverUtility::class);
        if (
            !empty($referrerUri) &&
            ExtensionManagementUtility::isLoaded('realurl')
        ) {
            /** @var ConfigurationReader $realUrlConfiguration */
            $realUrlConfiguration = GeneralUtility::makeInstance(
                ConfigurationReader::class,
                ConfigurationReader::MODE_DECODE
            );
            
            /** @var Utility $realUrlUtility */
            $realUrlUtility = GeneralUtility::makeInstance(Utility::class, $realUrlConfiguration);
            
            $rootPageId = (int)$realUrlConfiguration->get('pagePath/rootpage_id');
            $convertedUrl = $realUrlUtility->getCache()->getUrlFromCacheBySpeakingUrl(
                $rootPageId,
                $uriResolverUtility->getPagePath($referrerUri),
                (int)GeneralUtility::_GET('L')
            );
            $pid = $convertedUrl['pageid'];
        } else {
            $pid = $uriResolverUtility->getGetParams($referrerUri)['id'];
        }
        return $pid;
    }
}