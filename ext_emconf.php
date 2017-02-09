<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Recommend a Page',
    'description' => 'Recommend pages using PIWIK Statistics',
    'category' => '',
    'author' => 'Markus Kugler',
    'author_mail' => 'projects@jweiland.net',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.2.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-8.4.99',
            'php' => '5.6.0-7.1.99'
        ),
        'conflicts' => array(
            'realurl' => '1.0.0-1.9.99'
        ),
        'suggests' => array(
            'realurl' => '2.0.0'
        ),
    )
);
