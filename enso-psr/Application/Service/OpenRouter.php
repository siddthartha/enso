<?php

namespace Application\Service;

use Fp\Collections\Seq;
use Fp\Functional\Option\Option;
use Fp\Streams\Stream as FPStream;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\StreamInterface;
use Yiisoft\Arrays\ArrayHelper;

class OpenRouter
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = 'https://openrouter.ai/api/v1';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = match (
            $_apiKey = match ($apiKey)
            {
                null => getenv('OPENROUTER_API_KEY'),
                default => $apiKey,
            }
        ) {
            false => throw new \InvalidArgumentException('OpenRouter API key is not set'),
            default => $_apiKey,
        };

        $this->client = new Client([
            'base_uri' => "https://openrouter.ai/api/v1",
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Non-streaming version of chat completions
     *
     * @param string $model
     * @param array $messages
     * @param array $options
     * @return array
     * @throws GuzzleException
     */
    public function chatCompletions(
        string $model,
        array $messages,
        array $options = []
    ): array {

        try {
            $response = $this
                ->client
                ->request(
                    method: 'POST',
                    uri: 'https://openrouter.ai/api/v1/chat/completions',
                    options: [
                        'json' => [
                            'model' => "openai/gpt-oss-20b:free", //$model,
                            'messages' => $messages,
                            'stream' => false,
                        ],
                    ]
                );

            return json_decode(
                json: $response->getBody()->getContents(),
                associative: true,
                depth: 12,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (RequestException $e) {
            throw $e;
        }
    }

    /**
     * Call the OpenRouter API and return a streaming response
     *
     * @param string $model The model to use (e.g., 'openai/gpt-4o')
     * @param array $messages Array of message objects with role and content
     * @param array $options Additional options for the API call
     * @return \Generator The response stream generator
     * @throws GuzzleException
     */
    public function streamChatCompletions(
        string $model,
        array $messages,
        array $options = []
    ): array {
        $defaultOptions = [
            'model' => $model,
            'messages' => $messages,
            'stream' => true, // Enable streaming according to the OpenRouter API docs @see
        ];

        $requestBody = ArrayHelper::merge($defaultOptions, $options);

        try {
            $response = $this->client
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'json' => $requestBody,
                ]);

            return $this->parseJsonLinesSSE(
                $response->getBody()
            );
        } catch (RequestException $e) {
            // Log the error or handle it as needed
            throw $e;
        }
    }

    /**
     * @param StreamInterface $psrStream
     * @param int $chunkSize
     * @return FPStream
     */
    function fromPsrStream(StreamInterface $psrStream, int $chunkSize = 4096): FPStream
    {
        return FPStream::emits(
            (function () use ($psrStream, $chunkSize) {
                while (!$psrStream->eof()) {
                    $chunkBuffer = $psrStream->read($chunkSize);
                    foreach(str_split($chunkBuffer) as $char)
                    {
                        yield $char;
                    }
                }
            })
            ()
        );
    }

    /**
     * @return list<array>
     */
    function parseJsonLinesSSE(StreamInterface $stream): array
    {

        return $this->fromPsrStream($stream)
            ->groupAdjacentBy(fn ($char) => PHP_EOL === $char)
            ->map(fn (array $pair) => $pair[1])
            ->map(fn (Seq $line) => $line->mkString(sep: ''))
//            ->filter(fn (string $line) => '' !== $line) /* filter empty line */
            ->filter(fn (string $line) => str_starts_with($line, 'data: ')) /* not starts from 'data: ' */
            ->map(fn (string $line) => substr($line, 6)) /* remove 'data: ' */
            ->filter(fn (string $line) => '[DONE]' !== $line) /* filter terminal line */
//            ->filterMap(fn (string $line) => $this->parseFoo($line)) // @TODO: parse on by one
            ->toList();
    }

    /**
     * @param string $json
     * @return Option<array>
     */
    function parseFoo(string $json): Option
    {
        return Option::try(fn() => json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR));
    }
}