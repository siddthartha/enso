<?php
declare(strict_types = 1);
/**
 * Class Application\CVRuAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

/**
 * Russian edition of the CV, served from CV.ru.md at a dedicated path.
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class CVRuAction extends CVAction
{
    protected const LANG = 'ru';

    /**
     * @OA\Get(
     *     path="/default/cv-ru",
     *     @OA\Response(response="200", description="Curriculum Vitae (Russian)")
     * )
     */
    #[Route("/default/cv-ru", methods: ["GET"])]
    public function __invoke(): \Psr\Http\Message\ResponseInterface
    {
        return parent::__invoke();
    }
}
