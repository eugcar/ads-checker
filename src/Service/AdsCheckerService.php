<?php

declare(strict_types=1);

/**
 * This file is part of the ads-checker project.
 *
 * @copyright Eugenio Carocci.
 */

namespace App\Service;

use App\Exceptions\AdsFileNotPresentException;
use App\Exceptions\NotValidUrlException;
use App\Strategy\AdsValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * AdsCheckerService.
 */
class AdsCheckerService
{
    public const ADS_URL = '%s/ads.txt';

    private HttpClientInterface $client;

    private AdsValidatorInterface $adsValidator;

    /**
     * The constructor method.
     */
    public function __construct(HttpClientInterface $client, AdsValidatorInterface $adsValidator)
    {
        $this->client       = $client;
        $this->adsValidator = $adsValidator;
    }

    /**
     * Counts lines in a ADS file.
     *
     * @throws AdsFileNotPresentException
     * @throws TransportExceptionInterface
     */
    public function checkAdsTxtOnUrl(string $url): bool
    {
        $hostname = $this->getHostNameFromUrl($url);

        $response = $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ADS_URL, $hostname)
        );

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            throw new AdsFileNotPresentException();
        }

        return $this->adsValidator->validateAdsTxtContent($response->getContent());
    }

    /**
     * Gets hostname from url, if valid.
     *
     * @throws NotValidUrlException
     */
    private function getHostNameFromUrl(string $url): ?string
    {
        $scheme   = null;
        $hostname = null;

        $url = filter_var($url, FILTER_SANITIZE_URL);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new NotValidUrlException();
        }

        $scheme   = parse_url($url, PHP_URL_SCHEME);
        $hostname = parse_url($url, PHP_URL_HOST);

        return $scheme.'://'.$hostname;
    }
}
