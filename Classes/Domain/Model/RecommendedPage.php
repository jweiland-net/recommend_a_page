<?php
namespace JWeiland\RecommendAPage\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * RecommendedPage
 */
class RecommendedPage extends AbstractEntity
{
    /**
     * referrerPid
     *
     * @var int
     */
    protected $referrerPid;
    
    /**
     * targetPid
     *
     * @var int
     */
    protected $targetPid;
    
    /**
     * Return referrerPid
     *
     * @return int
     */
    public function getReferrerPid()
    {
        return (int)$this->referrerPid;
    }
    
    /**
     * Set referrerPid
     *
     * @param int $referrerPid
     */
    public function setReferrerPid($referrerPid)
    {
        $this->referrerPid = (int)$referrerPid;
    }
    
    /**
     * Return targetPid
     *
     * @return int
     */
    public function getTargetPid()
    {
        return (int)$this->targetPid;
    }
    
    /**
     * Set targetPid
     *
     * @param int $targetPid
     */
    public function setTargetPid($targetPid)
    {
        $this->targetPid = (int)$targetPid;
    }
}