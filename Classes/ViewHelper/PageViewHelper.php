<?php
namespace JWeiland\RecommendAPage\ViewHelper;

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
 * PageViewHelper
 */
class PageViewHelper
{
    /**
     * Returns title of page
     *
     * @param int $pid
     */
    public function getPageTitleByPid($pid = 0)
    {
        $dbConnection = $this->getDatabaseConnection();
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