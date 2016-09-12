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
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\ViewHelpers\DebugViewHelper;
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
        
        $piwikToTypo3PidList = array();
        
        foreach ($knownPiwikPageList as $key => $page) {
            $piwikToTypo3PidList[$key] = $uriResolverUtility->getTYPO3PidFromUri($page['name']);
        }
        
        $updateList = array();
        
        foreach ($knownPiwikPageList as $key => $page)
        {
            $idaction = $page['idaction'];
            $name = $page['name'];
            
            $typo3Pid = $uriResolverUtility->getTYPO3PidFromUri($name);
            if (!$updateList[$idaction] && $idaction !== null) {
                $updateList[$idaction] = $typo3Pid;
                
                /*
                 * TODO:
                 * - Get count for this from configuration
                 */
                $recommendedPages = $piwikDatabaseService->getTargetPids($idaction);
                
                foreach ($recommendedPages as $targetPage) {
                    $targetPid = $piwikToTypo3PidList[$targetPage['targetPid']];
                    DebuggerUtility::var_dump(array($typo3Pid => $targetPid));
                    if (!empty($targetPid)) {
                        $this->insertRecommendedPagesToDatabase($typo3Pid, $targetPid);
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Insert recommended Pages into the database
     *
     * @param int $referrerPid
     * @param int $targetPid
     *
     * @return bool|\mysqli_result|object MySQLi result object / DBAL object
     */
    protected function insertRecommendedPagesToDatabase($referrerPid, $targetPid)
    {
        return $this->getDatabaseConnection()->exec_INSERTmultipleRows(
            'tx_recommendapage_domain_model_recommendedpage',
            array('referrer_pid', 'target_pid'),
            array(
                'referrer_pid' => $referrerPid,
                'target_pid' => $targetPid
            )
        );
    }
    
    /**
     * Returns the TYPO3 database connection from globals
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}