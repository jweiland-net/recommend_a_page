<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][JWeiland\RecommendAPage\Task\LoadRecommendedPagesTask::class] = array(
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' .
        $_EXTKEY .
        '/Resources/Private/Language/locallang.xlf:task.loadRecommendedPages.title',
    'description' => 'LLL:EXT:' .
        $_EXTKEY .
        '/Resources/Private/Language/locallang.xlf:task.loadRecommendedPages.description',
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.' . $_EXTKEY,
    'recommendPages',
    array(
        'Display' => 'show,',
    ),
    array(
        
    )
);

if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY] = @unserialize($_EXTCONF);
}