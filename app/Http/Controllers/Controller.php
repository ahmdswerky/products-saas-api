<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function merchant()
    {
        return $this->merchant = Merchant::byApiKey(apiKey())->firstOrFail();
    }
}
