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

use JWeiland\RecommendAPage\Service\PiwikDatabaseService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * DisplayController
 */
class DisplayController extends ActionController
{
    /**
     * piwikDatabaseService
     *
     * @var PiwikDatabaseService
     */
    protected $piwikDatabaseService = null;
    
    /**
     * recommendedPageRepository
     *
     * @var \JWeiland\RecommendAPage\Domain\Repository\RecommendedPageRepository
     * @inject
     */
    protected $recommendedPageRepository = null;
    
    /**
     * Displays the recommended pages
     *
     * @return void
     */
    public function showAction()
    {
        //$this->piwikDatabaseService = $this->objectManager->get(PiwikDatabaseService::class);

        //$piwikPid = $this->piwikDatabaseService->getPiwikPageIdByUri($this->uriBuilder->getRequest()->getRequestUri());
        //$recommendedPages = $this->piwikDatabaseService->getRecommendedPagesByCurrentPiwikPid(0);
        //$this->view->assign('recommendedPages', $recommendedPages);
        
        //DebuggerUtility::var_dump($this->recommendedPageRepository->findAll());
        
        DebuggerUtility::var_dump($this->recommendedPageRepository->findAll());
        
    }
}