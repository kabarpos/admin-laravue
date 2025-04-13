<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            
            // Pengaturan umum
            $table->string('site_name')->nullable();
            $table->string('site_subtitle')->nullable();
            $table->string('site_description')->nullable();
            $table->string('homepage_route')->default('/');
            
            // Kontak & Footer
            $table->string('contact_email')->nullable();
            $table->string('copyright_text')->nullable();
            $table->year('copyright_year')->nullable();
            
            // Media/Gambar
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('default_og_image_path')->nullable(); // Meta image default
            
            // Script tracking & analytics
            $table->text('header_scripts')->nullable(); // Google Analytics, dsb.
            $table->text('meta_pixel_script')->nullable();
            $table->text('tiktok_pixel_script')->nullable();
            $table->text('google_tag_script')->nullable();
            $table->text('footer_scripts')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
