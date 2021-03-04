<?php
namespace JWeiland\RecommendAPage\Tests\Unit\Domain\Model;

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
use JWeiland\RecommendAPage\Domain\Model\RecommendedPage;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * UriResolverUtilityTest
 */
class RecommendedPageTest extends UnitTestCase
{
    /**
     * @var RecommendedPage
     */
    protected $subject;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->subject = new RecommendedPage();
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
    public function getReferrerPidInitiallyReturnsZero()
    {
        self::assertSame(
            0,
            $this->subject->getReferrerPid()
        );
    }

    /**
     * @test
     */
    public function setReferrerPidSetsReferrerPid()
    {
        $this->subject->setReferrerPid(123456);

        self::assertSame(
            123456,
            $this->subject->getReferrerPid()
        );
    }

    /**
     * @test
     */
    public function setReferrerPidWithStringResultsInInteger()
    {
        $this->subject->setReferrerPid('123Test');

        self::assertSame(
            123,
            $this->subject->getReferrerPid()
        );
    }

    /**
     * @test
     */
    public function setReferrerPidWithBooleanResultsInInteger()
    {
        $this->subject->setReferrerPid(TRUE);

        self::assertSame(
            1,
            $this->subject->getReferrerPid()
        );
    }

    /**
     * @test
     */
    public function getTargetPidInitiallyReturnsZero()
    {
        self::assertSame(
            0,
            $this->subject->getTargetPid()
        );
    }

    /**
     * @test
     */
    public function setTargetPidSetsTargetPid()
    {
        $this->subject->setTargetPid(123456);

        self::assertSame(
            123456,
            $this->subject->getTargetPid()
        );
    }

    /**
     * @test
     */
    public function setTargetPidWithStringResultsInInteger()
    {
        $this->subject->setTargetPid('123Test');

        self::assertSame(
            123,
            $this->subject->getTargetPid()
        );
    }

    /**
     * @test
     */
    public function setTargetPidWithBooleanResultsInInteger()
    {
        $this->subject->setTargetPid(TRUE);

        self::assertSame(
            1,
            $this->subject->getTargetPid()
        );
    }
}
