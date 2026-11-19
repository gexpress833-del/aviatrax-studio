<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private int $initialOutputBufferLevel = 0;

    protected function setUp(): void
    {
        $this->initialOutputBufferLevel = ob_get_level();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->initialOutputBufferLevel) {
            ob_end_clean();
        }

        parent::tearDown();
    }
}
