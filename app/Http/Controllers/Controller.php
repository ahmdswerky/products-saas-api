<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Http\Resources\MainPaginatedCollection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function cache(string $key, \Closure $fallback)
    {
        $key .= request()->getQueryString();
        $key = api_key() . ':' . $key;
        //$data = api_key() . ':' . Redis::get($key);
		$data = $fallback();

		if (!Redis::get($key)) {
			if (!$this->isEmptyData($data)) {
				$method = method_exists($data, 'getContent') ? 'getContent' : 'toJson';
	
				Redis::set($key, $data->{$method}());

				return json_decode(Redis::get($key));
			}
		}
		
		return $data;

		return Redis::get($key);

        if ($data) {
            $data = $fallback();
            $method = method_exists($data, 'getContent') ? 'getContent' : 'toJson';

            //Redis::set($key, $data->{$method}());
        }

        if (gettype($data) === 'string') {
            $data = json_decode($data);
        }

        return $data;
    }

	protected function isEmptyData($data): bool
	{
		if (!$data) {
			return true;
		}
		
		if ($data instanceof MainPaginatedCollection) {
			return !$data->collection->count();
		}

		return false;
	}

    public function merchant()
    {
        return $this->merchant = Merchant::byApiKey(api_key())->firstOrFail();
    }
}
