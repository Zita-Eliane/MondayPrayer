<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Disables CSRF verification for tests but keeps session middleware.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Disable CSRF only - keep session and other essential middleware
        // This way tests can use session() and other session-dependent features
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }
}
