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

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * PiwikDatabaseService
 */
class PiwikDatabaseService
{
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
     * Returns all known actions ids and names
     *
     * @return array|NULL Array of rows, or NULL in case of SQL error
     */
    public function getActionIdsAndUrls()
    {
        return $this->getDatabaseConnection()->exec_SELECTgetRows(
            'idaction, name',
            'piwik_log_action',
            'type != 4 AND name LIKE \'%' .
            $this->databaseConnection->escapeStrForLike(GeneralUtility::getIndpEnv('HTTP_HOST'), 'piwik_log_action') .
            '%\''
        );
    }
    
    /**
     * Returns configured count top clicked pages grouped
     *
     * @param int $pid
     *
     * @return array
     */
    public function getTargetPids($pid)
    {
        $pid = $this->databaseConnection->fullQuoteStr($pid, 'piwik_log_link_visit_action');
        
        return $this->databaseConnection->exec_SELECTgetRows(
            'idaction_url as targetPid, COUNT(*) AS clicks',
            'piwik_log_link_visit_action',
            'idaction_url_ref = ' . $pid,
            'idaction_url_ref, idaction_url',
            'clicks',
            $this->getDatabaseConfiguration()['countOfRecommendedPages']
        );
    }
    
    /**
     * Returns the Database connection to PIWIK DB
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        if (!empty($this->databaseConnection)) {
            return $this->databaseConnection;
        }
        
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = GeneralUtility::makeInstance(DatabaseConnection::class);
        $databaseConnection->setDatabaseHost($this->getDatabaseConfiguration()['piwikDatabaseHost']);
        $databaseConnection->setDatabaseName($this->getDatabaseConfiguration()['piwikDatabaseName']);
        $databaseConnection->setDatabaseUsername($this->getDatabaseConfiguration()['piwikDatabaseUser']);
        $databaseConnection->setDatabasePassword($this->getDatabaseConfiguration()['piwikDatabasePassword']);
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
        return $this->databaseConfiguration = unserialize(
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['recommend_a_page']
        );
    }
}