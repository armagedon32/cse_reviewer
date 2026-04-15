<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('questions', 'option_a')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('option_a')->default('Option A')->after('difficulty');
            });
        }
        if (! Schema::hasColumn('questions', 'option_b')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('option_b')->default('Option B')->after('option_a');
            });
        }
        if (! Schema::hasColumn('questions', 'option_c')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('option_c')->default('Option C')->after('option_b');
            });
        }
        if (! Schema::hasColumn('questions', 'option_d')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('option_d')->default('Option D')->after('option_c');
            });
        }
        if (! Schema::hasColumn('questions', 'correct_option')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('correct_option', 1)->default('A')->after('option_d');
            });
        }

        DB::table('questions')->update([
            'option_a' => DB::raw('COALESCE(option_a, "Option A")'),
            'option_b' => DB::raw('COALESCE(option_b, "Option B")'),
            'option_c' => DB::raw('COALESCE(option_c, "Option C")'),
            'option_d' => DB::raw('COALESCE(option_d, "Option D")'),
            'correct_option' => DB::raw('COALESCE(correct_option, "A")'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_option',
            ]);
        });
    }
};
