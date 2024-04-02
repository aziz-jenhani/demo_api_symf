<?php

namespace App\Fixture;

use Nelmio\Alice\Loader\NativeLoader as BaseNativeLoader;
use Faker\Generator as FakerGenerator;

class NativeLoader extends BaseNativeLoader
{
    protected function createFakerGenerator(): FakerGenerator
    {
        $generator = parent::createFakerGenerator();
        $generator->addProvider(new Provider());

        return $generator;
    }
}
