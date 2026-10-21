<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** Tidak ada halaman publik: beranda pun dijaga middleware auth. */
    public function test_guest_on_home_is_redirected_to_login()
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }
}
