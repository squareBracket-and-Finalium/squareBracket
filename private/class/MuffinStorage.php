<?php

namespace Orange;

use Symfony\Component\HttpClient\HttpClient;

class MuffinStorage implements Storage
{
    private \Symfony\Contracts\HttpClient\HttpClientInterface $muffinClient;
    private BunnyStorage $bunnyFallback;

    public function __construct(Orange $orange) {
        global $muffinSettings;

        $this->muffinClient = HttpClient::create([
            'base_uri' => $muffinSettings["muffURL"],
            'extra' => ['key' => $muffinSettings["muffAPI"]],
        ]);
        $this->bunnyFallback = new BunnyStorage($orange);
    }

    public function processVideo($new, $target_file) {
        // Video processing is still done under BunnyCDN due to its reliability.
        $this->bunnyFallback->processVideo($new, $target_file);
    }

    public function getVideoThumbnail($id) {
        return false;
    }

    public function getImageThumbnail($id) {
        return false;
    }

    public function fileExists($file) {
        $response = $this->muffinClient->request('GET', '/check_file.php', [
            'query' => [
                'file' => $file,
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode == 200) {
            return true;
        } else {
            return false;
        }
    }

    public function uploadImage($temp_name, $target_file, $format, $resize = false, $width = 0, $height = 0) {
        return false;
    }

    public function processImage($temp_name, $new) {
        return false;
    }
}