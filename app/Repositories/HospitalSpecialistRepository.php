<?php

namespace App\Repositories;

use App\Models\HospitalSpecialist;

class HospitalSpecialistRepository
{
    // Method to check if a specialist is available in a hospital
    public function existsForHospitalAndSpecialist(int $hospitalId, int $specialistId): bool
    {
        return HospitalSpecialist::where('hospital_id', $hospitalId)
            ->where('specialist_id', $specialistId)
            ->exists();
    }

    // Method to attach a specialist to a hospital
    public function attach(int $hospitalId, int $specialistId): void
    {
        HospitalSpecialist::firstOrCreate([
            'hospital_id' => $hospitalId,
            'specialist_id' => $specialistId,
        ]);
    }

    // Method to detach a specialist from a hospital
    public function detach(int $hospitalId, int $specialistId): void
    {
        HospitalSpecialist::where('hospital_id', $hospitalId)
            ->where('specialist_id', $specialistId)
            ->delete();
    }
}