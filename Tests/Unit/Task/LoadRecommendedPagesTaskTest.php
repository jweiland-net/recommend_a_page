<?php
namespace JWeiland\RecommendAPage\Tests\Unit\Task;

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

use JWeiland\RecommendAPage\Service\PiwikDatabaseService;
use JWeiland\RecommendAPage\Task\LoadRecommendedPagesTask;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * LoadRecommendedPagesTaskTest
 */
class LoadRecommendedPagesTaskTest extends UnitTestCase
{
    /**
     * @var LoadRecommendedPagesTask|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;
    
    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = $this->createMock(LoadRecommendedPagesTask::class);
    }
    
    /**
     * TearDown
     */
    public function tearDown()
    {
        unset($this->subject);
    }
    
    /**
     * @test
     */
    public function executeWillReturnTrueBySuccess()
    {
        $piwikDatabaseResultSet = array(
            0 => array(
                'idaction' => '0',
                'name' => 'https://jweiland.net/index.php'
            ),
            1 => array(
                'idaction' => '201',
                'name' => 'http://jweiland.net/kontakt/impressum'
            )
        );
        
        /** @var PiwikDatabaseService|\PHPUnit_Framework_MockObject_MockObject $piwikDatabaseService */
        $piwikDatabaseService = $this->createMock(PiwikDatabaseService::class);
        $piwikDatabaseService->expects($this->once())
            ->method('getActionIdsAndUrls')
            ->willReturn(
                $piwikDatabaseResultSet
            );
        
        /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects($this->once())
            ->method('get')
            ->with(PiwikDatabaseService::class)
            ->willReturn($piwikDatabaseService);
        
        $this->subject->expects($this->once())
            ->method('getRecommendPagesForEachKnownPiwikPage')
            ->with($piwikDatabaseResultSet)
            ->willReturn(
                array(
                    0 => array(
                        'referrer_pid' => 0,
                        'target_pid' => 20
                    ),
                    1 => array(
                        'referrer_pid' => 201,
                        'target_pid' => 10
                    )
                )
            );
        
        $this->subject->expects($this->once())->method('init');
        $this->subject->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager);
        
        $this->subject->expects($this->once())
            ->method('insertNewRecommendedPagesIntoDatabase')
            ->with($piwikDatabaseResultSet);
        
        /** @var DatabaseConnection|\PHPUnit_Framework_MockObject_MockObject $databaseConnection */
        $databaseConnection = $this->createMock(DatabaseConnection::class);
        
        $globalsTYPO3DbBackup = $GLOBALS['TYPO3_DB'];
        $GLOBALS['TYPO3_DB'] = $databaseConnection;
    
        $databaseConnection->expects($this->exactly(2))
            ->method('exec_INSERTmultipleRows')
            ->with($this->arrayHasKey('referrer_pid'))
            ->with($this->arrayHasKey('target_pid'))
            ->willReturn(TRUE);
        
        $this->assertSame(TRUE, $this->subject->execute());
        
        $GLOBALS['TYPO3_DB'] = $globalsTYPO3DbBackup;
    }
}
