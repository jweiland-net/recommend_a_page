<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Task;

use JWeiland\RecommendAPage\Mapper\PiwikMapper;
use JWeiland\RecommendAPage\Service\PiwikDatabaseService;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * LoadRecommendedPagesTask
 */
class LoadRecommendedPagesTask extends AbstractTask
{
    /**
     * PiwikDatabaseService
     *
     * @var PiwikDatabaseService
     */
    protected $piwikDatabaseService;

    /**
     * ObjectManager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * This is the main method that is called when a task is executed
     *
     * @return bool Returns true on successful execution, false on error
     */
    public function execute()
    {
        $this->init();

        $knownPiwikPageList = $this->piwikDatabaseService->getActionIdsAndUrls();

        $recommendedPages = $this->getRecommendPagesForEachKnownPiwikPage($knownPiwikPageList);

        $this->insertNewRecommendedPagesIntoDatabase($recommendedPages);

        return true;
    }

    /**
     * Init obj and resolve dependencies
     */
    protected function init()
    {
        /** @var ObjectManager $objectManager */
        $this->objectManager = $objectManager = $this->getObjectManager();
        $this->piwikDatabaseService = $objectManager->get(PiwikDatabaseService::class);
    }

    /**
     * Create an array that holds all recommended Pages for every page
     *
     * @param array $pages
     * @return array
     */
    protected function getRecommendPagesForEachKnownPiwikPage($pages)
    {
        /** @var PiwikMapper $piwikMapper */
        $piwikMapper = $this->objectManager->get(PiwikMapper::class);

        /** @var array $mappedPages array(piwikPid => TYPO3pid) */
        $mappedPages = $piwikMapper->mapPiwikPidsToTypo3Pids($pages);

        /** @var array $updateList This list makes sure that uris that point to the same page aren't looped twice */
        $updateList = array();

        // Go trough every page that piwik knows of
        foreach ($pages as $key => $page) {
            $idAction = $page['idaction'];

            $typo3Pid = $mappedPages[$idAction];

            // Piwik does not know that two uris point to the same pid so check for it
            if ($typo3Pid !== null && !$updateList[$typo3Pid]) {
                $piwikRecommendedPages = $this->piwikDatabaseService->getTargetIdActions($idAction);

                $recommendedPages = $this->resolveRecommendedPages($piwikRecommendedPages, $mappedPages, $typo3Pid);
                $updateList[$typo3Pid] = array();

                foreach ($recommendedPages as $targetPid => $clicks) {
                    $updateList[$typo3Pid][] = $this->prepareRecommendedPageForDatabase(
                        $typo3Pid,
                        $targetPid
                    );
                }
            }
        }

        return $updateList;
    }

    /**
     * Resolves idActions to pids according to mapping and returns count configured
     *
     * @param array $piwikRecommendedPages
     * @param array $mapping
     * @param int $referrerTypo3Pid
     * @return array returns empty array if pages cannot be resolved
     */
    protected function resolveRecommendedPages($piwikRecommendedPages, $mapping, $referrerTypo3Pid)
    {
        $recommendedPages = array();
        $maxRecommendedPages = $this->getRecommendedPagesCount();

        for ($i = 0; $i < count($piwikRecommendedPages) && count($recommendedPages) < $maxRecommendedPages; $i++) {
            $recommendedPage = $piwikRecommendedPages[$i];
            $recommendedPageTypo3Pid = $mapping[$recommendedPage['targetPid']];

            if (
                $recommendedPageTypo3Pid != null &&
                $recommendedPageTypo3Pid != $referrerTypo3Pid &&
                !$recommendedPages[$recommendedPageTypo3Pid]
            ) {
                $recommendedPages[$recommendedPageTypo3Pid] = $recommendedPage['clicks'];
            }
        }

        return $recommendedPages;
    }

    /**
     * Add typo3Pid and targetPid to an assoc array for database insert
     *
     * @param int $typo3Pid
     * @param int $targetPid
     * @return array Returns an array with column name as array key
     */
    protected function prepareRecommendedPageForDatabase($typo3Pid, $targetPid)
    {
        return array(
            'referrer_pid' =>  $typo3Pid,
            'target_pid' => $targetPid
        );
    }

    /**
     * Insert recommended Pages into the database
     *
     * @param array $pages
     */
    protected function insertNewRecommendedPagesIntoDatabase($pages)
    {
        $this->getDatabaseConnection()->exec_TRUNCATEquery('tx_recommendapage_domain_model_recommendedpage');

        foreach ($pages as $recommendedPages) {
            $this->getDatabaseConnection()->exec_INSERTmultipleRows(
                'tx_recommendapage_domain_model_recommendedpage',
                array(
                    'referrer_pid', 'target_pid'
                ),
                $recommendedPages
            );
        }
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

    /**
     * Returns count of recommended pages to display
     *
     * @return int
     */
    protected function getRecommendedPagesCount()
    {
        return $this->piwikDatabaseService->getDatabaseConfiguration()['countOfRecommendedPages'];
    }

    /**
     * Get the ObjectManager
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
