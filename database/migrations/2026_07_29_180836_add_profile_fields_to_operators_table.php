<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->string('contact_person_name')->nullable()->after('address');
            $table->string('contact_person_title')->nullable()->after('contact_person_name');
            $table->string('business_registration_number')->nullable()->after('contact_person_title');
            $table->date('business_registration_date')->nullable()->after('business_registration_number');
            $table->string('business_type')->nullable()->after('business_registration_date');
            $table->string('logo_path')->nullable()->after('business_type');
            $table->text('description')->nullable()->after('logo_path');
            $table->string('website')->nullable()->after('description');
            $table->string('insurance_certificate_path')->nullable()->after('website');
            $table->date('insurance_expiry_date')->nullable()->after('insurance_certificate_path');
            $table->string('business_license_path')->nullable()->after('insurance_expiry_date');
            $table->timestamp('business_license_verified_at')->nullable()->after('business_license_path');
            $table->string('tax_id_path')->nullable()->after('business_license_verified_at');
            $table->timestamp('tax_id_verified_at')->nullable()->after('tax_id_path');
        });
    }

    public function down(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn([
                'contact_person_name',
                'contact_person_title',
                'business_registration_number',
                'business_registration_date',
                'business_type',
                'logo_path',
                'description',
                'website',
                'insurance_certificate_path',
                'insurance_expiry_date',
                'business_license_path',
                'business_license_verified_at',
                'tax_id_path',
                'tax_id_verified_at',
            ]);
        });
    }
};