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
use JWeiland\RecommendAPage\Utility\PiwikMapperUtility;
use JWeiland\RecommendAPage\Utility\UriResolverUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
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
     * UriResolverUtility
     *
     * @var UriResolverUtility $uriResolverUtility
     */
    protected $uriResolverUtility;
    
    /**
     * PiwikDatabaseService
     *
     * @var PiwikDatabaseService $piwikDatabaseService
     */
    protected $piwikDatabaseService;
    
    /**
     * This is the main method that is called when a task is executed
     *
     * @return bool Returns TRUE on successful execution, FALSE on error
     */
    public function execute()
    {
        $this->init();
        
        $knownPiwikPagesList = $this->piwikDatabaseService->getActionIdsAndUrls();
        
        $recommendedPages = $this->getRecommendPagesForEachPage($knownPiwikPagesList);
        DebuggerUtility::var_dump($recommendedPages);
        
        return true;
    }
    
    /**
     * Init obj and resolve dependencies
     *
     * @return void
     */
    protected function init()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        
        $this->uriResolverUtility = GeneralUtility::makeInstance(UriResolverUtility::class);
        $this->piwikDatabaseService = $objectManager->get(PiwikDatabaseService::class);
    }
    
    /**
     * Create an array that holds all recommended Pages for every page
     *
     * @param $pages
     *
     * @return array
     */
    protected function getRecommendPagesForEachPage($pages)
    {
        /** @var PiwikMapperUtility $piwikMapper */
        $piwikMapper = GeneralUtility::makeInstance(PiwikMapperUtility::class);
    
        $mappedPages = $piwikMapper->mapPiwikPidsToTYPO3Pids($pages);
        
        /** @var array $updateList List that holds already updated pages*/
        $updateList = array();
    
        foreach ($pages as $key => $page)
        {
            $idaction = $page['idaction'];
            $name = $page['name'];
        
            if (!$mappedPages[$idaction]) {
                $typo3Pid = $this->uriResolverUtility->getTYPO3PidFromUri($name);
            } else {
                $typo3Pid = $mappedPages[$idaction];
            }
        
            // Piwik does not know if two uris point to the same pid so check for it
            if (!$updateList[$idaction] && $idaction !== null) {
                /*
                 * TODO:
                 * - Get count for this from configuration
                 */
                $recommendedPages = $this->piwikDatabaseService->getTargetPids($idaction);
            
                foreach ($recommendedPages as $targetPage) {
                    $targetPid = $mappedPages[$targetPage['targetPid']];
                    if ($targetPid != null) {
                        $updateList[$idaction] = array(
                            'referrer_pid' =>  $typo3Pid,
                            'target_pid' => $targetPid
                        );
                    }
                }
            }
        }
        
        return $updateList;
    }
    
    /**
     * Insert recommended Pages into the database
     *
     * @param array $pages
     *
     * @return bool|\mysqli_result|object MySQLi result object / DBAL object
     */
    protected function insertRecommendedPagesIntoDatabase($pages)
    {
        return $this->getDatabaseConnection()->exec_INSERTquery(
            'tx_recommendapage_domain_model_recommendedpage',
            $pages
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