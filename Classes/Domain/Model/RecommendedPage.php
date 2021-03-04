<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Domain\Model;

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
