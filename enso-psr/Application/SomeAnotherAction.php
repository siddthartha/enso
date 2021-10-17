<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAnotherAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Relay\Request;
use Enso\Relay\Response;

/**
 * Description of SomeAnotherAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class SomeAnotherAction extends \Enso\System\ActionHandler
{

    #[Route("/default/index", methods: ["GET"])]
    public function __invoke(): array
    {
        return [
            'before' => $this->getRequest()->before,
            'fuck' => ['them' => 'all']
        ];
    }
}