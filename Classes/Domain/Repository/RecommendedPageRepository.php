<?php
namespace JWeiland\RecommendAPage\Domain\Repository;

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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * RecommendedPageRepository
 */
class RecommendedPageRepository extends Repository
{
    /**
     * Returns all entries to a specific referrer pid
     *
     * @param int $referrerId
     *
     * @return array
     */
    public function findAllByUid($referrerId)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('referrer_pid', $referrerId)
        );
        return $query->execute();
    }
}