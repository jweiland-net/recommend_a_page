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
        
        $recommendedPages = $piwikDatabaseService->getPreparedRecommendedPages();
        for ($i = 0; $i < count($recommendedPages); $i++) {
            unset($recommendedPages[$i]['requests']);
        }
        if (!$this->insertRecommendedPagesToDatabase($this->getDatabaseConnection(), $recommendedPages)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Insert recommended Pages into the database
     *
     * @param DatabaseConnection $database
     * @param array $recommendedPages
     * @return bool|\mysqli_result|object MySQLi result object / DBAL object
     */
    protected function insertRecommendedPagesToDatabase($database, $recommendedPages)
    {
        return $database->exec_INSERTmultipleRows(
            'tx_recommendapage_domain_model_recommendedpage',
            array('referrer_pid', 'target_pid'),
            $recommendedPages
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