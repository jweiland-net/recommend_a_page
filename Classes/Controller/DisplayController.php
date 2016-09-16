<?php
namespace JWeiland\RecommendAPage\Controller;

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
     * @var \JWeiland\RecommendAPage\Domain\Repository\RecommendedPageRepository
     */
    protected $recommendedPageRepository = null;
    
    /**
     * @param RecommendedPageRepository $recommendedPageRepository
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
        $recommendations = $this->recommendedPageRepository->findByReferrerPid($GLOBALS['TSFE']->id);
        $this->view->assign('recommendations', $recommendations);
    }
}