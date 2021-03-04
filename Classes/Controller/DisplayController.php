<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Controller;

use JWeiland\RecommendAPage\Domain\Repository\RecommendedPageRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * DisplayController
 */
class DisplayController extends ActionController
{
    /**
     * recommendedPageRepository
     *
     * @var RecommendedPageRepository
     */
    protected $recommendedPageRepository = null;

    /**
     * Injects the RecommendedPageRepository
     *
     * @param RecommendedPageRepository $recommendedPageRepository
     *
     * @return void
     */
    public function injectRecommendedPageRepository(RecommendedPageRepository $recommendedPageRepository)
    {
        $this->recommendedPageRepository = $recommendedPageRepository;
    }

    /**
     * Displays the recommended pages
     *
     * @return void
     */
    public function showAction()
    {
        $recommendations = $this->recommendedPageRepository->findByReferrerPid((int)$GLOBALS['TSFE']->id);
        $this->view->assign('recommendations', $recommendations);
    }
}
