<?php

declare(strict_types=1);

/**
 * This file is part of the ads-checker project.
 *
 * @copyright Eugenio Carocci.
 */

namespace App\Strategy;

/**
 * AdsValidatorInterface.
 */
interface AdsValidatorInterface
{
    /**
     * Validates that the provided ads.txt content is valid.
     */
    public function validateAdsTxtContent(string $adsTxtContent): bool;
}
