<?php
namespace JWeiland\RecommendAPage\Tests\Unit\Service;

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
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Tests\AccessibleObjectInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * PiwikDatabaseServiceTest
 */
class PiwikDatabaseServiceTest extends UnitTestCase
{
    /**
     * @var PiwikDatabaseService|\PHPUnit_Framework_MockObject_MockObject|AccessibleObjectInterface
     */
    protected $subject;
    
    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(PiwikDatabaseService::class, array('dummy'));
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
    public function getActionIdsAndUrlsWillReturnEmptyArrayWithDatabaseResultNull()
    {
        /** @var DatabaseConnection|\PHPUnit_Framework_MockObject_MockObject $databaseConnection */
        $databaseConnection = $this->createMock(DatabaseConnection::class);
        
        $databaseConnection->expects($this->exactly(3))->method('fullQuoteStr');
        $databaseConnection->expects($this->exactly(3))->method('escapeStrForLike');
        $databaseConnection->expects($this->once())->method('exec_SELECTgetRows')->with(
            'idaction, name',
            'piwik_log_action',
            $this->stringContains('OR')
        )->willReturn(null);
        
        $this->subject->_set('databaseConnection', $databaseConnection);
        
        $this->assertSame(array(), $this->subject->getActionIdsAndUrls());
    }
    
    /**
     * @test
     */
    public function getTargetIdActionsWillReturnEmptyArrayWithNull()
    {
        /** @var DatabaseConnection|\PHPUnit_Framework_MockObject_MockObject $databaseConnection */
        $databaseConnection = $this->createMock(DatabaseConnection::class);
        $databaseConnection->expects($this->never())->method('fullQuoteStr');
        
        $this->subject->_set('databaseConnection', $databaseConnection);
        
        $this->assertSame(array(), $this->subject->getTargetIdActions(null));
    }
    
    /**
     * @test
     */
    public function getTargetIdActionsWillReturnEmptyArrayWithDatabaseResultNull()
    {
        $idAction = '20';
        
        /** @var DatabaseConnection|\PHPUnit_Framework_MockObject_MockObject $databaseConnection */
        $databaseConnection = $this->createMock(DatabaseConnection::class);
        
        $databaseConnection->expects($this->once())->method('fullQuoteStr')
            ->with($idAction)
            ->willReturn('\'' . $idAction . '\'');
        
        $databaseConnection->expects($this->once())->method('exec_SELECTgetRows')
            ->with(
                'idaction_url as targetPid, COUNT(*) AS clicks',
                'piwik_log_link_visit_action',
                self::stringContains('\'20\''),
                'idaction_url_ref, idaction_url',
                'clicks DESC'
            )
            ->willReturn(null);
        
        $this->subject->_set('databaseConnection', $databaseConnection);
        
        $this->assertSame(array(), $this->subject->getTargetIdActions($idAction));
    }
}
