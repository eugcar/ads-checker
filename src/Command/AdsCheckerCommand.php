<?php

declare(strict_types=1);

/**
 * This file is part of the ads-checker project.
 *
 * @copyright Eugenio Carocci.
 */

namespace App\Command;

use App\Exceptions\AdsFileNotPresentException;
use App\Service\AdsCheckerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * AdsCheckerCommand.
 */
class AdsCheckerCommand extends Command
{
    public const OUTPUT__FILE_NOT_PRESENT   = 'The given URL does not have ads.txt file';
    public const OUTPUT__VALID_ADS_FILE     = 'The ads.txt has more than %d lines';
    public const OUTPUT__NOT_VALID_ADS_FILE = 'The ads.txt has less than %d lines';

    public const ARGUMENT_URL = 'url';

    protected static $defaultName = 'app:ads-checker';

    private AdsCheckerService $adsCheckerService;

    private int $minimumLinesInAdsFile;

    /**
     * {@inheritdoc}
     */
    public function __construct(AdsCheckerService $adsCheckerService, int $minimumLinesInAdsFile)
    {
        parent::__construct();

        $this->adsCheckerService     = $adsCheckerService;
        $this->minimumLinesInAdsFile = $minimumLinesInAdsFile;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Check the ads.txt file on the provided URL.')
            ->addArgument(self::ARGUMENT_URL, InputArgument::REQUIRED, 'URL to check');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('This is the ads.txt checker command.');

        $url = $input->getArgument(self::ARGUMENT_URL);

        try {
            $isValid = $this->adsCheckerService->checkAdsTxtOnUrl($url);
        } catch (AdsFileNotPresentException $e) {
            $io->error(self::OUTPUT__FILE_NOT_PRESENT);

            return Command::FAILURE;
        }

        if ($isValid) {
            $io->text(sprintf(self::OUTPUT__VALID_ADS_FILE, $this->minimumLinesInAdsFile));
        } else {
            $io->text(sprintf(self::OUTPUT__NOT_VALID_ADS_FILE, $this->minimumLinesInAdsFile));
        }

        return Command::SUCCESS;
    }
}
