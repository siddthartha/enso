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

    /** Pinned language; null means resolve from the `?lang=` query parameter */
    protected const LANG = null;

    /** Available CV translations: lang code => markdown file */
    protected const LANG_FILES = [
        'en' => 'CV.md',
        'ru' => 'CV.ru.md',
    ];

    /**
     * @OA\Get(
     *     path="/default/cv",
     *     @OA\Parameter(name="lang", in="query", required=false, @OA\Schema(type="string", enum={"en", "ru"})),
     *     @OA\Response(response="200", description="Curriculum Vitae")
     * )
     */
    #[Route("/default/cv", methods: ["GET"])]
    public function __invoke(): ResponseInterface
    {
        $lang = $this->resolveLang();

        $cv = file_get_contents(__DIR__ . '/../' . self::LANG_FILES[$lang]);
        $html = (new \ParsedownExtra())
            ->text($cv);

        $body = new BufferStream();
        $body->write(
            (new Template(__DIR__ . '/views/cv.php'))
            ->render(
                vars: compact('html', 'lang')
            )
        );

        return (new PSRResponse())
            ->withHeader('Content-type', 'text/html; charset=utf-8')
            ->withBody($body);
    }

    /**
     * Picks the CV language from `?lang=`, falling back to English.
     * Query params live in `queryParams` under FPM/Swoole and only in the URI under RoadRunner,
     * so both sources are consulted.
     */
    protected function resolveLang(): string
    {
        if (static::LANG !== null)
        {
            return static::LANG;
        }

        $request = $this->getRequest();

        // `queryParams` is a magic Subject attribute (no __isset), so read the attribute bag directly
        $params = method_exists($request, '__get_attributes')
            ? (array) ($request->__get_attributes()['queryParams'] ?? [])
            : [];

        if (empty($params) && method_exists($request, 'getUri'))
        {
            parse_str($request->getUri()->getQuery(), $params);
        }

        $lang = strtolower((string) ($params['lang'] ?? 'en'));

        return isset(self::LANG_FILES[$lang]) ? $lang : 'en';
    }
}
