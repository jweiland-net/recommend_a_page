<?php
namespace JWeiland\RecommendAPage\Service;

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

use JWeiland\RecommendAPage\Database\PiwikDatabaseInterface;
use JWeiland\RecommendAPage\Utility\UriResolverUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * PiwikDatabaseService
 */
class PiwikDatabaseService
{
    /**
     * @var DatabaseConnection
     */
    private $databaseConnection = null;
    
    /**
     * Initialization
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->databaseConnection = $this->getDatabaseConnection();
    }
    
    /**
     * Returns the pid piwik defined for it self
     *
     * @param string $uri
     *
     * @return int
     */
    public function getPiwikPageIdByUri($uri = '')
    {
        /** @var UriResolverUtility $uriResolver */
        $uriResolver = GeneralUtility::makeInstance(UriResolverUtility::class);
        
        $uri = $uriResolver->prepareUriForPiwik($uri);
    
        $result = $this->databaseConnection->exec_SELECTgetSingleRow('idaction', 'piwik_log_action',
            'name = \'' . $uri . '\'');
    
        return $result['idaction'];
    }
    
    /**
     * Returns the TYPO3 pid from the last visits that is defined as custom var k1 in piwik
     *
     * @param int $piwikId
     * @param string $limit
     *
     * @return array|NULL Array of rows, or NULL in case of SQL error
     */
    public function getRecommendedPagesByCurrentPiwikPid($piwikId = 0, $limit = '3')
    {
        return $this->databaseConnection->exec_SELECTgetRows(
            'custom_var_v1 AS pid, piwik_log_action.name AS title',
            'piwik_log_link_visit_action INNER JOIN piwik_log_action ON piwik_log_action.idaction = piwik_log_link_visit_action.idaction_name',
            'custom_var_k1 = "typo3pid" AND idaction_url_ref = ' . $piwikId . ' AND NOT idaction_url = idaction_url_ref',
            'custom_var_v1 DESC',
            null,
            $limit
        );
    }
    
    /**
     * Returns the Database connection to PIWIK DB
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = GeneralUtility::makeInstance(DatabaseConnection::class);
        $databaseConnection->setDatabaseHost(PiwikDatabaseInterface::HOST);
        $databaseConnection->setDatabaseName(PiwikDatabaseInterface::DATABASE);
        $databaseConnection->setDatabaseUsername(PiwikDatabaseInterface::USER);
        $databaseConnection->setDatabasePassword(PiwikDatabaseInterface::PASSWORD);
        $this->databaseConnection = $databaseConnection;
        
        return $this->databaseConnection;
    }
}