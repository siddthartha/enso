<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAnotherAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\ActionHandler;

/**
 * Description of IndexAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class IndexAction extends ActionHandler
{

    #[Route("/default/index", methods: ["GET"])]
    public function __invoke(): array
    {
        return [
            'before' => $this->getRequest()->before,
            'context' => [
                'sapi' => PHP_SAPI,
                'swoole' => function_exists('swoole_version') ? true : false,
            ],
        ];
    }

}