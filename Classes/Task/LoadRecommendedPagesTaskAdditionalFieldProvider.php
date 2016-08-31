<?php
namespace JWeiland\RecommendAPage\Task;

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

use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Additional fields for OpenWeatherMap scheduler task
 */
class LoadRecommendedPagesTaskAdditionFieldProvider implements AdditionalFieldProviderInterface
{
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $parentObject) {
        
    }
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject) {
        
    }
    public function saveAdditionalFields(array $submittedData, AbstractTask $task) {
        
    }
}