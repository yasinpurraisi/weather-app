<?php
namespace Yasinpourraisi\Weatherapp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class WeatherService {

    private Client $client;
    public function __construct(
        private readonly string $apikey,
        private readonly string $apiurl = 'https://api.openweathermap.org/data/2.5/weather'

    )
    {
        if ($this->apikey === '') {
            throw new \RuntimeException('OPENWEATHER_API_KEY is not configured. Add it to the .env file.');
        }

        $this->client = new Client();
    }
    public function getWeather(string $city): array {
        if (trim($city) === '') {
            throw new \InvalidArgumentException('City cannot be empty.');
        }

        try {
            $response = $this->client->get($this->apiurl, [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apikey,
                    'units' => 'metric',
                ],
            ]);
        } catch (ConnectException $exception) {
            throw new \RuntimeException(
                'Could not connect to the weather service. Check your internet connection.',
                0,
                $exception
            );
        } catch (RequestException $exception) {
            $statusCode = $exception->getResponse()?->getStatusCode();

            if ($statusCode === 404) {
                throw new \RuntimeException("City '$city' was not found.", 404, $exception);
            }

            if ($statusCode === 401) {
                throw new \RuntimeException('The OpenWeather API key is invalid.', 401, $exception);
            }

            if ($statusCode === 429) {
                throw new \RuntimeException('The weather service rate limit has been exceeded.', 429, $exception);
            }

            throw new \RuntimeException(
                'The weather service returned an unexpected error' .
                    ($statusCode !== null ? " (HTTP $statusCode)." : '.'),
                $statusCode ?? 0,
                $exception
            );
        }

        try {
            $weatherData = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $exception) {
            throw new \RuntimeException('The weather service returned invalid data.', 0, $exception);
        }

        if (!isset(
            $weatherData['name'],
            $weatherData['main']['temp'],
            $weatherData['main']['humidity'],
            $weatherData['weather'][0]['description']
        )) {
            throw new \RuntimeException('The weather service returned an incomplete response.');
        }

        return [
            'city' => $weatherData['name'],
            'temperature' => $weatherData['main']['temp'],
            'description' => $weatherData['weather'][0]['description'],
            'humidity' => $weatherData['main']['humidity'],
        ];
    }


}

?>