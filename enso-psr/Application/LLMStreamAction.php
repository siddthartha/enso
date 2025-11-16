<?php

namespace Application;

use Application\Service\OpenRouter;
use Enso\System\ActionHandler;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

class LLMStreamAction extends ActionHandler
{
    #[Route("/ai", methods: ["ClI"])]
    public function __invoke(): StreamInterface
    {
        $openRouter = new OpenRouter();

        $response = $openRouter->streamChatCompletions(
            model: getenv('OPENROUTER_API_MODEL', 'openai/gpt-oss-20b:free'),
            messages: [
                [
                    "role" => "user",
                    "content" => $this->getRequest()->hasArguments()
                        ? $this->getRequest()->getArgumentsLine()
                        : $this->getRequest()->getBody()->getContents()
                ],
            ]
        );

        foreach ($response as $item)
        {
            echo $item['choices'][0]['delta']['content'];
            @ob_end_flush();
        }

        exit(0);

//        return Utils::streamFor(
//            $response
//                ->map(fn (array $sseResponse) => $sseResponse['choices'][0]['delta']['content'])
//                ->filter(fn (string $line) => '' !== $line)
//                ->getIterator(),
//            ['size' => 64]
//        );
    }
}