<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'campus_id',
        'uid',
        'name',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'status',
        'note',
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
    ];

    protected $casts = [
        'birth_date' => 'date', // DatePicker'dan gelen değeri düzgün işler
        'entry_date' => 'date',
        'takes_meal_service' => 'boolean',
        'uses_transport' => 'boolean',
        'guardian_receive_sms' => 'boolean',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];

    protected $appends = [
        'full_name',
    ];

    // Boş string 'campus_id' → null'a çevir (PG int tip hatasını önler)
    public function setCampusIdAttribute($value): void
    {
        $this->attributes['campus_id'] = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function getFullNameAttribute(): string
    {
        $fullName = trim(collect([$this->first_name, $this->last_name])->filter()->join(' '));

        return $fullName !== '' ? $fullName : (string) $this->name;
    }
}
