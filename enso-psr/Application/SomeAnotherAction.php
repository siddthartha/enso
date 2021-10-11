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

    /**
     *
     * @return string
     */
    public function __invoke(): array
    {
        return ['before' => $this->_request->before, 'fuck' => ['them' => 'all']];
    }
}