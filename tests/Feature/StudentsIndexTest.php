<?php

namespace Tests\Feature;

use App\Models\MyClass;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/students')->assertRedirect('/login');
    }

    public function test_index_redirects_to_first_class_list(): void
    {
        $this->seed();

        $admin = User::where('user_type', 'super_admin')->firstOrFail();
        $first = MyClass::orderBy('name', 'asc')->firstOrFail();

        $this->actingAs($admin)
            ->get('/students')
            ->assertRedirect(route('students.list', $first->id));
    }

    public function test_public_disk_root_is_public_storage(): void
    {
        // Uploads must land in public/storage so they are servable on hosts
        // without symlink support (no `storage:link`).
        $this->assertSame(public_path('storage'), rtrim(Storage::disk('public')->path(''), '/'));
    }
}
