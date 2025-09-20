<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('gender', 20)->nullable()->after('birth_date');
            $table->string('photo_path')->nullable()->after('gender');
            $table->string('registration_type', 50)->nullable()->after('photo_path');
            $table->string('identity_type', 50)->nullable()->after('registration_type');
            $table->string('identity_number', 50)->nullable()->after('identity_type');
            $table->string('email', 150)->nullable()->after('identity_number');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('school_name', 120)->nullable()->after('phone');
            $table->string('education_year', 20)->nullable()->after('school_name');
            $table->string('school_number', 50)->nullable()->after('education_year');
            $table->string('class_level', 30)->nullable()->after('school_number');
            $table->string('section', 30)->nullable()->after('class_level');
            $table->string('registered_by', 150)->nullable()->after('section');
            $table->date('entry_date')->nullable()->after('registered_by');
            $table->string('entry_type', 50)->nullable()->after('entry_date');
            $table->string('education_type', 50)->nullable()->after('entry_type');
            $table->string('foreign_language', 50)->nullable()->after('education_type');
            $table->string('previous_school', 150)->nullable()->after('foreign_language');
            $table->string('reference', 150)->nullable()->after('previous_school');
            $table->string('heard_from', 150)->nullable()->after('reference');
            $table->boolean('takes_meal_service')->default(false)->after('heard_from');
            $table->string('education_preference', 100)->nullable()->after('takes_meal_service');
            $table->boolean('uses_transport')->default(false)->after('education_preference');
            $table->string('second_language', 50)->nullable()->after('uses_transport');
            $table->string('address_line1')->nullable()->after('second_language');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('country', 100)->nullable()->after('address_line2');
            $table->string('province', 100)->nullable()->after('country');
            $table->string('district', 100)->nullable()->after('province');
            $table->string('subdistrict', 100)->nullable()->after('district');
            $table->string('neighborhood', 100)->nullable()->after('subdistrict');
            $table->string('postal_code', 20)->nullable()->after('neighborhood');
            $table->decimal('latitude', 10, 6)->nullable()->after('postal_code');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            $table->string('guardian_relationship', 50)->nullable()->after('longitude');
            $table->string('guardian_national_id', 50)->nullable()->after('guardian_relationship');
            $table->string('guardian_first_name', 100)->nullable()->after('guardian_national_id');
            $table->string('guardian_last_name', 100)->nullable()->after('guardian_first_name');
            $table->string('guardian_email', 150)->nullable()->after('guardian_last_name');
            $table->string('guardian_phone', 30)->nullable()->after('guardian_email');
            $table->boolean('guardian_receive_sms')->default(false)->after('guardian_phone');
            $table->string('guardian_education_level', 150)->nullable()->after('guardian_receive_sms');
            $table->string('guardian_profession_group', 150)->nullable()->after('guardian_education_level');
            $table->string('guardian_work_type', 150)->nullable()->after('guardian_profession_group');
            $table->string('guardian_profession', 150)->nullable()->after('guardian_work_type');
            $table->string('guardian_job_title', 150)->nullable()->after('guardian_profession');
            $table->string('guardian_employer', 150)->nullable()->after('guardian_job_title');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'gender',
                'photo_path',
                'registration_type',
                'identity_type',
                'identity_number',
                'email',
                'phone',
                'school_name',
                'education_year',
                'school_number',
                'class_level',
                'section',
                'registered_by',
                'entry_date',
                'entry_type',
                'education_type',
                'foreign_language',
                'previous_school',
                'reference',
                'heard_from',
                'takes_meal_service',
                'education_preference',
                'uses_transport',
                'second_language',
                'address_line1',
                'address_line2',
                'country',
                'province',
                'district',
                'subdistrict',
                'neighborhood',
                'postal_code',
                'latitude',
                'longitude',
                'guardian_relationship',
                'guardian_national_id',
                'guardian_first_name',
                'guardian_last_name',
                'guardian_email',
                'guardian_phone',
                'guardian_receive_sms',
                'guardian_education_level',
                'guardian_profession_group',
                'guardian_work_type',
                'guardian_profession',
                'guardian_job_title',
                'guardian_employer',
            ]);
        });
    }
};
