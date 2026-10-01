<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn', 50)->change();
            $table->text('synopsis')->nullable()->change();
        });

        if (! Schema::hasColumn('books', 'deleted_at')) {
            Schema::table('books', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('books', 'deleted_at')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn', 12)->change();
            $table->string('synopsis')->nullable()->change();
        });
    }
};
