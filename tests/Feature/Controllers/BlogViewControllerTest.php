<?php

namespace Tests\Feature\Controllers;

use Facades\Illuminate\Support\Str;
use App\Http\Middleware\BlogShowLimit;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use App\StrRandom;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BlogViewController
 */
class BlogViewControllerTest extends TestCase
{
    use RefreshDatabase;
    // use WithoutMiddleware;

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
    function ブログの詳細画面が表示でき、コメントが古い順に表示される()
    {
        $this->withoutMiddleware(BlogShowLimit::class);

        // $blog = Blog::factory()->create();

        // Comment::factory()->create([
        //     'created_at' => now()->sub('2 days'),
        //     'name' => '太郎',
        //     'blog_id' => $blog->id,
        // ]);
        // Comment::factory()->create([
        //     'created_at' => now()->sub('3 days'),
        //     'name' => '次郎',
        //     'blog_id' => $blog->id,
        // ]);
        // Comment::factory()->create([
        //     'created_at' => now()->sub('1 days'),
        //     'name' => '三郎',
        //     'blog_id' => $blog->id,
        // ]);

        $blog = Blog::factory()->withCommentsData([
            ['created_at' => now()->sub('2 days'), 'name' => '太郎'],
            ['created_at' => now()->sub('3 days'), 'name' => '次郎'],
            ['created_at' => now()->sub('1 days'), 'name' => '三郎'],
        ])->create();

        //dd($blog->comments->toArray());

        $this->get('blogs/'.$blog->id)
            ->assertOk()
            ->assertSee($blog->title)
            ->assertSee($blog->user->name)
            ->assertSeeInOrder(['次郎', '太郎', '三郎']);
    }

    /** @test show */
    function ブログで非公開のものは、詳細画面は表示できない()
    {
        $this->withoutMiddleware(BlogShowLimit::class);

        $blog = Blog::factory()->closed()->create();

        $this->get('blogs/'.$blog->id)
            ->assertForbidden();
    }

    /** @test show */
    function クリスマスの日は、メリークリスマス！と表示される()
    {
        $this->withoutMiddleware(BlogShowLimit::class);

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

    /** @test show */
    function ブログの詳細画面で、ランダムな文字列が10文字表示される()
    {
        $this->withoutMiddleware(BlogShowLimit::class);
        $this->withoutExceptionHandling();

        $blog = Blog::factory()->create();

        // Str::shouldReceive('random')
        //     ->once()->with(10)->andReturn('HELLO_RAND');

        // $mock = new Class ()
        // {
        //     public function random(int $len)
        //     {
        //         if ($len !== 10) {
        //             // return 'hoge';
        //             throw new \Exception('引数違う');
        //         }
        //         return 'HELLO_RAND';
        //     }
        // };

        // $this->app->instance(StrRandom::class, $mock);


        // $mock = \Mockery::mock(StrRandom::class);
        // $mock->shouldReceive('random')->once()->with(10)->andReturn('HELLO_RAND');
        // $this->app->instance(StrRandom::class, $mock);

        $this->mock(StrRandom::class, function ($mock) {
            $mock->shouldReceive('random')->once()->with(10)->andReturn('HELLO_RAND');
        });

        $this->get('blogs/'.$blog->id)
            ->assertOk()
            ->assertSee('HELLO_RAND');
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
