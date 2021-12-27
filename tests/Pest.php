<?php

use Tests\TestCase;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class)->in('Unit');

beforeEach()->createApplication();
