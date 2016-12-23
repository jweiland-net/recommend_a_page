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

use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * PiwikMapper
 */
class PiwikMapper
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;
    
    /**
     * @var UriMapper
     */
    protected $uriMapper;
    
    /**
     * inject pageRepository
     *
     * @param PageRepository $pageRepository
     */
    public function injectPageRepository(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }
    
    /**
     * inject uriMapper
     *
     * @param UriMapper $uriMapper
     * @return void
     */
    public function injectUriMapper(UriMapper $uriMapper)
    {
        $this->uriMapper = $uriMapper;
    }
    
    /**
     * Maps piwik pids to typo3 pids
     *
     * @param array $pages from table piwik_log_action
     *
     * @return array
     */
    public function mapPiwikPidsToTypo3Pids($pages)
    {
        $mappedPages = array();
        
        if (!is_array($pages)) {
            return array();
        }
        
        foreach ($pages as $key => $page) {
            $typo3pid = $this->uriMapper->getTypo3PidFromUri($page['name']);
            $typo3Page = $this->pageRepository->getPage($typo3pid);
            
            if ($typo3Page['nav_hide'] == 0 && $typo3Page['hidden'] == 0 && $typo3Page['deleted'] == 0) {
                $mappedPages[$page['idaction']] = $typo3pid;
            }
        }
        
        return $mappedPages;
    }
}
