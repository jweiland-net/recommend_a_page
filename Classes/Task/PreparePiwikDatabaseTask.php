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
 * PreparePiwikDatabaseTask
 */
class PreparePiwikDatabaseTask extends AbstractTask
{
    /**
     * TODO: Problem with real url because it only allows FE Environment
     *
     * This is the main method that is called when a task is executed
     *
     * @return bool Returns TRUE on successful execution, FALSE on error
     */
    public function execute()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        
        /** @var PiwikDatabaseService $piwikDatabaseService */
        $piwikDatabaseService = $objectManager->get(PiwikDatabaseService::class);
        
        $piwikActions = $piwikDatabaseService->getActionIdsAndUrls(30);
        
        /** @var UriResolverUtility $uriResolverUtility */
        $uriResolverUtility = GeneralUtility::makeInstance(UriResolverUtility::class);
        
        foreach ($piwikActions as $action)
        {
            $piwikDatabaseService->updateRows(
                array(
                    'custom_var_k1' => 'referrerPid',
                    'custom_var_v1' => $uriResolverUtility->getTYPO3PidFromUri($action['name'])
                ),
                'idaction_url = ' . $action['idaction']
            );
            $piwikDatabaseService->updateRows(
                array(
                    'custom_var_k2' => 'targetPid',
                    'custom_var_v2' => $uriResolverUtility->getTYPO3PidFromUri($action['name'])
                ),
                'idaction_url_ref = ' . $action['idaction']
            );
        }
        DebuggerUtility::var_dump($piwikActions);
        
        return true;
    }
}