<?php

namespace App\Services\Translators;

use App\Contracts\TranslatorContract;
use App\DTO\TranslatorResponseDTO;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DeeplTranslator implements TranslatorContract
{
    private string $secret;

    public function __construct()
    {
        $this->secret = config('services.deepl.secret');
    }

    public function make(string $text, string $language): TranslatorResponseDTO
    {
        $url = 'https://api-free.deepl.com/v2/translate';
        $data = [
            'target_lang' => $language,
            'text' => [$text],
        ];

        $response = Http::withHeader('Authorization', "DeepL-Auth-Key $this->secret")->post($url, $data);

        if ($response->status() != 200) {
            // dd($response->status(), $response->json(), $response->body());
            \Log::warning('[TRANSLATOR ERROR]', [
                'translator' => 'DeepL',
                'url' => $url,
                'data' => $data,
                'response' => $response,
                'status' => $response->status(),
            ]);

            throw new HttpException(400, 'Translator service error.');
        }

        $response = $response->json();

        $translatedText = $response['translations'][0]['text'];

        return TranslatorResponseDTO::fromArray([
            'result' => $translatedText,
        ]);
    }
}
