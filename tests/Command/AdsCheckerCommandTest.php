<?php

declare(strict_types=1);

/**
 * This file is part of the ads-checker project.
 *
 * @copyright Eugenio Carocci.
 */

namespace App\Tests\Command;

use App\Command\AdsCheckerCommand;
use App\Exceptions\AdsFileNotPresentException;
use App\Service\AdsCheckerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * AdsCheckerCommandTest.
 *
 * @coversDefaultClass \App\Command\AdsCheckerCommand
 */
class AdsCheckerCommandTest extends TestCase
{
    /**
     * @var AdsCheckerService|MockObject
     */
    private AdsCheckerService $adsCheckerService;

    private int $minimumLinesInAdsFile;

    private AdsCheckerCommand $adsCheckerCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->adsCheckerService     = $this->createMock(AdsCheckerService::class);
        $this->minimumLinesInAdsFile = 5;

        $this->adsCheckerCommand = new AdsCheckerCommand($this->adsCheckerService, $this->minimumLinesInAdsFile);
    }

    /**
     * Tests that the command exit with error code when ads.txt file is not present
     * on the provided URL.
     *
     * @covers ::execute
     */
    public function testCommandWhenAdsTxtFileNotPresent(): void
    {
        $url = 'https://www.ansa.it';

        $this->adsCheckerService
            ->expects($this->once())
            ->method('checkAdsTxtOnUrl')
            ->with($url)
            ->willThrowException(new AdsFileNotPresentException());

        $commandTester = new CommandTester($this->adsCheckerCommand);
        $commandTester->execute([
            AdsCheckerCommand::ARGUMENT_URL => $url
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString(AdsCheckerCommand::OUTPUT__FILE_NOT_PRESENT, $commandTester->getDisplay());
    }

    /**
     * Tests that the command provides an error message if the ads.txt file is not valid.
     *
     * @covers ::execute
     */
    public function testCommandWhenAdsTxtFileNotValid(): void
    {
        $url = 'https://www.ansa.it';

        $this->adsCheckerService
            ->expects($this->once())
            ->method('checkAdsTxtOnUrl')
            ->with($url)
            ->willReturn(false);

        $commandTester = new CommandTester($this->adsCheckerCommand);
        $commandTester->execute([
            AdsCheckerCommand::ARGUMENT_URL => $url
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString(sprintf(AdsCheckerCommand::OUTPUT__NOT_VALID_ADS_FILE, $this->minimumLinesInAdsFile), $commandTester->getDisplay());
    }

    /**
     * Tests that the command provides a success message if the ads.txt file is valid.
     *
     * @covers ::execute
     */
    public function testCommandWhenAdsTxtFileIsValid(): void
    {
        $url = 'https://www.ansa.it';

        $this->adsCheckerService
            ->expects($this->once())
            ->method('checkAdsTxtOnUrl')
            ->with($url)
            ->willReturn(true);

        $commandTester = new CommandTester($this->adsCheckerCommand);
        $commandTester->execute([
            AdsCheckerCommand::ARGUMENT_URL => $url
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString(sprintf(AdsCheckerCommand::OUTPUT__VALID_ADS_FILE, $this->minimumLinesInAdsFile), $commandTester->getDisplay());
    }
}
