<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SignUpController
 */
class SignUpControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test index */
    function ユーザー登録画面を開ける()
    {
        $this->get('signup')
            ->assertOk();
    }

    /** @test store */
    function ユーザー登録できる()
    {
        // データ検証
        // DBに保存
        // ログインさせてからマイページにリダイレクト

        // $this->withoutExceptionHandling();

        $validData = [
            'name' => '太郎',
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $this->post('signup', $validData)
            ->assertOk();

        unset($validData['password']);

        $this->assertDatabaseHas('users', $validData);

        // パスワードの検証
        $user = User::firstWhere($validData);
        $this->assertNotNull($user);

        $this->assertTrue(\Hash::check('abcd1234', $user->password));


    }
}
