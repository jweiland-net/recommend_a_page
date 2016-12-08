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
use TYPO3\CMS\Core\Tests\AccessibleObjectInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * LoadRecommendedPagesTaskTest
 */
class LoadRecommendedPagesTaskTest extends UnitTestCase
{
    /**
     * @var LoadRecommendedPagesTask|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $subject;
    
    /**
     * @var PiwikDatabaseService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $piwikDatabaseService;
    
    /**
     * @var DatabaseConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $databaseConnection;
    
    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            LoadRecommendedPagesTask::class,
            array(
                'init',
                'getRecommendPagesForEachKnownPiwikPage',
                'getObjectManager',
                'getDatabaseConnection'
            ),
            array(),
            '',
            false
        );
    
        $this->piwikDatabaseService = $this->createMock(PiwikDatabaseService::class);
        $this->databaseConnection = $this->createMock(DatabaseConnection::class);
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
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function executeWithNullWillThrowInvalidArgumentSuppliedForForeach()
    {
        $piwikDatabaseResultSet = array(null, null);
    
        $this->piwikDatabaseService->expects($this->once())
            ->method('getActionIdsAndUrls')
            ->willReturn(
                $piwikDatabaseResultSet
            );
    
        $this->subject->_set(
            'piwikDatabaseService',
            $this->piwikDatabaseService
        );
    
        $this->databaseConnection->expects($this->once())
            ->method('exec_TRUNCATEquery');
    
        $this->subject->expects($this->exactly(1))
            ->method('getDatabaseConnection')
            ->willReturn($this->databaseConnection);
    
        $this->subject->expects($this->once())
            ->method('getRecommendPagesForEachKnownPiwikPage')
            ->with($this->equalTo($piwikDatabaseResultSet));
        
        $this->subject->execute();
    }
    
    /**
     * @test
     */
    public function executeWithValidDataWillReturnTrue()
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
        
        $expectedValue = array(
            0 => array(
                'referrer_pid' => 0,
                'target_pid' => 20
            ),
            1 => array(
                'referrer_pid' => 201,
                'target_pid' => 10
            )
        );
        
        $this->piwikDatabaseService->expects($this->once())
            ->method('getActionIdsAndUrls')
            ->willReturn(
                $piwikDatabaseResultSet
            );
        
        $this->subject->_set(
            'piwikDatabaseService',
            $this->piwikDatabaseService
        );
        
        $this->subject->expects($this->once())
            ->method('getRecommendPagesForEachKnownPiwikPage')
            ->with($this->equalTo($piwikDatabaseResultSet))
            ->willReturn($expectedValue);
        
        $this->subject->expects($this->once())->method('init');
    
        $this->databaseConnection->expects($this->once())
            ->method('exec_TRUNCATEquery');
        
        $this->databaseConnection->expects($this->at(1))
            ->method('exec_INSERTmultipleRows')
            ->with(
                $this->equalTo('tx_recommendapage_domain_model_recommendedpage'),
                $this->equalTo(array('referrer_pid', 'target_pid')),
                $this->equalTo($expectedValue[0])
            )
            ->willReturn(true);
    
        $this->databaseConnection->expects($this->at(2))
            ->method('exec_INSERTmultipleRows')
            ->with(
                $this->equalTo('tx_recommendapage_domain_model_recommendedpage'),
                $this->equalTo(array('referrer_pid', 'target_pid')),
                $this->equalTo($expectedValue[1])
            )
            ->willReturn(true);
    
        $this->subject->expects($this->exactly(3))
            ->method('getDatabaseConnection')
            ->willReturn($this->databaseConnection);
        
        $this->assertSame(true, $this->subject->execute());
    }
}
