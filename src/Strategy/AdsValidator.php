<?php

declare(strict_types=1);

/**
 * This file is part of the ads-checker project.
 *
 * @copyright Eugenio Carocci.
 */

namespace App\Strategy;

/**
 * AdsValidator.
 */
class AdsValidator implements AdsValidatorInterface
{
    private const COMMENT_LINE_START_SYMBOL = '#';

    private int $minimumLinesInAdsFile;

    /**
     * The constructor method.
     */
    public function __construct(int $minimumLinesInAdsFile)
    {
        $this->minimumLinesInAdsFile = $minimumLinesInAdsFile;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAdsTxtContent(string $adsTxtContent): bool
    {
        $validLinesCounter = 0;

        $lines = preg_split('/\r\n|\r|\n/', $adsTxtContent);

        foreach ($lines as $line) {
            if ($line && !$this->isCommentLine($line)) {
                ++$validLinesCounter;
            }
        }

        return $validLinesCounter > $this->minimumLinesInAdsFile;
    }

    /**
     * Checks if input line is a comment one.
     */
    private function isCommentLine(string $line): bool
    {
        return self::COMMENT_LINE_START_SYMBOL === substr($line, 0, 1);
    }
}
