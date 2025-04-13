<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company_name');
            $table->string('slug')->unique();  // SEO-friendly URL
            $table->text('description');
            $table->string('meta_description')->nullable();  // SEO meta description
            $table->string('keywords')->nullable();  // Job-related keywords
            $table->integer('view_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->date('expiry_date')->nullable();  // Job expiry date (optional)
            $table->timestamps();
            $table->index('title');
            $table->index('company_name');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listings');
    }
}
