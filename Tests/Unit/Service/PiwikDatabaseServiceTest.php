<?php

/*
 * This file is part of the package jweiland/recommend_a_page.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\RecommendAPage\Tests\Unit\Service;

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
        $this->subject = $this->getAccessibleMock(PiwikDatabaseService::class, ['dummy']);
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

        $databaseConnection->expects(self::exactly(3))->method('fullQuoteStr');
        $databaseConnection->expects(self::exactly(3))->method('escapeStrForLike');
        $databaseConnection->expects(self::once())->method('exec_SELECTgetRows')->with(
            'idaction, name',
            'piwik_log_action',
            $this->stringContains('OR')
        )->willReturn(null);

        $this->subject->_set('databaseConnection', $databaseConnection);

        self::assertSame([], $this->subject->getActionIdsAndUrls());
    }

    /**
     * @test
     */
    public function getTargetIdActionsWillReturnEmptyArrayWithNull()
    {
        /** @var DatabaseConnection|\PHPUnit_Framework_MockObject_MockObject $databaseConnection */
        $databaseConnection = $this->createMock(DatabaseConnection::class);
        $databaseConnection->expects(self::never())->method('fullQuoteStr');

        $this->subject->_set('databaseConnection', $databaseConnection);

        self::assertSame([], $this->subject->getTargetIdActions(null));
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

        self::assertSame([], $this->subject->getTargetIdActions($idAction));
    }
}
