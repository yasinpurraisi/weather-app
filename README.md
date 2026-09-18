# Weather App

A PHP command-line application that retrieves current weather information from OpenWeatherMap.

## Usage

Pass the city name as the first command-line argument:

```powershell
php weather.php London
```

Example output:

```text
Getting weather for London...

City: London
Temperature: 18.42C
Description: clear sky
Humidity: 72%
```

Cities containing spaces should be quoted:

```powershell
php weather.php "New York"
```

