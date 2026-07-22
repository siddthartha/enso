<?php
declare(strict_types = 1);
/**
 * Class Application\CVAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\System\ActionHandler;
use Enso\System\Template;
use GuzzleHttp\Psr7\BufferStream;
use HttpSoft\Message\Response as PSRResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Description of CVAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class CVAction extends ActionHandler
{

    /**
     * @OA\Get(
     *     path="/default/cv",
     *     @OA\Response(response="200", description="Curriculum Vitae")
     * )
     */
    #[Route("/default/cv", methods: ["GET"])]
    public function __invoke(): ResponseInterface
    {
        $cv = file_get_contents(__DIR__ . '/../../CV.md');
        $html = (new \ParsedownExtra())
            ->text($cv);

        $body = new BufferStream();
        $body->write(
            (new Template(__DIR__ . '/views/cv.php'))
            ->render(
                vars: compact('html')
            )
        );

        return (new PSRResponse())
            ->withHeader('Content-type', 'text/html; charset=utf-8')
            ->withBody($body);
    }
}
