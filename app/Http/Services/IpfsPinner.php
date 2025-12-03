<?php

namespace App\Services;

use GuzzleHttp\Client;

class IpfsPinner
{
    protected Client $http;

    protected string $provider;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 30]);
        $this->provider = config('app.ipfs_provider', env('IPFS_PROVIDER', 'pinata'));
    }

    public function pinFile(string $localPath, string $filename): string
    {
        return match ($this->provider) {
            'infura' => $this->pinInfura($localPath),
            default => $this->pinPinata($localPath, $filename),
        };
    }

    protected function pinPinata(string $localPath, string $filename): string
    {
        $endpoint = 'https://api.pinata.cloud/pinning/pinFileToIPFS';
        $headers = [];

        if ($jwt = env('PINATA_JWT')) {
            $headers['Authorization'] = "Bearer {$jwt}";
        }

        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($localPath, 'r'),
                'filename' => $filename,
            ],
            [
                'name' => 'pinataOptions',
                'contents' => json_encode(['cidVersion' => 1]),
            ],
        ];

        if (! $jwt) {
            $multipart[] = [
                'name' => 'pinata_api_key',
                'contents' => env('PINATA_API_KEY'),
            ];
            $multipart[] = [
                'name' => 'pinata_secret_api_key',
                'contents' => env('PINATA_API_SECRET'),
            ];
        }

        $res = $this->http->post($endpoint, [
            'headers' => $headers,
            'multipart' => $multipart,
        ]);
        $body = json_decode((string) $res->getBody(), true);

        return $body['IpfsHash'] ?? throw new \RuntimeException('Pinata: Missing IpfsHash');
    }

    protected function pinInfura(string $localPath): string
    {
        $endpoint = env('INFURA_IPFS_ENDPOINT', 'https://ipfs.infura.io:5001/api/v0/add');
        $res = $this->http->post($endpoint, [
            'auth' => [env('INFURA_PROJECT_ID'), env('INFURA_PROJECT_SECRET')],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($localPath, 'r'),
                ],
            ],
        ]);
        $bodyRaw = (string) $res->getBody();
        // Infura returns plain lines; find the last line with "Hash"
        $data = json_decode($bodyRaw, true);

        return $data['Hash'] ?? throw new \RuntimeException('Infura: Missing Hash');
    }
}
