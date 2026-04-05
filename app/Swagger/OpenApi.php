<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'AuthPlus API',
    version: '1.0',
    description: 'Authentication API'
)]

#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\OpenApi(
    security: [['bearerAuth' => []]]
)]
class OpenApi
{
}