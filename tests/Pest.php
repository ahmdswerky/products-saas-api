<?php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use function Pest\Laravel\actingAs;

uses(
    TestCase::class,
    CreatesApplication::class,
    RefreshDatabase::class
)->beforeEach(fn () => actingAs(User::first()))
    ->in('Unit', 'Feature');

function createRequest($method, $uri)
{
    $symfonyRequest = SymfonyRequest::create(
        $uri,
        $method,
    );

    return Request::createFromBase($symfonyRequest);
}
