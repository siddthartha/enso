<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Relay\Request;
use Enso\Relay\Response;

/**
 * Description of SomeAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class SomeAction extends \Enso\System\ActionHandler
{

    #[Route("/default/action", methods: ["GET"])]
    public function __invoke(): array
    {
        return \Enso\Helpers\A::merge(
            $this->getRequest()->attributes,
            ['work' => 'done', 'random' => rand(1000, 10000)]
        );
    }
}