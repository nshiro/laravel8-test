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
        $this->post('mypage/blogs/create', [])->assertRedirect($url);
        $this->get('mypage/blogs/edit/1')->assertRedirect($url);
        $this->post('mypage/blogs/edit/1')->assertRedirect($url);
        $this->delete('mypage/blogs/delete/1')->assertRedirect($url);
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

    /** @test store */
    function マイページ、ブログを新規登録できる、公開の場合()
    {
        $this->login();

        $validData = Blog::factory()->validData();

        $this->post('mypage/blogs/create', $validData)
            ->assertRedirect('mypage/blogs/edit/1'); // SQLite のインメモリ

        $this->assertDatabaseHas('blogs', $validData);
    }

    /** @test store */
    function マイページ、ブログを新規登録できる、非公開の場合()
    {
        $this->login();

        $validData = Blog::factory()->validData();

        unset($validData['status']);

        $this->post('mypage/blogs/create', $validData)
            ->assertRedirect('mypage/blogs/edit/1'); // SQLite のインメモリ

        $validData['status'] = '0';

        $this->assertDatabaseHas('blogs', $validData);
    }

    /** @test store */
    function マイページ、ブログの登録時の入力チェック()
    {
        $url = 'mypage/blogs/create';

        $this->login();

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        app()->setlocale('testing');

        $this->post($url, ['title' => ''])->assertSessionHasErrors(['title' => 'required']);
        $this->post($url, ['title' => str_repeat('a', 256)])->assertSessionHasErrors(['title' => 'max']);
        $this->post($url, ['title' => str_repeat('a', 255)])->assertSessionDoesntHaveErrors(['title' => 'max']);
        $this->post($url, ['body' => ''])->assertSessionHasErrors(['body' => 'required']);
    }

    /** @test edit */
    function 他人様のブログの編集画面は開けない()
    {
        $blog = Blog::factory()->create();

        $this->login();

        $this->get('mypage/blogs/edit/'.$blog->id)
            ->assertForbidden();
    }

    /** @test update */
    function 他人様のブログは更新できない()
    {
        $validData = [
            'title' => '新タイトル',
            'body' => '新本文',
            'status' => '1',
        ];

        $blog = Blog::factory()->create();

        $this->login();

        $this->post('mypage/blogs/edit/'.$blog->id, $validData)
            ->assertForbidden();

        // $this->assertDatabaseMissing('blogs', $validData);

        $this->assertCount(1, Blog::all());
        $this->assertEquals($blog->toArray(), Blog::first()->toArray());
    }

    /** @test destroy */
    function 他人様のブログを削除はできない()
    {
        $blog = Blog::factory()->create();

        $this->login();

        $this->delete('mypage/blogs/delete/'.$blog->id)
            ->assertForbidden();

        $this->assertCount(1, Blog::all());
    }

    /** @test destroy */
    function 自分のブログは削除できる()
    {
        $blog = Blog::factory()->create();

        $this->login($blog->user);

        $this->delete('mypage/blogs/delete/'.$blog->id)
            ->assertRedirect('mypage/blogs');

        // $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);  // $blog->only('id)
        $this->assertDeleted($blog);
    }

    /** @test edit */
    function 自分のブログの編集画面は開ける()
    {
        $blog = Blog::factory()->create();

        $this->login($blog->user);

        $this->get('mypage/blogs/edit/'.$blog->id)
            ->assertOk();
    }

    /** @test update */
    function 自分のブログは更新できる()
    {
        $validData = [
            'title' => '新タイトル',
            'body' => '新本文',
            'status' => '1',
        ];

        $blog = Blog::factory()->create();

        $this->login($blog->user);

        $this->post('mypage/blogs/edit/'.$blog->id, $validData)
            ->assertRedirect('mypage/blogs/edit/'.$blog->id);

        $this->get('mypage/blogs/edit/'.$blog->id)
            ->assertSee('ブログを更新しました');

        // 新規で追加されたかも知れない。なので不完全と言えば、不完全
        $this->assertDatabaseHas('blogs', $validData);

        $this->assertCount(1, Blog::all());
        $this->assertEquals(1, Blog::count());

        // 項目が少ない時は、fresh()を使って
        $this->assertEquals('新タイトル', $blog->fresh()->title);
        $this->assertEquals('新本文', $blog->fresh()->body);

        // 項目が多い時は、refresh()を使って
        $blog->refresh();
        $this->assertEquals('新本文', $blog->body);
        $this->assertEquals('新本文', $blog->body);
        $this->assertEquals('新本文', $blog->body);
        $this->assertEquals('新本文', $blog->body);
        $this->assertEquals('新本文', $blog->body);
        $this->assertEquals('新本文', $blog->body);
        $this->assertEquals('新本文', $blog->body);
    }
}
