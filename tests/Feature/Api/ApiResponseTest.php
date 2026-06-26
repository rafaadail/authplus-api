<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Helpers\ApiResponse;

class ApiResponseTest extends TestCase
{
    public function test_success_response_with_default_values(): void
    {
        $response = ApiResponse::success();

        $this->assertEquals(200, $response->status());

        $this->assertEquals([
            'success' => true,
            'message' => 'Success',
            'data' => null,
        ], $response->getData(true));
    }

    public function test_error_response_with_default_values(): void
    {
        $response = ApiResponse::error('Something went wrong');

        $this->assertEquals(400, $response->status());

        $this->assertEquals([
            'success' => false,
            'message' => 'Something went wrong',
            'errors' => null,
        ], $response->getData(true));
    }
}
