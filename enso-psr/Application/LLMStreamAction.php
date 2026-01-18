<?php declare(strict_types=1);

namespace Application;

use Application\Service\OpenRouter;
use Enso\Enso;
use Enso\Helpers\Runtime;
use Enso\Helpers\XTerm256;
use Enso\System\ActionHandler;
use Enso\System\WebRequest;
use Fp\Streams\Stream;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\PumpStream;
use function Fp\Collection\exists;
use function Fp\Collection\filter;

class LLMStreamAction extends ActionHandler
{
    public function __construct(
        protected ?Enso &$context = null,
        private ?OpenRouter $openRouter = null,
    ) {
        parent::__construct($context);
        $this->openRouter = new OpenRouter();
    }

    #[Route("/ai", methods: ["ClI"])]
    public function __invoke(): StreamInterface
    {
        $openRouter = $this->openRouter;
        $executableName = implode(' ', array_slice($this->getRequest()->getArguments(), 0, 2));
        $history = [
            [
                "role" => "system",
                "content" => "You are Enso CLI Agent Tool and you are executed now as `{$executableName}` with a CLI message from command line in a first user's message and an input stream body (if it's present, it comes via | pipe) in a second user's message. it could be both a message, or input with comment, or some input with instruction or a question. message can be just a message not a command.",
            ],
            [
                "role" => "system",
                "content" => "as an local agent, you can ask yourself to execute local commands."
                    . "To do this just output strictly in 3 lines. JSON-formatted (with escaped string if needed) data wrapped in markdown, like:\n"
                    . "```__consoleCall()\n" .
                    "[\"any-console-command\", \"--first-arg\", \"two\", ...]\n"
                    . "```\n"
                    . "Then if it is allowed it will be executed and a command and the result will be returned to you in next user's (!) message without any wrappings."
                    . "Do it only if needed to answer user."
            ],
        ];

        $reasoningMode = exists(
            explode(' ', $this->getRequest()->getArgumentsLine()),
            fn ($value) => $value === '-r' || $value === '--reasoning'
        );

        $arguments = array_slice($this->getRequest()->getArguments(), 2);

        $commandQuery = filter(
            $arguments,
            fn ($value) => $value !== '-r' && $value !== '--reasoning'
        );
        $commandQueryLine = implode(' ', $commandQuery);


        $inputBody = $this->getRequest()->getBody();

        $webQueryLine = urldecode($this->getRequest()->getUri()?->getQuery());

        $message = $this->getRequest() instanceof WebRequest
            ? $webQueryLine
            : $commandQueryLine;

        $responseGenerator = $this->callLLMRaw(
            message: "",
            history: $history = array_merge(
                $history,
                [
                    [
                        "role" => "user",
                        "content" => $message,
                    ],
                    [
                        "role" => "user",
                        "content" => $inputBody->getContents(),
                    ]
                ]
            ),
        );

        $responseText = "";
        $reasoningText = "";

        // if we are in CLI mode, and the output is piped, then we want to print the response
        // as it comes in, so that we can see the reasoning behind the model's response
        if ($reasoningMode && Runtime::isCLI() && Runtime::isOutputTty())
        {
            @ob_end_clean();

            foreach ($responseGenerator as $responseItem)
            {
                $reasoningDelta = $responseItem['choices'][0]['delta']['reasoning'] ?? null;
                $reasoningText .= $reasoningDelta;

                // print reasoning
                if ($reasoningMode && isset($reasoningDelta))
                {
                    print XTerm256::label(XTerm256::output("\033[3m") . $reasoningDelta ?? '', 0xc9643b);
                }

                $responseDelta = $responseItem['choices'][0]['delta']['content'];
                $responseText .= $responseDelta;

                // print real model response
                print $responseDelta ?? '';
                @flush();
            }

            echo "\n";
            exit (Runtime::EXIT_SUCCESS);
        }
        elseif (Runtime::isCLI())
        {
            $responseText = $responseGenerator
                ->map(static fn (array $sseResponse) => $sseResponse['choices'][0]['delta']['content'] ?? '')
                ->filter(static fn (string $line) => '' !== $line)
                ->mkString(sep: '');

            $responseTextLines = explode("\n", $responseText);
            $history = array_merge(
                $history,
                [[
                    "role" => "user",
                    "content" => $responseText,
                ]]
            );

            if (
                $responseTextLines[0] === '```__consoleCall()'
                && $responseTextLines[2] === '```'
            ) {
                usleep(250000);
                $command = (array) json_decode($responseTextLines[1], true);
                $commandLine = implode(' ', $command);

                print XTerm256::label("executing $ $commandLine", 0x64c93b);
                echo "\n";

                if (!$this->approve())
                {
                    exit (Runtime::EXIT_SUCCESS);
                }

                if (!empty($command) /*&& $command[0] == 'ls'*/)
                {
                    $commandOutput = shell_exec($commandLine);

                    echo "\n```\n" . $commandOutput . "\n```\n";

                    $functionResponse = $this->callLLM(
                        message: $commandOutput,
                        history: $history,
                    );

                    echo $functionResponse;
                    echo "\n";
                    exit (Runtime::EXIT_SUCCESS);
                }
            }

            echo $responseText;

            echo "\n";
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

    public function callLLMRaw(string $message, array $history = [], string $role = 'user'): Stream
    {
        if (!empty($message))
        {
            $history[] = [
                "role" => $role,
                "content" => $message,
            ];
        }

        return clone $this
            ->openRouter
            ->streamChatCompletions(
                model: getenv('OPENROUTER_API_MODEL') ?? 'openai/gpt-oss-20b:free',
                messages: $history,
            );
    }


    public function callLLM(string $message, array $history = [], string $role = 'user'): string
    {
        return $this
            ->callLLMRaw($message, $history, $role)
            ->map(static fn (array $sseResponse) => $sseResponse['choices'][0]['delta']['content'] ?? '')
            ->filter(static fn (string $line) => '' !== $line)
            ->mkString(sep: '');
    }

    function approve(string $question = 'approve?'): bool
    {
        if (!stream_isatty(STDIN))
        {
            return false;
        }

        fwrite(STDOUT, $question . ' [Enter = OK, Esc = Cancel] ');

        shell_exec('stty -icanon -echo');

        try
        {
            while (true)
            {
                $char = fread(STDIN, 1);

                if ($char === false || $char === '')
                {
                    continue;
                }

                $code = ord($char);

                return match ($code) {
                    10, 13 => fwrite(STDOUT, PHP_EOL) ? true : true,
                    27 => false,
                    default => false, // any other key
                };
            }
        }
        finally
        {
            shell_exec('stty sane');
        }
    }
}