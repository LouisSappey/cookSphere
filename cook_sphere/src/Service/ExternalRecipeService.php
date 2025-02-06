<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExternalRecipeService
{
    private const API_BASE_URL = 'https://www.themealdb.com/api/json/v1/1';

    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    public function getRandomRecipe(): array
    {
        $response = $this->httpClient->request('GET', self::API_BASE_URL . '/random.php');
        return $response->toArray();
    }
} 