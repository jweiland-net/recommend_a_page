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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * Returns realurl like page path from uri
     *
     * @param string $uri
     *
     * @return string
     */
    public function getPagePath($uri)
    {
        $uriArray = parse_url($uri);
        return ltrim($uriArray['path'], '/');
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
}