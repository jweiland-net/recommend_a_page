<?php
namespace JWeiland\RecommendAPage\Task;

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
use JWeiland\RecommendAPage\Utility\UriResolverUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * LoadRecommendedPagesTask
 */
class LoadRecommendedPagesTask extends AbstractTask
{
    /**
     * This is the main method that is called when a task is executed
     *
     * @return bool Returns TRUE on successful execution, FALSE on error
     */
    public function execute()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        
        /** @var UriResolverUtility $uriResolverUtility */
        $uriResolverUtility = GeneralUtility::makeInstance(UriResolverUtility::class);
        
        /** @var PiwikDatabaseService $piwikDatabaseService */
        $piwikDatabaseService = $objectManager->get(PiwikDatabaseService::class);
        
        $knownPiwikPageList = $piwikDatabaseService->getActionIdsAndUrls();
        
        $updateList = array();
        
        foreach ($knownPiwikPageList as $key => $page)
        {
            $idaction = $page['idaction'];
            $name = $page['name'];
            
            $typo3Pid = $uriResolverUtility->getTYPO3PidFromUri($name);
            if ($updateList[$idaction] && $idaction !== null) {
                $updateList[$idaction] = $typo3Pid;
                
                /*
                 * TODO:
                 * - Get count for this from configuration
                 */
                $recommendedPages = $piwikDatabaseService->getTargetPids($idaction);
                foreach ($recommendedPages as $targetPage) {
                    $targetPid = $targetPage['targetPid'];
                    
                    if (!$updateList[$targetPid]) {
                        $updateList[$idaction] = $uriResolverUtility->getTYPO3PidFromUri($name);
                    }
                }
            }
        }
        
        DebuggerUtility::var_dump($updateList);
        
        return true;
    }
}