<?php

use Yasinpourraisi\Weatherapp\WeatherService;
use Dotenv\Dotenv;

require_once __DIR__ .'/vendor/autoload.php'; 

Dotenv::createImmutable(__DIR__)->safeLoad();

if ($argc < 2) {
    echo "Usage: php weather.php <city>\n";
    echo "Example: php weather.php London\n";
    exit(1);
}

$city = trim($argv[1]);

try {
    $weatherService = new WeatherService($_ENV['OPENWEATHER_API_KEY'] ?? '');

    echo "Getting weather for $city...\n";
    $weather = $weatherService->getWeather($city);

    echo "\n";
    echo "City: " . $weather['city'] . "\n";
    echo "Temperature: " . $weather['temperature'] . "°C\n";
    echo "Description: " . $weather['description'] . "\n";
    echo "Humidity: " . $weather['humidity'] . "%\n";
} catch (Throwable $exception) {
    fwrite(STDERR, "Error: {$exception->getMessage()}\n");
    exit(1);
}
?>