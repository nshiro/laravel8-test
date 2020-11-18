<?php

namespace Tests\Feature\Controllers\Mypage;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see App\Http\Controllers\Mypage\BlogMypageController
 */
class BlogMypageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function ゲストはブログを管理できない()
    {
        $url = 'mypage/login';

        $this->get('mypage/blogs')->assertRedirect($url);
        $this->get('mypage/blogs/create')->assertRedirect($url);
    }

    /** @test index */
    function マイページ、ブログ一覧で自分のデータのみ表示される()
    {
        // $this->withoutExceptionHandling();
        $user = $this->login();

        $other = Blog::factory()->create();
        $myblog = Blog::factory()->create(['user_id' => $user]);

        $this->get('mypage/blogs')
            ->assertOk()
            ->assertDontSee($other->title)
            ->assertSee($myblog->title);
    }

    /** @test create */
    function マイページ、ブログの新規登録画面を開ける()
    {
        $this->login();

        $this->get('mypage/blogs/create')
            ->assertOk();
    }
}
