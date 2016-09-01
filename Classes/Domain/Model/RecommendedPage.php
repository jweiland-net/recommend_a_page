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
     * @var int
     */
    protected $refPid;
    
    /**
     * @var int
     */
    protected $targetPid;
    
    public function setRefPid($refPid)
    {
        $this->refPid = $refPid;
    }
    
    public function setTargetPid($targetPid)
    {
        $this->targetPid = $targetPid;
    }
    
    public function getRefPid()
    {
        return $this->refPid;
    }
    
    public function getTargetPid()
    {
        return $this->targetPid;
    }
}