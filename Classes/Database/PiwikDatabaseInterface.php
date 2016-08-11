<?php
namespace JWeiland\RecommendAPage\Database;

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

/**
 * Interface PiwikDatabaseInterface
 */
interface PiwikDatabaseInterface
{
    /**
     * @var string
     */
    const USER = 'root';
    
    /**
     * @var string
     */
    const PASSWORD = 'geheim12';
    
    /**
     * @var string
     */
    const DATABASE = 'piwik';
    
    /**
     * @var string
     */
    const HOST = 'localhost';
}