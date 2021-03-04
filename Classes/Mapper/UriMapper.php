<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Mapper;

use DmitryDulepov\Realurl\Cache\CacheFactory;
use DmitryDulepov\Realurl\Cache\CacheInterface;
use DmitryDulepov\Realurl\Cache\UrlCacheEntry;
use DmitryDulepov\Realurl\Configuration\ConfigurationReader;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * UriMapper
 */
class UriMapper
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var CacheInterface
     */
    protected $realUrlCache;

    /**
     * inject objectManager
     *
     * @param ObjectManager $objectManager
     */
    public function injectObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
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
        $path = (string)parse_url(
            $this->sanitizeUri($uri),
            PHP_URL_PATH
        );
        $path = trim($path, '/') . '/';
        $pathParts = pathinfo($path);
        if (isset($pathParts['extension'])) {
            $path = rtrim($path, '/');
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
        return parse_url($this->sanitizeUri($uri))['host'];
    }

    /**
     * Sanitize URI
     *
     * @param string $uri
     *
     * @return string
     */
    protected function sanitizeUri($uri)
    {
        if (!strpos($uri, '://')) {
            $uri = 'http://' . $uri;
        }
        return $uri;
    }

    /**
     * Extracts get params from an uri
     *
     * @param string $uri
     *
     * @return int|null
     */
    protected function getUidFromUri($uri)
    {
        $query = parse_url($uri, PHP_URL_QUERY);
        parse_str($query, $result);
        return isset($result['id']) ? $result['id'] : null;
    }

    /**
     * Returns the PID from the URI by either reverse realurl or from params
     *
     * @param string $uri
     *
     * @return int|null
     */
    public function getTypo3PidFromUri($uri)
    {
        if (!$this->isValidUri($uri)) {
            return null;
        }

        if ($this->isRealUrlCompatibleUri($uri)) {
            $pid = (int)$this->getPidFromRealUrlApi($uri);
        } else {
            $pid = (int)$this->getUidFromUri($uri);
        }

        return $pid;
    }

    /**
     * Get RealUrl Cache
     *
     * @return \DmitryDulepov\Realurl\Cache\CacheInterface
     */
    protected function getRealUrlCache()
    {
        if ($this->realUrlCache === null) {
            $this->realUrlCache = CacheFactory::getCache();
        }
        return $this->realUrlCache;
    }

    /**
     * Is valid URI
     *
     * @param string $uri
     *
     * @return bool
     */
    protected function isValidUri($uri)
    {
        return !empty($uri) && $this->getHttpHost($uri) === GeneralUtility::getIndpEnv('HTTP_HOST');
    }

    /**
     * Check, if realurl is loaded and URI does not contain 'index.php'
     *
     * @param string $uri
     *
     * @return bool
     */
    protected function isRealUrlCompatibleUri($uri)
    {
        return ExtensionManagementUtility::isLoaded('realurl') && $this->getUidFromUri($uri) === null;
    }

    /**
     * Get PID from realurl API
     *
     * @param string $uri
     *
     * @return int|null
     */
    protected function getPidFromRealUrlApi($uri)
    {
        /** @var ConfigurationReader $realUrlConfiguration */
        $realUrlConfiguration = $this->objectManager->get(
            ConfigurationReader::class,
            ConfigurationReader::MODE_DECODE
        );

        $rootPageId = (int)$realUrlConfiguration->get('pagePath/rootpage_id');

        /** @var UrlCacheEntry $convertedUrl */
        $convertedUrl = $this->getRealUrlCache()->getUrlFromCacheBySpeakingUrl(
            $rootPageId,
            $this->getSpeakingUrl($uri),
            (int)GeneralUtility::_GET('L')
        );

        if ($convertedUrl === null) {
            return null;
        }

        return $convertedUrl->getPageId();
    }
}
