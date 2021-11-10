<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\System\ActionHandler;
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
        $html = (new \ParsedownExtra())->text($readme);

        $body = new BufferStream();
        $body->write('<html lang="en"><head><title>Docs</title>'
            . '<meta name="color-scheme" content="light dark" />'
            . '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexboxgrid/6.3.1/flexboxgrid.min.css" type="text/css" />'
//            . '<link rel="stylesheet" href="https://sindresorhus.com/github-markdown-css/github-markdown.css" />'
            . '<link rel="stylesheet" href="http://markdowncss.github.io/retro/css/retro.css" />'
            . '<style>
                code {
                    padding: .2em .4em;
                    margin: 0;
                    font-size: 85%;
                    background-color: #333;
                    border-radius: 6px;
                }                
            </style>'
            . '<meta name="viewport" content="width=device-width, initial-scale=1.0" />'
            . '</head><body>'
            . '<article class="markdown-body">' . $html . '</article>'
            . '</body></html>'
        );

        return (new PSRResponse())
            ->withHeader('Content-type', 'text/html; charset=utf-8')
            ->withBody($body);
    }
}