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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * PiwikMapper
 */
class PiwikMapper
{
    /**
     * Maps piwik pids to typo3 pids
     *
     * @param array $pages from table piwik_log_action
     *
     * @return array
     */
    public function mapPiwikPidsToTYPO3Pids($pages)
    {
        /** @var UriMapper $uriMapper */
        $uriMapper = GeneralUtility::makeInstance(UriMapper::class);
        
        $mappedPages = array();
        foreach ($pages as $key => $page) {
            $mappedPages[$page['idaction']] = $uriMapper->getTYPO3PidFromUri($page['name']);
        }
        
        return $mappedPages;
    }
}