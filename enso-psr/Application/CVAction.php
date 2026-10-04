<?php
declare(strict_types = 1);
/**
 * Class Application\CVAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Dompdf\Dompdf;
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

    /** Download file name of the PDF edition per language */
    protected const PDF_FILES = [
        'en' => 'Anton_Sadovnikov_CV.pdf',
        'ru' => 'Anton_Sadovnikov_CV_ru.pdf',
    ];

    /**
     * @OA\Get(
     *     path="/default/cv",
     *     @OA\Parameter(name="lang", in="query", required=false, @OA\Schema(type="string", enum={"en", "ru"})),
     *     @OA\Parameter(name="format", in="query", required=false, @OA\Schema(type="string", enum={"html", "pdf"})),
     *     @OA\Response(response="200", description="Curriculum Vitae (HTML, or a PDF download with ?format=pdf)")
     * )
     */
    #[Route("/default/cv", methods: ["GET"])]
    public function __invoke(): ResponseInterface
    {
        $params = $this->queryParams();
        $lang = $this->resolveLang($params);
        $pdf = ($params['format'] ?? '') === 'pdf';

        $cv = file_get_contents(__DIR__ . '/../' . self::LANG_FILES[$lang]);
        $html = (new \ParsedownExtra())
            ->text($cv);

        $page = (new Template(__DIR__ . '/views/cv.php'))
            ->render(
                vars: compact('html', 'lang', 'pdf')
            );

        $body = new BufferStream();

        if ($pdf)
        {
            $body->write($this->toPdf($page));

            return (new PSRResponse())
                ->withHeader('Content-type', 'application/pdf')
                ->withHeader('Content-Disposition', 'attachment; filename="' . self::PDF_FILES[$lang] . '"')
                ->withBody($body);
        }

        $body->write($page);

        return (new PSRResponse())
            ->withHeader('Content-type', 'text/html; charset=utf-8')
            ->withBody($body);
    }

    /**
     * Renders the already-built page (PDF variant of the view) into an A4 PDF document.
     */
    protected function toPdf(string $html): string
    {
        $dompdf = new Dompdf([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => false,
            'tempDir' => sys_get_temp_dir(),
        ]);

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Picks the CV language from `?lang=`, falling back to English.
     */
    protected function resolveLang(array $params): string
    {
        if (static::LANG !== null)
        {
            return static::LANG;
        }

        $lang = strtolower((string) ($params['lang'] ?? 'en'));

        return isset(self::LANG_FILES[$lang]) ? $lang : 'en';
    }

    /**
     * Query params live in `queryParams` under FPM/Swoole and only in the URI under RoadRunner,
     * so both sources are consulted.
     */
    protected function queryParams(): array
    {
        $request = $this->getRequest();

        // `queryParams` is a magic Subject attribute (no __isset), so read the attribute bag directly
        $params = method_exists($request, '__get_attributes')
            ? (array) ($request->__get_attributes()['queryParams'] ?? [])
            : [];

        if (empty($params) && method_exists($request, 'getUri'))
        {
            parse_str($request->getUri()->getQuery(), $params);
        }

        return $params;
    }
}
