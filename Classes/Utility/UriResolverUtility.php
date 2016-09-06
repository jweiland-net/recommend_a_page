<?php
namespace JWeiland\RecommendAPage\Utility;

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

use DmitryDulepov\Realurl\Cache\UrlCacheEntry;
use DmitryDulepov\Realurl\Configuration\ConfigurationReader;
use DmitryDulepov\Realurl\Utility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * UriResolverUtility
 */
class UriResolverUtility
{
    /**
     * Returns the current page id that was given by piwik
     *
     * @param string $uri
     *
     * @return string
     */
    public function prepareUriForPiwik($uri)
    {
        $uriArray = parse_url($uri);
        $uriHostArray = array();
    
        preg_match('~([w0-9]+\.)?(?P<domain>[[:alnum:]\-\.]+)~', $uriArray['host'], $uriHostArray);
    
        return $uriHostArray['domain'] . $uriArray['path'];
    }
    
    /**
     * Returns realurl like speaking url
     *
     * @param string $uri
     *
     * @return string
     */
    public function getSpeakingUrl($uri)
    {
        if (!strpos($uri, '://')) {
            $uri = 'http://' . $uri;
        }
        
        $path = parse_url($uri)['path'];
        if ($path !== '/') {
            $path = trim($path, '/');
            $path .= '/';
        }
        return $path;
    }
    
    /**
     * Extracts get params from an uri
     *
     * @param $uri
     *
     * @return array
     */
    public function getGetParams($uri)
    {
        $query = parse_url($uri)['query'];
        $query = explode('&', $query);
        $query = array_filter($query);
        $params = array();
        foreach ($query as $key => $value) {
            list($k, $v) = explode('=', $value);
            $params[$k] = $v;
        }
        return $params;
    }
    
    /**
     * @param string $uri
     *
     * @return int
     */
    public function getTYPO3PidFromUri($uri)
    {
        if (
            !empty($uri) &&
            ExtensionManagementUtility::isLoaded('realurl') &&
            !preg_match('~index.php~', $uri)
        ) {
            /** @var ConfigurationReader $realUrlConfiguration */
            $realUrlConfiguration = GeneralUtility::makeInstance(
                ConfigurationReader::class,
                ConfigurationReader::MODE_DECODE
            );
        
            /** @var Utility $realUrlUtility */
            $realUrlUtility = GeneralUtility::makeInstance(Utility::class, $realUrlConfiguration);
        
            $rootPageId = (int)$realUrlConfiguration->get('pagePath/rootpage_id');
            /** @var UrlCacheEntry $convertedUrl */
            $convertedUrl = $realUrlUtility->getCache()->getUrlFromCacheBySpeakingUrl(
                $rootPageId,
                $this->getSpeakingUrl($uri),
                (int)GeneralUtility::_GET('L')
            );
            $pid = $convertedUrl->getPageId();
        } else {
            $pid = $this->getGetParams($uri)['id'];
        }
        
        return $pid;
    }
}