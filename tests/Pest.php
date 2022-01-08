<?php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Tests\CreatesApplication;
use Database\Seeders\UserSeeder;
use function Pest\Laravel\actingAs;
use Database\Seeders\EssentialSeeder;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

//uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class)->beforeAll(fn () => prepare())->in('Unit');
//uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class)->beforeEach(fn () => prepare())->in('Unit');
uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class)->beforeEach(fn () => actingAs(User::first()))->in('Unit', 'Feature');

function createRequest($method, $uri)
{
    $symfonyRequest = SymfonyRequest::create(
        $uri,
        $method,
    );

    return Request::createFromBase($symfonyRequest);
}

//beforeEach()->createApplication();

//beforeAll(fn () => prepare());


//function prepare()
//{
//    Config::set('database.default', 'sqlite');
//    Artisan::call('app:setup');
//    (new EssentialSeeder)->run();
//    (new UserSeeder)->run();
//}

//beforeAll(function () {
//    dd('asd');
//    User::factory()->count(1)->create([
//        'public_id' => Str::random(20),
//        'name' => 'Customer',
//        'email' => 'test@test.com',
//    ]);
//});

//uses()->beforeEach(fn () => $this->actingAs(User::first()));
