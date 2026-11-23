<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->whereIn('status', ['delivered', 'payment_pending'])
            ->update(['status' => 'awaiting_payment']);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', 'awaiting_payment')
            ->update(['status' => 'payment_pending']);
    }
};
