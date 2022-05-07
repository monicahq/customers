<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // set the bcrypt hashing rounds (the minimum allowed).
        // this reduces the amount of cycles needed to manage users.
        Hash::setRounds(4);

        return $app;
    }
}
