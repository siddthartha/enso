<?php
/**
 * index
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

/**
 * @OA\OpenApi(
 *
 *     @OA\Server(
 *         url="http://enso.localhost/",
 *         description="Enso API test server"
 *     ),
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Enso",
 *         description="A sample API that demonstrates a proof of concept",
 *         @OA\Contact(name="Anton Sadovnikov"),
 *         @OA\License(name="BSD-3-Clause")
 *     ),
 * )
 */

$run = require_once '../entrypoint.php';

return $run();
