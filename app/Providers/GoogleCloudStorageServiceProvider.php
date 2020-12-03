<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Google\Cloud\Storage\StorageClient;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use Illuminate\Support\ServiceProvider;

class GoogleCloudStorageServiceProvider extends ServiceProvider
{

    public function initClient()
    {
        if (env('APP_ENV', 'local') == 'production') {
            $config = [
                'keyFilePath' => 'public/assets/siyou-b2s-firebase-adminsdk-vsa6x-1fe7f56bb0.json'
            ];
        } else {
            $config = [
                'keyFile' => [
                    "type" => "service_account",
                    "project_id" => env('GOOGLE_PROJECT_ID', ''),
                    "private_key_id" => env('GOOGLE_PRIVATE_KEY_ID', ''),
                    "private_key" => env('GOOGLE_PRIVATE_KEY', ''),
                    "client_email" => "firebase-adminsdk-vsa6x@siyou-b2s.iam.gserviceaccount.com",
                    "client_id" => env('GOOGLE_CLIENT_ID', ''),
                    "auth_uri" => env('GOOGLE_AUTH_URI', ''),
                    "token_uri" => env('GOOGLE_TOKEN_URI', ''),
                    "auth_provider_x509_cert_url" => env('GOOGLE_AUTH_PROVIDER_CERT', ''),
                    "client_x509_cert_url" => env('GOOGLE_CLIENT_CERT', '')
                ]
            ];
        }
        $storage = new StorageClient($config);
        $bucket = $storage->bucket(env('GOOGLE_STORAGE_BUCKET', ''));
        return new GoogleStorageAdapter($storage, $bucket);
    }

    public function boot()
    {
        // if (env('APP_ENV', 'local') != 'production') {
            $currentConfig = [
                "keyFilePath" => base_path() . '/public/assets/siyou-b2s-firebase-adminsdk-vsa6x-4b0307f3e4.json'
            ];
        // } else {
            // $currentConfig = [
            //     'key_file' => [
            //         "type" => "service_account",
            //         "project_id" => env('GOOGLE_PROJECT_ID', ''),
            //         "private_key_id" => env('GOOGLE_PRIVATE_KEY_ID', ''),
            //         "private_key" => env('GOOGLE_PRIVATE_KEY', '-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCWT1emJFALqI1X\nb8++XguEAGhVx9L+ORaMdNcTmidrZEWoTTg1lf57UFZhOmdjVJiyn2dlWB5iLNCr\no2wCmak01jUiPku9iNI+2NbBDWR9i+5n5GjQnUTDaBaP16D3k/+HQOA0rDvk+yOa\nZhbUtkWI9pIf3V8e+boAm/EtQMKYPBJ18QNYLy/jRIMNpq0tqoXdOxHq9exU2/jt\nBDUUYaWgDgZ8xrOYBYKYSpqhh+penxJZBSYSKEKnXUiGGQOWULvYcQ9mMG3TGF+J\n8YLbk57/KH5pn7O9fHnonQ4xbwL6ge8eqxltUq2mcjpUHewXVmunraS+QKg6AOhD\nwMLofxPJAgMBAAECggEAEUtSveJ6tUSw8okCYZhngcocNxZ1P9vorMN6xZ4cf/47\nB4jO+VdnPt/4+FnkcGjY4uB1YJM5d7lJTx5P/KKadeJZnbWGOGoIKsP/PZohYFKb\n3SdKKgqWUVdmFSnNsv67zV+ZDGZuw8Njjs3FPbc1G/Omz3TEj4dTL+PBZaAIY8SE\ndaWKCZtJbL1cAg/nnB5oGJaFq1OCS15XKsNq1h/R4qvu1r1BwHm8nBSO5zXDe5X4\n0limQLO/puCgeCIalKtrUhFVUELE1eg1g8C5+GRR0+VXZCJfwCoqvdcfGhazYWFw\nEXS39ocJaBD3XWfs2+kdybv6WD5DGcvNRf3nsqgJEwKBgQDOp2p+j9cwXhteTuYq\n8abEepQIwRYsTQdFqqSiK1u7cYJOtfU5o0mXUMZ+SKvvvxVgBLuWAAQkEYNQKhz3\n1DnpY4YEBQXUCnZfbResMZBgypfcPKfWuuby9g6+50vepWAFzTgHFe3tJ828q8gn\njHLJsfyhtUNznH2bUc4y26PiAwKBgQC6M6md4jg4SJ5DPCHfGpcgBWv4lYpYRD2F\nCGadd5/yE1Lz/4LzSRD1/y4MS++HFFgqq9QTT0SdVVqcpsp4vtAfHcNlFtjYiMnP\nOdpfyL6bCMfcQ9MVGBx28JdpCJYmC1TSOALeoI5KwKmi+RNEhcwqnRKqln0zzGsK\nel6aJfRPQwKBgGWAucAcarfpIUw7OLaTJzSOeLOsE3YDB7ViUacN4Oq6oCSFH0QG\nPhlCpZxVmY4Sry8QZAsTSvVkXLk9ViksZp2KMsM6rFgwI2BPtzbuyVmhaOHWMSI5\nf0KLNUbzWMKtO/Ldj817Kfg4esdXIOK8C42iDNJotA7PKKrnjX5Rf7d9AoGBAKn8\nAeudmn9fZAOzQKTBTJex+2ibdAmyeosZKTy1+XFwTLltWGmrGW3JwIr/Q7MIUpjZ\n9qiOI/CR/D+oUIy8fZj6feeqXNoIvluM0BvDUqaL9JDT5j6KnWnDqCX97bzbeVrU\nQWJmJqB9lB+wzPMwRjvc2y6aZcBE7K9LGVQWNBurAoGAJzigYHjAd2THZbHI+MEc\n+g+crFyeWoeI/8PAdWfVcTyWyZRbVjP8oVVAD9J9r1rnzAVVSLS5az1bE+bqpycw\nEmd7Mw6dqrM7nBYnyBDcFIyF2WoMsGFOCqPV2O49gsNxRPcf6T11bBIQYh2mALe1\n6UHPs1DcQclcae7JE+mPi4I=\n-----END PRIVATE KEY-----\n'),
            //         "client_email" => "firebase-adminsdk-vsa6x@siyou-b2s.iam.gserviceaccount.com",
            //         "client_id" => env('GOOGLE_CLIENT_ID', ''),
            //         "auth_uri" => env('GOOGLE_AUTH_URI', ''),
            //         "token_uri" => env('GOOGLE_TOKEN_URI', ''),
            //         "auth_provider_x509_cert_url" => env('GOOGLE_AUTH_PROVIDER_CERT', ''),
            //         "client_x509_cert_url" => env('GOOGLE_CLIENT_CERT', '')
            //     ]
            // ];
        // }
        Storage::extend('google', function ($app, $config) use ($currentConfig) {
            $client = new StorageClient($currentConfig);
            $bucket = $client->bucket(env('GOOGLE_STORAGE_BUCKET', ''));
            return new Filesystem(new GoogleStorageAdapter($client, $bucket));
        });
    }

    public function register()
    {
        //
    }
}
