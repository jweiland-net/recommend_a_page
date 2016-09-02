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

use JWeiland\RecommendAPage\Utility\UriResolverUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * PiwikDatabaseService
 */
class PiwikDatabaseService
{
    // TODO: Respect piwik idsite field in case piwik is used for multiple sites
    
    /**
     * databaseConnection
     *
     * @var DatabaseConnection
     */
    private $databaseConnection = null;
    
    /**
     * databaseConfiguration
     *
     * @var array
     */
    private $databaseConfiguration = array();
    
    /**
     * Initialization
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->databaseConfiguration = $this->getDatabaseConfiguration();
        $this->databaseConnection = $this->getDatabaseConnection();
    }
    
    /**
     * Returns the pid piwik defined for it self
     *
     * @param string $uri
     *
     * @return int
     */
    public function getPiwikPageIdByUri($uri)
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
     *
     * @return array|NULL Array of rows, or NULL in case of SQL error
     */
    public function getRecommendedPagesByCurrentPiwikPid($piwikId)
    {
        $limit = $this->databaseConfiguration['countOfRecommendedPages'];
        
        return $this->databaseConnection->exec_SELECTgetRows(
            'custom_var_v1 AS pid, piwik_log_action.name AS title',
            'piwik_log_link_visit_action 
            INNER JOIN piwik_log_action ON piwik_log_action.idaction = piwik_log_link_visit_action.idaction_name',
            'custom_var_k1 = "typo3pid" 
            AND idaction_url_ref = ' . $piwikId .
            ' AND NOT idaction_url = idaction_url_ref',
            'custom_var_v1 DESC',
            null,
            $limit
        );
    }
    
    /**
     * Returns an array of all recommended pages already grouped and sorted
     *
     * @return array|NULL Array of rows, or NULL in case of SQL error
     */
    public function getPreparedRecommendedPages()
    {
        return $this->getDatabaseConnection()->exec_SELECTgetRows(
            'custom_var_v1 AS requestPid, custom_var_v2 AS targetPid, COUNT(*) AS requests',
            'piwik_log_link_visit_action',
            '',
            'idaction_url_ref, idaction_url',
            'idaction_url_ref, requests DESC'
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
        $databaseConnection->setDatabaseHost($this->databaseConfiguration['piwikDatabaseHost']);
        $databaseConnection->setDatabaseName($this->databaseConfiguration['piwikDatabaseName']);
        $databaseConnection->setDatabaseUsername($this->databaseConfiguration['piwikDatabaseUser']);
        $databaseConnection->setDatabasePassword($this->databaseConfiguration['piwikDatabasePassword']);
        $this->databaseConnection = $databaseConnection;
        
        return $this->databaseConnection;
    }
    
    /**
     * Loads database details from ext conf
     *
     * @return array
     */
    protected function getDatabaseConfiguration()
    {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['recommend_a_page']);
    }
}