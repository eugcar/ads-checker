<?php

declare(strict_types=1);

/**
 * This file is part of the ads-checker project.
 *
 * @copyright Eugenio Carocci.
 */

namespace App\Tests\Service;

use App\Exceptions\AdsFileNotPresentException;
use App\Exceptions\NotValidUrlException;
use App\Service\AdsCheckerService;
use App\Strategy\AdsValidatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * AdsCheckerServiceTest.
 *
 * @coversDefaultClass AdsCheckerService
 */
class AdsCheckerServiceTest extends TestCase
{
    /**
     * @var HttpClientInterface|MockObject
     */
    private HttpClientInterface $client;

    /**
     * @var AdsValidatorInterface|MockObject
     */
    private AdsValidatorInterface $adsValidator;

    private AdsCheckerService $adsCheckerService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->client       = $this->createMock(HttpClientInterface::class);
        $this->adsValidator = $this->createMock(AdsValidatorInterface::class);

        $this->adsCheckerService = new AdsCheckerService($this->client, $this->adsValidator);
    }

    /**
     * Tests that an exception is thrown if the provided URL is not valid.
     *
     * @covers ::checkAdsTxtOnUrl
     */
    public function testCheckAdsTxtOnUrlWhenNotValidUrl(): void
    {
        $url = 'invalid-url';

        $this->client
            ->expects($this->never())
            ->method('request');

        $this->adsValidator
            ->expects($this->never())
            ->method('validateAdsTxtContent');

        $this->expectException(NotValidUrlException::class);

        $this->adsCheckerService->checkAdsTxtOnUrl($url);
    }

    /**
     * Tests that an exception is thrown if the provided URL does not provides the ads.txt file.
     *
     * @covers ::checkAdsTxtOnUrl
     */
    public function testCheckAdsTxtOnUrlWhenAdsTxtNotPresent(): void
    {
        $url      = 'https://www.ansa.it/sito/notizie/cronaca/cronaca.shtml';
        $hostname = 'https://www.ansa.it';

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_NOT_FOUND);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with(Request::METHOD_GET, sprintf(AdsCheckerService::ADS_URL, $hostname))
            ->willReturn($response);

        $this->adsValidator
            ->expects($this->never())
            ->method('validateAdsTxtContent');

        $this->expectException(AdsFileNotPresentException::class);

        $this->adsCheckerService->checkAdsTxtOnUrl($url);
    }

    /**
     * Tests that the ads.txt file on the provided URL is correctly checked.
     *
     * @covers ::checkAdsTxtOnUrl
     */
    public function testCheckAdsTxtOnUrl(): void
    {
        $url      = 'https://www.ansa.it/sito/notizie/cronaca/cronaca.shtml';
        $hostname = 'https://www.ansa.it';

        $responseContent = 'Valid Content';

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($responseContent);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with(Request::METHOD_GET, sprintf(AdsCheckerService::ADS_URL, $hostname))
            ->willReturn($response);

        $this->adsValidator
            ->expects($this->once())
            ->method('validateAdsTxtContent')
            ->with($responseContent)
            ->willReturn(true);

        $this->assertTrue($this->adsCheckerService->checkAdsTxtOnUrl($url));
    }
}
