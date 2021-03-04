<?php

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

return [
    'ctrl' => [
        'label' => '',
        'hideTable' => 1,
    ],
    'columns' => [
        'referrer_pid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:recommend_a_page/Resources/Private/Language/locallang.xlf:db.ref_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'target_pid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:recommend_a_page/Resources/Private/Language/locallang.xlf:db.target_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
    ],
];
