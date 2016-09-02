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

/**
 * RealUrlDatabaseService
 */
class RealUrlDatabaseService
{
    /**
     * Returns pid for given page path
     *
     * @param string $pagePath
     *
     * @return int
     */
    public function getPidFromPagePath($pagePath = '')
    {
       return $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
           'page_id',
           'tx_realurl_pathcache',
           'pagepath = \'' . $pagePath . '\''
       )['page_id'];
    }
    
    /**
     * Returns database connection from globals
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}