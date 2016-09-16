<?php
namespace JWeiland\RecommendAPage\Mapper;

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

use DmitryDulepov\Realurl\Cache\CacheFactory;
use DmitryDulepov\Realurl\Cache\UrlCacheEntry;
use DmitryDulepov\Realurl\Configuration\ConfigurationReader;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * UriMapper
 */
class UriMapper
{
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
     * Returns http host
     *
     * @param string $uri
     *
     * @return string
     */
    public function getHttpHost($uri)
    {
        if (!strpos($uri, '://')) {
            $uri = 'http://' . $uri;
        }
        
        $host = parse_url($uri)['host'];
        
        return $host;
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
        parse_str($query, $result);
        return $result;
    }
    
    /**
     * Returns the PID from the URI by either reverse realurl or from params
     *
     * @param string $uri
     *
     * @return int|null
     */
    public function getTYPO3PidFromUri($uri)
    {
        if ($this->getHttpHost($uri) != GeneralUtility::getIndpEnv('HTTP_HOST')) {
            return null;
        }
        
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
            
            $rootPageId = (int)$realUrlConfiguration->get('pagePath/rootpage_id');
            
            /** @var UrlCacheEntry $convertedUrl */
            $convertedUrl = CacheFactory::getCache()->getUrlFromCacheBySpeakingUrl(
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