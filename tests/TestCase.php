<?php

namespace Tests;

use Faker\Generator;
use Faker\Provider\Color;
use Faker\Provider\DateTime;
use Faker\Provider\Lorem;
use Faker\Provider\pt_BR\Address;
use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Internet;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\PhoneNumber;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{

    protected $faker;

    protected $allowedHosts = ['', ':memory:', '127.0.0.1', 'localhost', 'mongo'];

    protected $client;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';

        $this->protectDatabase();

        return $app;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = new Generator();
        $this->faker->addProvider(new Company($this->faker));
        $this->faker->addProvider(new PhoneNumber($this->faker));
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Address($this->faker));
        $this->faker->addProvider(new Internet($this->faker));
        $this->faker->addProvider(new DateTime($this->faker));
        $this->faker->addProvider(new Lorem($this->faker));
        $this->faker->addProvider(new Color($this->faker));
        $this->faker->addProvider(new Uuid($this->faker));

        $this->beforeApplicationDestroyed(function () {
            $this->app['db']->connection('mongodb-test')->drop();
        });

        $clientServiceConfig = config('apiServices.pitzi');
        $cliente = $clientServiceConfig['client'];

        $url = $cliente['base_uri'];
        $usuario = $cliente['usuario'];
        $password = $cliente['password'];

        $this->client = Http::withHeaders(self::headers())
            ->baseUrl($url)
            ->withBasicAuth($usuario, $password);
    }

    private function protectDatabase()
    {
        $hosts = config('database.connections.mongodb.host');

        foreach ($hosts as $host) {
            if (!in_array($host, $this->allowedHosts)) {
                throw new RuntimeException("$host not allowed. Please change your phpunit.xml setup.");
            }
        }

        $hosts = config('database.connections.mongodb.host');

        foreach ($hosts as $host) {
            if (!in_array($host, $this->allowedHosts)) {
                throw new RuntimeException("$host not allowed. Please change your phpunit.xml setup.");
            }
        }
    }

    public static function headers(array $headers = [])
    {
        return empty($headers) ? [
            "Content-Type" => "application/json; charset=utf-8",
            "Accept" => "application/json"] : $headers;
    }
}
