<?php

namespace Tests\Unit\Models;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Models\Blog
 */
class BlogTest extends TestCase
{
    use RefreshDatabase;

    /** @test user */
    function userリレーションを返す()
    {
        $blog = Blog::factory()->create();

        $this->assertInstanceOf(User::class, $blog->user); // $blog->user()->first();
    }

    /** @test comments */
    function commentsリレーションを返す()
    {
        $blog = Blog::factory()->create();

        $this->assertInstanceOf(Collection::class, $blog->comments);
    }

    /** @test scopeOnlyOpen */
    function ブログの公開・非公開のscope()
    {
        $blog1 = Blog::factory()->closed()->create([
            'title' => 'ブログA',
            ]);
        $blog2 = Blog::factory()->create(['title' => 'ブログB']);
        $blog3 = Blog::factory()->create(['title' => 'ブログC']);

        $blogs = Blog::onlyOpen()->get();

        $this->assertFalse($blogs->contains($blog1));
        $this->assertTrue($blogs->contains($blog2));
        $this->assertTrue($blogs->contains($blog3));
    }

    /** @test isClosed */
    function ブログで非公開時はtrueを返し、公開時はfalseを返す()
    {
        $blog = Blog::factory()->make();

        $this->assertFalse($blog->isClosed());

        $blog = Blog::factory()->closed()->make();

        $this->assertTrue($blog->isClosed());
    }
}
