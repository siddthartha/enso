<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\System\ActionHandler;
use Enso\System\Template;
use GuzzleHttp\Psr7\BufferStream;
use HttpSoft\Message\Response as PSRResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Description of DocsAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class DocsAction extends ActionHandler
{

    /**
     * @OA\Get(
     *     path="/default/docs",
     *     @OA\Response(response="200", description="Just some action")
     * )
     */
    #[Route("/default/docs", methods: ["GET"])]
    public function __invoke(): ResponseInterface
    {
        $readme = file_get_contents(__DIR__ . '/../docs/README.md');
        $html = (new \ParsedownExtra())
            ->text($readme);

        $body = new BufferStream();
        $body->write(
            (new Template(__DIR__ . '/views/docs.php'))
            ->render(
                vars: compact('html')
            )
        );

        return (new PSRResponse())
            ->withHeader('Content-type', 'text/html; charset=utf-8')
            ->withBody($body);
    }
}