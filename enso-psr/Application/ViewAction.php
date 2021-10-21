<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Helpers\A;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\ActionHandler;

/**
 * Description of ViewAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class ViewAction extends ActionHandler
{

    #[Route("/default/action", methods: ["GET"])]
    public function __invoke():     array
    {
        return A::merge(
            $this->getRequest()->attributes,
            ['work' => 'done', 'random' => rand(1000, 10000)]
        );
    }
}