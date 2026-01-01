<?php

namespace Modules\Gallery\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Core\Tenant\TenantModule;
use App\Core\Users\User;
use Modules\Gallery\Entities\Album;
use Modules\Gallery\Entities\CustomGroup;
use Modules\Gallery\Entities\MediaTag;
use Modules\Gallery\Entities\Slideshow;
use Illuminate\Support\Str;

class GalleryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;

        // Activate Gallery module for tenant 1
        TenantModule::updateOrCreate(
            ['tenant_id' => $tenantId, 'module_slug' => 'gallery'],
            ['enabled' => true, 'enabled_at' => now(), 'settings' => []]
        );

        $user = User::where('tenant_id', $tenantId)->first();
        if (!$user) {
            return;
        }

        // Tags
        $tags = ['trekking', 'panorama', 'gruppo'];
        foreach ($tags as $tagName) {
            MediaTag::firstOrCreate(
                ['tenant_id' => $tenantId, 'slug' => Str::slug($tagName)],
                [
                    'name' => ucfirst($tagName), 
                    'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))
                ]
            );
        }

        // Album Selvaggio Blu 2024 (participants)
        $sbAlbum = Album::updateOrCreate(
            ['tenant_id' => $tenantId, 'slug' => 'selvaggio-blu-2024'],
            [
                'name' => 'Selvaggio Blu 2024',
                'description' => 'Trekking estremo in Sardegna',
                'visibility' => 'participants',
                'created_by' => $user->id,
                'is_collaborative' => true,
                'download_enabled' => true,
                'allow_comments' => true,
                'allow_likes' => true,
            ]
        );

        // Album Foto Pubbliche (public)
        $publicAlbum = Album::updateOrCreate(
            ['tenant_id' => $tenantId, 'slug' => 'foto-pubbliche'],
            [
                'name' => 'Foto Pubbliche',
                'description' => 'Raccolta pubblica di eventi',
                'visibility' => 'public',
                'created_by' => $user->id,
                'is_collaborative' => false,
                'download_enabled' => true,
                'allow_comments' => true,
                'allow_likes' => true,
            ]
        );

        // Custom Group Trek Maggio
        CustomGroup::updateOrCreate(
            ['tenant_id' => $tenantId, 'slug' => 'trek-maggio'],
            [
                'name' => 'Trek Maggio',
                'description' => 'Gruppo per il trek di Maggio',
                'color' => '#4CAF50',
                'created_by' => $user->id,
            ]
        );

        // Slideshow Demo
        Slideshow::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Demo Slideshow'],
            [
                'album_id' => $publicAlbum->id,
                'duration_per_slide' => 5,
                'transition_type' => 'fade',
                'auto_play' => true,
                'loop' => true,
                'show_captions' => true,
                'created_by' => $user->id,
            ]
        );
    }
}
