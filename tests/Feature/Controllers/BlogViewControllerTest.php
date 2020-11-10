<?php

namespace Tests\Feature\Controllers;

use App\Models\Blog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogViewControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test index */
    function ブログのTOPページを開ける()
    {
        // $this->withoutExceptionHandling();

        $blog1 = Blog::factory()->hasComments(1)->create();
        $blog2 = Blog::factory()->hasComments(3)->create();
        $blog3 = Blog::factory()->hasComments(2)->create();

        $this->get('/')
            ->assertOk()
            ->assertSee($blog1->title)
            ->assertSee($blog2->title)
            ->assertSee($blog3->title)
            ->assertSee($blog1->user->name)
            ->assertSee($blog2->user->name)
            ->assertSee($blog3->user->name)
            ->assertSee("（1件のコメント）")
            ->assertSee("（3件のコメント）")
            ->assertSee("（2件のコメント）")
            ->assertSeeInOrder([$blog2->title, $blog3->title, $blog1->title]);

        // Blog::factory()->create(['title' => 'あいうえお']);
        // Blog::factory()->create(['title' => 'かきくけこ']);
        // Blog::factory()->create(['title' => 'さしすせそ']);

        // $this->get('/')
        //     ->assertOk()
        //     ->assertSee('あいうえお')
        //     ->assertSee('かきくけこ')
        //     ->assertSee('さしすせそ');
    }

    /** @test index */
    function ブログの一覧、非公開のブログは表示されない()
    {
        Blog::factory()->closed()->create([
            'title' => 'ブログA',
            ]);
        Blog::factory()->create(['title' => 'ブログB']);
        Blog::factory()->create(['title' => 'ブログC']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('ブログA')
            ->assertSee('ブログB')
            ->assertSee('ブログC');
    }

    /** @test show */
    function ブログの詳細画面が表示できる()
    {
        $blog = Blog::factory()->create();

        $this->get('blogs/'.$blog->id)
            ->assertOk()
            ->assertSee($blog->title)
            ->assertSee($blog->user->name);
    }

    /** @test show */
    function ブログで非公開のものは、詳細画面は表示できない()
    {
        $blog = Blog::factory()->closed()->create();

        $this->get('blogs/'.$blog->id)
            ->assertForbidden();
    }

    /** @test show */
    function クリスマスの日は、メリークリスマス！と表示される()
    {
        $blog = Blog::factory()->create();

        Carbon::setTestNow('2020-12-24');

        $this->get('blogs/'. $blog->id)
            ->assertOk()
            ->assertDontSee('メリークリスマス！');

        Carbon::setTestNow('2020-12-25');

        $this->get('blogs/'. $blog->id)
            ->assertOk()
            ->assertSee('メリークリスマス！');
    }

    /** @test  */
    function factoryの観察()
    {
        // $blog = Blog::factory()->create();
        // $blog = Blog::factory()->create(['user_id' => 5]);
        // $blog = Blog::factory()->make(['user_id' => null]);

        // dump($blog->toArray());
        // dump(Blog::count());

        // dump(User::get()->toArray());
        // dump(User::count());

        $this->assertTrue(true);
    }
}
