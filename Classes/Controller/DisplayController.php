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
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * AbstractController
 */
class DisplayController extends ActionController {
    /**
     * Displays recommend Pages
     *
     * @return void
     */
   public function showAction() {
       $database = $this->getDatabaseConnection();
   }
    
    /**
     * Returns the DatabaseConnection from the Globals
     *
     * @return DatabaseConnection
     */
   private function getDatabaseConnection() {
       return $GLOBALS['TYPO3_DB'];
   }
}