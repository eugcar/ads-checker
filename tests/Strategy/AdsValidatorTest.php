<?php

declare(strict_types=1);

namespace App\Tests\Strategy;

use App\Strategy\AdsValidator;
use PHPUnit\Framework\TestCase;

/**
 * This file is part of the ads-checker project.
 *
 * @copyright Eugenio Carocci.
 */

/**
 * AdsValidatorTest.
 *
 * @coversDefaultClass \App\Strategy\AdsValidator
 */
class AdsValidatorTest extends TestCase
{
    private int $minimumLinesInAdsFile;

    private AdsValidator $adsValidator;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->minimumLinesInAdsFile = 5;

        $this->adsValidator = new AdsValidator($this->minimumLinesInAdsFile);
    }

    /**
     * Tests that the ads.txt content is correctly validated.
     *
     * @covers ::validateAdsTxtContent
     */
    public function testValidateAdsTxtContent(): void
    {
        $adsTxtContentNotValid = "#ANSA\r\ngoogle.com, pub-9998744094739872, DIRECT, f08c47fec0942fa0\r\n#google.com, pub-2720933683151214, DIRECT, f08c47fec0942fa0\r\n#google.com, pub-9998744094739872, DIRECT, f08c47fec0942fa0\r\ngoogle.com, pub-2538762546398839, DIRECT, f08c47fec0942fa0\r\nappnexus.com, 2579, DIRECT #native\r\nquantum-advertising.com,  1343, DIRECT #native\r\n";
        $adsTxtContentValid    = "#ANSA\r\ngoogle.com, pub-9998744094739872, DIRECT, f08c47fec0942fa0\r\ngoogle.com, pub-2720933683151214, DIRECT, f08c47fec0942fa0\r\ngoogle.com, pub-9998744094739872, DIRECT, f08c47fec0942fa0\r\ngoogle.com, pub-2538762546398839, DIRECT, f08c47fec0942fa0\r\nappnexus.com, 2579, DIRECT #native\r\nquantum-advertising.com,  1343, DIRECT #native\r\n";

        $this->assertFalse($this->adsValidator->validateAdsTxtContent($adsTxtContentNotValid));
        $this->assertTrue($this->adsValidator->validateAdsTxtContent($adsTxtContentValid));
    }
}
