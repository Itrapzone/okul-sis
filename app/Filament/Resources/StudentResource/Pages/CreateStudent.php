<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = trim(collect([$data['first_name'] ?? null, $data['last_name'] ?? null])->filter()->join(' '));
        $data['registered_by'] = $data['registered_by'] ?? Auth::user()?->name;

        if (blank($data['school_number'] ?? null)) {
            $data['school_number'] = StudentResource::generateSchoolNumber($data['education_year'] ?? null);
        }

        $data['takes_meal_service'] = (bool) ($data['takes_meal_service'] ?? false);
        $data['uses_transport'] = (bool) ($data['uses_transport'] ?? false);
        $data['guardian_receive_sms'] = (bool) ($data['guardian_receive_sms'] ?? false);

        foreach (['enrollment_mode', 'existing_student_id', 'guardian_mode', 'guardian_copy_student_id'] as $transientField) {
            unset($data[$transientField]);
        }

        return $data;
    }
}
