<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$doNotRecommend = [
    'tx_recommend_a_page_do_not_recommend' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:recommend_a_page/Resources/Private/Language/locallang.xlf:pages.recommend_a_page',
        'config' => [
            'type' => 'check',
            'items' => [
                '1' => [
                    '0' => 'LLL:EXT:recommend_a_page/Resources/Private/Language/locallang.xlf:pages.recommend_a_page.doNotRecommend',
                ]
            ]
        ]
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    $doNotRecommend
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    'tx_recommend_a_page_do_not_recommend',
    '',
    'after:nav_title'
);
