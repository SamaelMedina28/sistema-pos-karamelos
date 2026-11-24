<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      x={
 *          "logo": {
 *              "url": "https://via.placeholder.com/190x90.png?text=SAAS 'Karamelos'"
 *          }
 *      },
 *      title="SAAS 'Karamelos'",
 *      description="Sistema de punto de venta para local de 'Karamelos'",
 *      @OA\Contact(
 *          email="samaelortiz2218@gmail.com"
 *      ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="cookieAuth",
 *     type="apiKey",
 *     in="cookie",
 *     name="token",
 *     description="JWT guardado en la cookie HttpOnly `token`. Se obtiene llamando a POST /api/login desde este mismo origen; el navegador la envía automáticamente."
 * )
 */
abstract class Controller
{
    //
}
