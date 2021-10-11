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

    /**
     *
     * @return array
     */
    public function __invoke(): array
    {
        return $this->_request->attributes;
    }
}