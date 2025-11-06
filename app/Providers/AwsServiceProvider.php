<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Sqs\SqsClient;

class AwsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DynamoDbClient::class, function ($app) {
            return new DynamoDbClient([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);
        });
        $this->app->singleton(SqsClient::class, function ($app) {
            return new SqsClient([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);
        });
    }

    public function boot()
    {
        //
    }
}
