<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Tests\Unit\Task;

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
            [
                'init',
                'getRecommendPagesForEachKnownPiwikPage',
                'getObjectManager',
                'getDatabaseConnection'
            ],
            [],
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
        $piwikDatabaseResultSet = [null, null];

        $this->piwikDatabaseService->expects(self::once())
            ->method('getActionIdsAndUrls')
            ->willReturn(
                $piwikDatabaseResultSet
            );

        $this->subject->_set(
            'piwikDatabaseService',
            $this->piwikDatabaseService
        );

        $this->databaseConnection->expects(self::once())
            ->method('exec_TRUNCATEquery');

        $this->subject->expects(self::exactly(1))
            ->method('getDatabaseConnection')
            ->willReturn($this->databaseConnection);

        $this->subject->expects(self::once())
            ->method('getRecommendPagesForEachKnownPiwikPage')
            ->with($this->equalTo($piwikDatabaseResultSet));

        $this->subject->execute();
    }

    /**
     * @test
     */
    public function executeWithValidDataWillReturnTrue()
    {
        $piwikDatabaseResultSet = [
            0 => [
                'idaction' => '0',
                'name' => 'https://jweiland.net/index.php'
            ],
            1 => [
                'idaction' => '201',
                'name' => 'http://jweiland.net/kontakt/impressum'
            ]
        ];

        $expectedValue = [
            0 => [
                'referrer_pid' => 0,
                'target_pid' => 20
            ],
            1 => [
                'referrer_pid' => 201,
                'target_pid' => 10
            ]
        ];

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
                $this->equalTo(['referrer_pid', 'target_pid']),
                $this->equalTo($expectedValue[0])
            )
            ->willReturn(true);

        $this->databaseConnection->expects($this->at(2))
            ->method('exec_INSERTmultipleRows')
            ->with(
                $this->equalTo('tx_recommendapage_domain_model_recommendedpage'),
                $this->equalTo(['referrer_pid', 'target_pid']),
                $this->equalTo($expectedValue[1])
            )
            ->willReturn(true);

        $this->subject->expects($this->exactly(3))
            ->method('getDatabaseConnection')
            ->willReturn($this->databaseConnection);

        self::assertSame(true, $this->subject->execute());
    }
}
