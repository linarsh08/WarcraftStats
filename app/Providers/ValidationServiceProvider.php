<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libs\WarcraftRaceEnum;
use Validator;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('true_race', $this->getTrueRaceCallback());

        Validator::extend('true_races', $this->getTrueRacesCallback());
    }

    protected function getTrueRaceCallback()
    {
        return function ($attribute, $value, $parameters, $validator) {
            return WarcraftRaceEnum::isValid($value);
        };
    }

    protected function getTrueRacesCallback()
    {
        return function ($attribute, $value, $parameters, $validator) {
            if (count($decoded_arr = json_decode($value)) === 0) {
                return false;
            }

            foreach (json_decode($value) as $race) {
                if (!WarcraftRaceEnum::isValid($race)) {
                    return false;
                }
            }

            return true;
        };
    }
}
