<?php

namespace Tests\Feature;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirect()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}