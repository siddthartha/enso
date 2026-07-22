<?php
declare(strict_types = 1);
/**
 * Class Application\RoutesAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Helpers\Tree;
use Enso\System\ActionHandler;
use Psr\Http\Message\ResponseInterface;

/**
 * Description of RoutesAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class RoutesAction extends ActionHandler
{

    /**
     * @OA\Get(
     *     path="/default/routes",
     *     @OA\Response(response="200", description="List of all routes")
     * )
     */
    #[Route("/default/routes", methods: ["GET"])]
    public function __invoke(): array
    {
        return [
            ...(
                new Tree(
                    $this->_context->getRoutingTree()
                )
            )->next()
        ];
    }

}