<?php
namespace JWeiland\RecommendAPage\Controller;

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
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * AbstractController
 */
class DisplayController extends ActionController
{
    /**
     * @var DatabaseConnection
     */
    protected $piwikDatabaseConnection;
    
    /**
     * Displays recommend Pages
     *
     * @return void
     */
   public function showAction()
   {
       $result = $this->getRowsWhereIdactionUrlRef('1');
       $this->view->assign('recommendedPages', $result);
   }
   
    /**
     * Returns all records with specified id
     *
     * @param string $id
     * @param string $limit
     *
     * @return array|NULL Array of rows, or NULL in case of SQL error
     */
   protected function getRowsWhereIdactionUrlRef($id = '', $limit = '3')
   {
       $pagesUrl = $this->getPiwikDatabaseConnection()->exec_SELECTgetRows(
           'piwik_log_action.name AS url',
           'piwik_log_link_visit_action INNER JOIN piwik_log_action ON piwik_log_action.idaction = piwik_log_link_visit_action.idaction_url',
           'idaction_url_ref = ' . $id,
           'piwik_log_link_visit_action.idaction_url DESC',
           '',
           $limit
       );
       
       $pagesName = $this->getPiwikDatabaseConnection()->exec_SELECTgetRows(
           'piwik_log_action.name AS name',
           'piwik_log_link_visit_action INNER JOIN piwik_log_action ON piwik_log_action.idaction = piwik_log_link_visit_action.idaction_name',
           'idaction_url_ref = ' . $id,
           'piwik_log_link_visit_action.idaction_url DESC',
           '',
           $limit
       );
       
       $result = array();
       
       foreach ($pagesUrl as $key => $value) {
           $result[$key] = array_merge($pagesUrl[$key], $pagesName[$key]);
       }
       
       return $result;
   }
    
    /**
     * Returns the Database connection to PIWIK DB
     *
     * @return DatabaseConnection
     */
    protected function getPiwikDatabaseConnection()
    {
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = GeneralUtility::makeInstance(DatabaseConnection::class);
        $databaseConnection->setDatabaseHost(PiwikDatabaseInterface::HOST);
        $databaseConnection->setDatabaseName(PiwikDatabaseInterface::DATABASE);
        $databaseConnection->setDatabaseUsername(PiwikDatabaseInterface::USER);
        $databaseConnection->setDatabasePassword(PiwikDatabaseInterface::PASSWORD);
        $this->piwikDatabaseConnection = $databaseConnection;
        
        return $this->piwikDatabaseConnection;
    }
    
    /**
     * Returns Page uid from globals
     *
     * @return string
     */
    protected function getPageIdFromGlobals() {
        return $GLOBALS['TSFE']->id;
    }
}