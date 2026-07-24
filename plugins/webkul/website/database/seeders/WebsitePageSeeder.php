<?php

namespace Webkul\Website\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class WebsitePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('website_pages')->delete();

        $user = User::first();

        DB::table('website_pages')->insert([
            [
                'title'             => 'Home',
                'content'           => 'Home Content',
                'slug'              => 'home',
                'is_published'      => true,
                'is_header_visible' => false,
                'is_footer_visible' => false,
                'published_at'      => now(),
                'meta_title'        => 'Home',
                'meta_keywords'     => 'home',
                'meta_description'  => 'Home Description',
                'creator_id'        => $user?->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'title'             => 'About Us',
                'content'           => 'About Us Content',
                'slug'              => 'about-us',
                'is_published'      => true,
                'is_header_visible' => true,
                'is_footer_visible' => true,
                'published_at'      => now(),
                'meta_title'        => 'About Us',
                'meta_keywords'     => 'about us',
                'meta_description'  => 'About Us Description',
                'creator_id'        => $user?->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'title'             => 'Privacy Policy',
                'content'           => 'Privacy Policy Content',
                'slug'              => 'privacy-policy',
                'is_published'      => true,
                'is_header_visible' => false,
                'is_footer_visible' => true,
                'published_at'      => now(),
                'meta_title'        => 'Privacy Policy',
                'meta_keywords'     => 'privacy policy',
                'meta_description'  => 'Privacy Policy Description',
                'creator_id'        => $user?->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'title'             => 'Terms & Conditions',
                'content'           => 'Terms & Conditions Content',
                'slug'              => 'terms-conditions',
                'is_published'      => true,
                'is_header_visible' => false,
                'is_footer_visible' => true,
                'published_at'      => now(),
                'meta_title'        => 'Terms & Conditions',
                'meta_keywords'     => 'terms & conditions',
                'meta_description'  => 'Terms & Conditions Description',
                'creator_id'        => $user?->id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
