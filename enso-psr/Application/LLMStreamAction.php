<?php

namespace Application;

use Application\Service\OpenRouter;
use Enso\Helpers\Runtime;
use Enso\Helpers\XTerm256;
use Enso\System\ActionHandler;
use Enso\System\WebRequest;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\PumpStream;

class LLMStreamAction extends ActionHandler
{
    #[Route("/ai", methods: ["ClI"])]
    public function __invoke(): StreamInterface
    {
        $openRouter = new OpenRouter();

        $message = $this->getRequest() instanceof WebRequest
            ? urldecode($this->getRequest()->getUri()?->getQuery())
            : ($this->getRequest()->hasArguments()
                ? $this->getRequest()->getArgumentsLine() . " " . $this->getRequest()->getBody()->getContents()
                : $this->getRequest()->getBody()->getContents()
            );

        $responseGenerator = $openRouter->streamChatCompletions(
            model: getenv('OPENROUTER_API_MODEL', 'openai/gpt-oss-20b:free'),
            messages: [
                [
                    "role" => "user",
                    "content" => $message,
                ],
            ]
        );

        // if we are in CLI mode, and the output is piped, then we want to print the response
        // as it comes in, so that we can see the reasoning behind the model's response
        if (Runtime::isCLI() && Runtime::isOutputTty())
        {
            @ob_end_clean();

            foreach ($responseGenerator as $responseItem)
            {
                // print reasoning
                if (isset($responseItem['choices'][0]['delta']['reasoning']))
                {
                    print XTerm256::label($responseItem['choices'][0]['delta']['reasoning'] ?? '', 0x555533);
                }

                // print real model response
                print $responseItem['choices'][0]['delta']['content'] ?? '';
                @flush();
            }

            exit (Runtime::EXIT_SUCCESS);
        }

        $responseIterator = $responseGenerator
            ->map(static fn (array $sseResponse) => $sseResponse['choices'][0]['delta']['content'] ?? '')
            ->filter(static fn (string $line) => '' !== $line)
            ->getIterator();

        return new PumpStream(function () use ($responseIterator)
        {
            if (!$responseIterator->valid())
            {
                return false;
            }
            $result = $responseIterator->current();
            $responseIterator->next();

            return $result;
        }, []);
    }
}