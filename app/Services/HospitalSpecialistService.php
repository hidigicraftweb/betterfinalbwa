<?php

namespace App\Services;

use App\Repositories\HospitalSpecialistRepository;

class HospitalSpecialistService
{
    private $hospitalSpecialistRepository;

    // Constructor injection of the HospitalSpecialistRepository
    public function __construct(HospitalSpecialistRepository $hospitalSpecialistRepository)
    {
        $this->hospitalSpecialistRepository = $hospitalSpecialistRepository;
    }

    // Method to attach a specialist to a hospital
    public function attachSpecialist(int $hospitalId, int $specialistId): void
    {
        $this->hospitalSpecialistRepository->attach($hospitalId, $specialistId);
    }

    // Method to detach a specialist from a hospital
    public function detachSpecialist(int $hospitalId, int $specialistId): void
    {
        $this->hospitalSpecialistRepository->detach($hospitalId, $specialistId);
    }
}