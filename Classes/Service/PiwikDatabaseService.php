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
     * @return array Array of rows, or empty array in case of SQL error
     */
    public function getActionIdsAndUrls()
    {
        $host = $this->getHost();
        
        $constraints = array();
        
        $constraints[] = 'name LIKE ' .
            $this->databaseConnection->fullQuoteStr(
                $this->databaseConnection->escapeStrForLike(
                    $host, 'piwik_log_action'
                ) . '%', 'piwik_log_action'
            );
    
        $constraints[] = 'name LIKE ' .
            $this->databaseConnection->fullQuoteStr(
                'http://' .
                $this->databaseConnection->escapeStrForLike(
                    $host, 'piwik_log_action'
                ) . '%', 'piwik_log_action'
            );
    
        $constraints[] = 'name LIKE ' .
            $this->databaseConnection->fullQuoteStr(
                'https://' .
                $this->databaseConnection->escapeStrForLike(
                    $host, 'piwik_log_action'
                ) . '%', 'piwik_log_action'
            );
        
        $result = $this->getDatabaseConnection()->exec_SELECTgetRows(
            'idaction, name',
            'piwik_log_action',
            'type != 4 AND ' .
            implode(' OR ', $constraints)
        );
        
        if ($result === null) {
            $result = array();
        }
        
        return $result;
    }
    
    /**
     * Returns all pages that were visited from idAction
     *
     * @param int $idAction
     *
     * @return array
     */
    public function getTargetIdActions($idAction)
    {
        $idAction = $this->databaseConnection->fullQuoteStr($idAction, 'piwik_log_link_visit_action');
        
        $result = $this->databaseConnection->exec_SELECTgetRows(
            'idaction_url as targetPid, COUNT(*) AS clicks',
            'piwik_log_link_visit_action',
            'idaction_url_ref = ' . $idAction . ' AND idaction_url <> ' . $idAction,
            'idaction_url_ref, idaction_url',
            'clicks DESC'
        );
        
        if ($result === null) {
            $result = array();
        }
        
        return $result;
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
    public function getDatabaseConfiguration()
    {
        return $this->databaseConfiguration = unserialize(
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['recommend_a_page']
        );
    }
    
    /**
     * Returns host from IndpEnv
     *
     * @return string
     */
    protected function getHost()
    {
        return GeneralUtility::getIndpEnv('HTTP_HOST');
    }
}