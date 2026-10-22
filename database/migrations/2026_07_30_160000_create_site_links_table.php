<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_links', function (Blueprint $table) {
            $table->id();
            $table->string('title');           // عنوان الرابط (مثال: سناب شات | snapchat)
            $table->string('subtitle')->nullable(); // عنوان فرعي (مثال: سناب شات)
            $table->string('url');             // رابط الموقع
            $table->string('icon')->nullable(); // أيقونة Font Awesome (مثال: fa-brands fa-snapchat)
            $table->string('icon_bg_color')->default('#333333'); // لون خلفية الأيقونة
            $table->string('type')->default('link'); // نوع: link, social, announcement, menu, whatsapp, location
            $table->string('image')->nullable();     // صورة للإعلان إن وجد
            $table->text('description')->nullable(); // وصف إضافي
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_links');
    }
};
