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
        if (! Schema::hasColumn('users', 'payment_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('payment_status')->default('approved')->after('role');
            });
        }
        if (! Schema::hasColumn('users', 'gcash_reference')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gcash_reference')->nullable()->after('payment_status');
            });
        }
        if (! Schema::hasColumn('users', 'gcash_receipt_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gcash_receipt_path')->nullable()->after('gcash_reference');
            });
        }
        if (! Schema::hasColumn('users', 'payment_submitted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('payment_submitted_at')->nullable()->after('gcash_receipt_path');
            });
        }
        if (! Schema::hasColumn('users', 'payment_approved_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('payment_approved_at')->nullable()->after('payment_submitted_at');
            });
        }

        DB::table('users')
            ->where('role', 'student')
            ->update([
                'payment_status' => 'approved',
                'payment_approved_at' => now(),
            ]);

        DB::table('users')
            ->where('role', 'admin')
            ->update([
                'payment_status' => 'approved',
                'payment_approved_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'gcash_reference',
                'gcash_receipt_path',
                'payment_submitted_at',
                'payment_approved_at',
            ]);
        });
    }
};
