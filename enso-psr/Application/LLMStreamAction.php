<?php

namespace Application;

use Application\Service\OpenRouter;
use Enso\Helpers\Ansi;
use Enso\Helpers\Runtime;
use Enso\Helpers\XTerm256;
use Enso\System\ActionHandler;
use Enso\System\WebEmitter;
use Enso\System\WebRequest;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Console\Color;

class LLMStreamAction extends ActionHandler
{
    #[Route("/ai", methods: ["ClI"])]
    public function __invoke(): StreamInterface
    {
        $openRouter = new OpenRouter();

        $query = $this->getRequest() instanceof WebRequest
            ? urldecode($this->getRequest()->getUri()?->getQuery())
            : ($this->getRequest()->hasArguments()
                ? $this->getRequest()->getArgumentsLine()
                : $this->getRequest()->getBody()->getContents()
            );

        $response = $openRouter->streamChatCompletions(
            model: getenv('OPENROUTER_API_MODEL', 'openai/gpt-oss-20b:free'),
            messages: [
                [
                    "role" => "user",
                    "content" => $query,
                ],
            ]
        );

        if (Runtime::isCLI() && Runtime::isPiped())
        {
            @ob_end_clean();

            foreach ($response as $item)
            {
                // print reasoning
                print XTerm256::color(0x555533)
                    . ($item['choices'][0]['delta']['reasoning'] ?? '')
                    . XTerm256::clear();

                // print real model response
                print XTerm256::color(0xffffff)
                    . ($item['choices'][0]['delta']['content'] ?? '')
                    . XTerm256::clear();
                @ob_end_flush();
            }

            exit (Runtime::EXIT_SUCCESS);

        }

        return Utils::streamFor(
            $response
                ->map(fn (array $sseResponse) => $sseResponse['choices'][0]['delta']['content'])
                ->filter(fn (string $line) => '' !== $line)
                ->getIterator(),
        );
    }
}