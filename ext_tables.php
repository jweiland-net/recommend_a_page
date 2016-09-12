<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'JWeiland.' . $_EXTKEY,
        'recommendPages',
        'Recommend Pages'
    );
}