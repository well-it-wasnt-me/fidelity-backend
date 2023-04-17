<?php
namespace App\Moebius;

/**
 * Class Definition
 * @package App\Moebius
 */
final class ChatGPT {
    private $api_key;
    private $api_url;

    private $headers;

    function __construct()
    {
        $this->api_key = getenv('CHATGPT_KEY');
        $this->api_url = 'https://api.openai.com/v1/engines/davinci-codex/completions';
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        ];
    }

    public function askQuestion($question){
        $client = new \GuzzleHttp\Client();

        $response = $client->post($this->api_url, [
            'headers' => $this->headers,
            'json' => [
                'prompt' => $question,
                'max_tokens' => 50,
                'temperature' => 0.7
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['choices'][0]['text'];
    }
}