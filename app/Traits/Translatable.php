<?php

namespace App\Traits;

Trait Translatable
{
    protected $locale;
    protected $request;

    public static function bootTranslatable()
    {
        if ( !request()->isMethod('get') ) {
            if ( !request()->dontTranslate ) {
                self::saved(function ($model) {
                    $model->translate( request()->all() );
                });
            }
        }
    }

    public function __get($property)
    {
        if ( in_array($property, $this->translatedAttributes) ) {
            return $this->findTranslated($property);
        }

        return parent::__get($property);
    }

    public function translations()
    {
        $class = class_name($this) . 'Translation';

        return $this->hasMany( $class );
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function translate(array $data, $locale = null)
    {
        $data = $this->getTranslatableData($data);

        $data['locale'] = $locale ? $locale : $this->getLocale();

        $this->translations()->updateOrCreate([
            'locale' => $data['locale']],
            $data
        );

        return $this;
    }

    public function getTranslatableData($data)
    {
        $translatableData = collect($data)->reject(function ($value, $key) {
            return !in_array( $key, $this->translatedAttributes );
        })->toArray();

        return $translatableData;
    }

    public function findTranslated($property)
    {
        $locale = $this->getXLocale();

        $translation = $this->translations()->where( compact('locale') )->count() ?
                        $this->translations()->where( compact('locale') )->first() :
                        $this->translations()->first();

        return $translation ? $translation[$property] : null;
    }

    public function getLocale() {
        $locale = config('translation.locale');
        $available_locales = array_keys( config('translation.locale_codes') );
        if ( request()->isMethod('PUT') && request()->has('locale') && in_array(request()->locale, $available_locales) ) {
            $locale = request()->locale;
        }

        if ( request()->has('locale') && !in_array(request()->locale, $available_locales) ) {
            abort(422, __('This language is not supported yet'));
        }

        return $locale;
    }

    public function getXLocale()
    {
        $locale = config('translation.locale');
        $available_locales = array_keys( config('translation.locale_codes') );

        if ( request()->hasHeader('X-Locale') ) {
            if ( in_array(request()->header('X-Locale'), $available_locales) ) {
                if ( $this->translations()->where('locale', request()->header('X-Locale'))->count() ) { // if model has perviuos translations
                    $locale = request()->header('X-Locale');
                }
            }
        }

        return $locale;
    }
}
