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
        
        $this->insertNewRecommendedPagesIntoDatabase($recommendedPages);
        
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
    
        /** @var array $mappedPages array(piwikPid => TYPO3pid) */
        $mappedPages = $piwikMapper->mapPiwikPidsToTYPO3Pids($pages);
        
        /** @var array $updateList List that holds already updated pages*/
        $updateList = array();
    
        // Go trough every page that piwik knows of
        foreach ($pages as $key => $page) {
            $idaction = $page['idaction'];

            $typo3Pid = $mappedPages[$idaction];
        
            // Piwik does not know that two uris point to the same pid so check for it
            if ($idaction !== null && !$updateList[$typo3Pid]) {
                $recommendedPages = $this->piwikDatabaseService->getTargetPids($idaction);
                
                $updateList[$typo3Pid] = array();
                
                // Get Recommended pages
                foreach ($recommendedPages as $targetPage) {
                    $targetPid = $mappedPages[$targetPage['targetPid']];
                    if ($targetPid != null) {
                        array_push($updateList[$typo3Pid], array(
                            'referrer_pid' =>  $typo3Pid,
                            'target_pid' => $targetPid
                        ));
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
     * @return void
     */
    public function insertNewRecommendedPagesIntoDatabase($pages)
    {
        $this->getDatabaseConnection()->exec_TRUNCATEquery('tx_recommendapage_domain_model_recommendedpage');
        
        $offset = 0;
        $nextOffset = 300;
        
        foreach ($pages as $recommendedPages) {
            $pagesToInsert = array_splice($recommendedPages, $offset, $nextOffset);
            $this->getDatabaseConnection()->exec_INSERTmultipleRows(
                'tx_recommendapage_domain_model_recommendedpage',
                array(
                    'referrer_pid', 'target_pid'
                ),
                $pagesToInsert
            );
            $offset += $nextOffset;
        }
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