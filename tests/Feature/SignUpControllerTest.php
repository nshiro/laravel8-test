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

    private function validData($overrides = [])
    {
        return array_merge([
            'name' => '太郎',
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ], $overrides);
    }

    /** @test store */
    function ユーザー登録できる()
    {
        // データ検証
        // DBに保存
        // ログインさせてからマイページにリダイレクト

        // $this->withoutExceptionHandling();

        // $validData = [
        //     'name' => '太郎',
        //     'email' => 'aaa@bbb.net',
        //     'password' => 'abcd1234',
        // ];

        // $validData = $this->validData();
        // $validData = User::factory()->make()->toArray();
        // $validData = User::factory()->valid()->raw();
        $validData = User::factory()->validData();

        $this->post('signup', $validData)
            ->assertRedirect('mypage/blogs');

        unset($validData['password']);

        $this->assertDatabaseHas('users', $validData);

        // パスワードの検証
        $user = User::firstWhere($validData);
        $this->assertNotNull($user);

        $this->assertTrue(\Hash::check('abcd1234', $user->password));

        $this->assertAuthenticatedAs($user);
    }

    /** @test store */
    function 不正なデータではユーザー登録できない()
    {
        // $this->withoutExceptionHandling();
        $url = 'signup';

        // $this->get('signup');

        $this->from('signup')->post($url, [])
            ->assertRedirect('signup');

        app()->setlocale('testing');

        // 注意点
        // (1) カスタムメッセージを設定している時は、そちらが優先される
        // (2) 入力エラーが出る前に言語ファイルを読もうとしている箇所がある時は、
        //      そちらもtestingに対応させる必要あり

        // ->dumpSession()
        $this->post($url, ['name' => ''])->assertSessionHasErrors(['name' => 'required']);
        $this->post($url, ['name' =>str_repeat('あ', 21)])->assertSessionHasErrors(['name' => 'max']);
        $this->post($url, ['name' =>str_repeat('あ', 20)])->assertSessionDoesntHaveErrors('name');

        $this->post($url, ['email' => ''])->assertSessionHasErrors(['email' => 'required']);
        $this->post($url, ['email' => 'aa@bb@cc'])->assertSessionHasErrors(['email' => 'email']);
        $this->post($url, ['email' => 'aa@ああ.いい'])->assertSessionHasErrors(['email' => 'email']);

        User::factory()->create(['email' => 'aaa@bbb.net']);
        $this->post($url, ['email' => 'aaa@bbb.net'])->assertSessionHasErrors(['email' => 'unique']);

        $this->post($url, ['password' => ''])->assertSessionHasErrors(['password' => 'required']);
        $this->post($url, ['password' => 'abcd123'])->assertSessionHasErrors(['password' => 'min']);
        $this->post($url, ['password' => 'abcd1234'])->assertSessionDoesntHaveErrors('password');
    }
}
