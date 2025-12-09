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
        Schema::table('insurance_policies', function (Blueprint $table) {
            $table->string('insurance_category')->nullable()->after('coverage_type'); // life, health, motor, travel, home, term
            $table->decimal('insured_value', 15, 2)->nullable()->after('coverage_amount');
            $table->decimal('deductible_amount', 10, 2)->nullable()->after('insured_value');
            $table->string('payment_frequency')->nullable()->after('deductible_amount'); // monthly, quarterly, annual
            $table->unsignedBigInteger('agent_id')->nullable()->after('payment_frequency');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('agent_id');
            $table->decimal('commission_amount', 10, 2)->nullable()->after('commission_rate');
            $table->boolean('renewal_reminder_sent')->default(false)->after('commission_amount');
            $table->date('next_renewal_date')->nullable()->after('renewal_reminder_sent');
            $table->string('policy_type')->nullable()->after('next_renewal_date'); // individual, family, business
            $table->string('coverage_area')->nullable()->after('policy_type'); // geographic coverage
            $table->json('network_hospitals')->nullable()->after('coverage_area'); // for health insurance
            $table->boolean('zero_depreciation')->default(false)->after('network_hospitals'); // for motor insurance
            $table->boolean('ncb_protector')->default(false)->after('zero_depreciation'); // for motor insurance
            $table->json('policy_documents')->nullable()->after('ncb_protector'); // store policy document paths
            $table->string('claim_status')->nullable()->after('policy_documents'); // pending, approved, rejected
            $table->decimal('claim_amount', 10, 2)->nullable()->after('claim_status');
            $table->date('claim_date')->nullable()->after('claim_amount');

            // Add indexes for better performance
            $table->index('insurance_category');
            $table->index('agent_id');
            $table->index('policy_type');
            $table->index('next_renewal_date');
            $table->index('claim_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_policies', function (Blueprint $table) {
            $table->dropIndex(['insurance_category']);
            $table->dropIndex(['agent_id']);
            $table->dropIndex(['policy_type']);
            $table->dropIndex(['next_renewal_date']);
            $table->dropIndex(['claim_status']);

            $table->dropColumn([
                'insurance_category',
                'insured_value',
                'deductible_amount',
                'payment_frequency',
                'agent_id',
                'commission_rate',
                'commission_amount',
                'renewal_reminder_sent',
                'next_renewal_date',
                'policy_type',
                'coverage_area',
                'network_hospitals',
                'zero_depreciation',
                'ncb_protector',
                'policy_documents',
                'claim_status',
                'claim_amount',
                'claim_date',
            ]);
        });
    }
};
