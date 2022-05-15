<?php

namespace Tests\Unit\Providers;

use App\Exceptions\MissingPrivateKeyException;
use Tests\TestCase;

class EncryptionServiceProviderTest extends TestCase
{
    /** @test */
    public function it_encrypts_and_decrypt_string(): void
    {
        config(['customers.key' => 'base64:CiZYhXuxFaXsYWOTw8o6C82rqiZkphLg+N6fVep2l0M=']);

        $crypted = app('license.encrypter')->encrypt('test');

        $this->assertStringStartsWith('eyJpdiI6I', $crypted);

        $decrypted = app('license.encrypter')->decrypt($crypted);

        $this->assertEquals('test', $decrypted);
    }

    /** @test */
    public function it_does_not_decrypt_with_wrong_key(): void
    {
        config(['customers.key' => 'base64:4IKtoOuRUWQLk2mdQ3MmM7VEr6dvP0IJWoijGk6NpRA=']);

        $crypted = 'eyJpdiI6IkZUNlJsQm96Zm1SU2l6WDAiLCJ2YWx1ZSI6IlIvZmJRMFgxeHFBWitZOD0iLCJtYWMiOiIiLCJ0YWciOiJ3eWc4RFZWb1FJcnFIdncyUVJpMkJBPT0ifQ==';

        $this->expectException(\Illuminate\Contracts\Encryption\DecryptException::class);

        app('license.encrypter')->decrypt($crypted);
    }

    /** @test */
    public function it_throw_exception_if_no_key(): void
    {
        config(['customers.key' => '']);

        $this->expectException(MissingPrivateKeyException::class);

        app('license.encrypter')->encrypt('test');
    }
}
