<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Recommend users pages others use to go',
    'description' => 'Recommend pages using PIWIK Statistics',
    'category' => '',
    'author' => 'Markus Kugler',
    'author_mail' => 'projects@jweiland.net',
    'state' => 'experimental',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.0.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-7.6.99',
            'php' => '5.3.7-7.9.99'
        ),
        'conflicts' => array(
            
        ),
        'suggests' => array(
            'realurl' => '2.0.0'
        ),
    )
);
