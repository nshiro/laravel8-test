<?php

namespace Tests\Feature\Controllers;

use App\Models\Blog;
use App\Models\User;
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

        $blog1 = Blog::factory()->create();
        $blog2 = Blog::factory()->create();
        $blog3 = Blog::factory()->create();

        $this->get('/')
            ->assertOk()
            ->assertSee($blog1->title)
            ->assertSee($blog2->title)
            ->assertSee($blog3->title);

        // Blog::factory()->create(['title' => 'あいうえお']);
        // Blog::factory()->create(['title' => 'かきくけこ']);
        // Blog::factory()->create(['title' => 'さしすせそ']);

        // $this->get('/')
        //     ->assertOk()
        //     ->assertSee('あいうえお')
        //     ->assertSee('かきくけこ')
        //     ->assertSee('さしすせそ');


    }
}
