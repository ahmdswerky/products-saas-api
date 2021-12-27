<?php

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as ModelCollection;

function per_page($limit = 12)
{
    if ($perPage = request()->query('limit')) {
        if ($perPage >= 300) {
            return 300;
        }

        return $perPage;
    }

    return $limit;
}

//function client_url($url)
//{
//    $slash = Str::startsWith($url, '/') ? '' : '/';

//	url()

//    return config('app.client_url') . $slash . $url;
//}

function client_url($path = null, $parameters = [], $secure = null)
{
    URL::forceRootUrl(config('app.client_url'));

    $url = url($path, $parameters, $secure);

    URL::forceRootUrl(config('app.url'));

    return $url;
}

function generate_uuid($field, Model $model)
{
    $uuid = Str::uuid();

    $exists = $model->where($field, $uuid)->exists();

    if ($exists) {
        return generate_uuid($field, $model);
    }

    return $uuid;
}

function random_integer($length = 6)
{
    $min = str_repeat(1, $length);
    $max = str_repeat(9, $length);

    return rand($min, $max);
}


function get_value(array $array, string $key, $fallback = null)
{
    return array_key_exists($key, $array) ? $array[$key] : $fallback;
}

function code_formatter($code, $separator = '-')
{
    $length = strlen($code);
    $isEven = $length % 2 === 0;

    if (!$isEven || $length <= 4) {
        return $code;
    }

    return implode($separator, str_split($code, $length / 2));
}

function slug($value, $against, $field = 'slug')
{
    //if (! ($against instanceof Model)) {
    //    $against = new $against;
    //}

    if ($against instanceof Model && (! class_exists($against))) {
        throw new Exception('class doesn\'t exists');
    }

    $slug = Str::slug($value);

    if ($against::where($field, $slug)->exists()) {
        return slug($slug . '-' . random_integer(4), $against, $field);
    }

    return $slug;
}

function qr_code_generator(string $content, $options = []): string
{
    $options = $options ?: [
        'size' => 150,
        'color' => '000000',
        'bgcolor' => 'ffffff',
    ];

    $size = $options['size'];
    $color = $options['color'];
    $bgcolor = $options['bgcolor'];

    return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&color={$color}&bgcolor={$bgcolor}&data={$content}";
}

function generate_letter_image($letter)
{
    $colors = [
        '#D81B60',
        '#8E24AA',
        '#4A148C',
        '#5E35B1',
        '#1565C0',
        '#00796B',
        '#00838F',
        '#FFA000',
        '#455A64',
    ];

    $letter = strtoupper($letter);
    $color = str_replace('#', '', collect($colors)->random());
    return "https://via.placeholder.com/150/{$color}/fff?text={$letter}";
}

function unique_slug($model, $against, $value)
{
    $value = Str::slug($value);

    if ($model::where($against, $value)->exists()) {
        $value = $value . '-' . rand();

        return unique_slug($model, $against, $value);
    }

    return $value;
}

function array_to_object(array $array): object
{
    return json_decode(json_encode($array, JSON_FORCE_OBJECT));
}

function active_guard()
{
    $guards = array_keys(config('auth.guards'));

    for ($index = 0; $index < count($guards); $index++) {
        $guard = $guards[$index];
        if (Auth::guard($guard)->check()) {
            return $guard;
        }
    }
}

// TODO: finish
function toObject($payload)
{
    if ($payload instanceof Collection) {
        return (object) $payload->toArray();
    }

    if (gettype($payload) === 'array') {
        return (object) $payload;
    }

    return $payload;
}

//if (! function_exists('guard')) {
//    function guard()
//    {
//        if (request()->header('api-key') || request()->api_key) {
//            return 'key';
//        }

//        return 'api';
//    }
//}
