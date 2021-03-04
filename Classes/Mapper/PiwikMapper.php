<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Mapper;

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
        $mappedPages = [];

        if (!is_array($pages)) {
            return [];
        }

        foreach ($pages as $key => $page) {
            $typo3pid = $this->uriMapper->getTypo3PidFromUri($page['name']);
            $typo3Page = $this->pageRepository->getPage($typo3pid);

            if (
                $typo3Page['tx_recommend_a_page_do_not_recommend'] == 0 &&
                $typo3Page['hidden'] == 0 &&
                $typo3Page['deleted'] == 0
            ) {
                $mappedPages[$page['idaction']] = $typo3pid;
            }
        }

        return $mappedPages;
    }
}
