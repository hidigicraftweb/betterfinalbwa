<?php

namespace App\Http\Controllers;

use App\Services\HospitalSpecialistService;
use Illuminate\Http\Request;

class HospitalSpecialistController extends Controller
{
    private $hospitalSpecialistService;

    // Constructor injection of the HospitalSpecialistService
    public function __construct(HospitalSpecialistService $hospitalSpecialistService)
    {
        $this->hospitalSpecialistService = $hospitalSpecialistService;
    }

    // Method to attach a specialist to a hospital
    public function attach(Request $request, int $hospitalId)
    {
        $request->validate([
            'specialist_id' => 'required|exists:specialists,id',
        ]);

        $this->hospitalSpecialistService->attachSpecialist($hospitalId, $request->input('specialist_id'));

        return response()->json(['message' => 'Specialist attached successfully']);
    }

    // Method to detach a specialist from a hospital
    public function detach(int $hospitalId, int $specialistId)
    {
        $this->hospitalSpecialistService->detachSpecialist($hospitalId, $specialistId);

        return response()->json(['message' => 'Specialist detached successfully']);
    }
}