<?php

namespace Tests\Feature\Controllers\Mypage;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Mypage\UserLoginController
 */
class UserLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test index */
    function ログイン画面を開ける()
    {
        $this->get('mypage/login')
            ->assertOk();
    }

    /** @test login */
    function ログイン時の入力チェック()
    {
        $url = 'mypage/login';

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        app()->setlocale('testing');

        $this->post($url, ['email' => ''])->assertSessionHasErrors(['email' => 'required']);
        $this->post($url, ['email' => 'aa@bb@cc'])->assertSessionHasErrors(['email' => 'email']);
        $this->post($url, ['email' => 'aa@ああ.いい'])->assertSessionHasErrors(['email' => 'email']);
        $this->post($url, ['password' => ''])->assertSessionHasErrors(['password' => 'required']);
    }

    /** @test login */
    function ログインできる()
    {
        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'aaa@bbb.net',
            'password' => bcrypt('abcd1234'),
        ];

        $user = User::factory()->create($dbData);

        $this->post('mypage/login', $postData)
            ->assertRedirect('mypage/blogs');

        $this->assertAuthenticatedAs($user);
    }

    /** @test login */
    function IDを間違えているのでログインできない()
    {
        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'ccc@bbb.net',
            'password' => bcrypt('abcd1234'),
        ];

        $user = User::factory()->create($dbData);

        $url = 'mypage/login';

        $this->from($url)->post($url, $postData)
            ->assertRedirect($url);

        $this->get($url)
            ->assertSee('メールアドレスかパスワードが間違っています。');

        $this->from($url)->followingRedirects()->post($url, $postData)
            ->assertSee('メールアドレスかパスワードが間違っています。')
            ->assertSee('<h1>ログイン画面</h1>', false);
            // ->assertSeeText('ログイン画面ですよ');
    }

    /** @test login */
    function パスワードを間違えているのでログインできない()
    {
        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'aaa@bbb.net',
            'password' => bcrypt('abcd5678'),
        ];

        $user = User::factory()->create($dbData);

        $url = 'mypage/login';

        $this->from($url)->post($url, $postData)
            ->assertRedirect($url);

        $this->get($url)
            ->assertSee('メールアドレスかパスワードが間違っています。');

        $this->from($url)->followingRedirects()->post($url, $postData)
            ->assertSee('メールアドレスかパスワードが間違っています。')
            ->assertSee('<h1>ログイン画面</h1>', false);
            // ->assertSeeText('ログイン画面ですよ');
    }

    /** @test login */
    function 認証エラーなのでvalidationExceptionの例外が発生する()
    {
        $this->withoutExceptionHandling();

        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        // $dbData = [
        //     'email' => 'aaa@bbb.net',
        //     'password' => bcrypt('abcd1234'),
        // ];

        // $user = User::factory()->create($dbData);

        // $this->expectException(ValidationException::class);

        try {
            $this->post('mypage/login', $postData);
            $this->fail('validationExceptionの例外が発生しませんでしたよ。');
        } catch (ValidationException $e) {
            $this->assertEquals('メールアドレスかパスワードが間違っています。',
                $e->errors()['email'][0] ?? ''
            );
        }
    }

    /** @test login */
    function 認証OKなのでvalidationExceptionの例外が出ない()
    {
        $this->withoutExceptionHandling();

        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'aaa@bbb.net',
            'password' => bcrypt('abcd1234'),
        ];

        $user = User::factory()->create($dbData);

        try {
            $this->post('mypage/login', $postData);
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('validationExceptionの例外が発生してしまいました。');
        }
    }

    /** @test logout */
    function ログアウトできる()
    {
        $this->login();

        $this->post('mypage/logout')
            ->assertRedirect($url = 'mypage/login');

        $this->get($url)
            ->assertSee('ログアウトしました。');

        $this->assertGuest();
    }
}
