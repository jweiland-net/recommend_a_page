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
use SJBR\StaticInfoTables\Domain\Model\AbstractEntity;

/**
 * RecommendedPage
 */
class RecommendedPage extends AbstractEntity
{
    /**
     * refPid
     *
     * @var int
     */
    protected $refPid;
    
    /**
     * targetPid
     *
     * @var int
     */
    protected $targetPid;
    
    /**
     * Set refPid
     *
     * @param int $refPid
     */
    public function setRefPid($refPid)
    {
        $this->refPid = $refPid;
    }
    
    /**
     * Set targetPid
     *
     * @param int $targetPid
     */
    public function setTargetPid($targetPid)
    {
        $this->targetPid = $targetPid;
    }
    
    /**
     * Return refPid
     *
     * @return int
     */
    public function getRefPid()
    {
        return $this->refPid;
    }
    
    /**
     * Return targetPid
     *
     * @return int
     */
    public function getTargetPid()
    {
        return $this->targetPid;
    }
}