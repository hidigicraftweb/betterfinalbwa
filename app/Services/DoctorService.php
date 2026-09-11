<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use App\Repositories\HospitalSpecialistRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DoctorService
{
    private $doctorRepository;
    private $hospitalSpecialistRepository;

    // Constructor injection of the DoctorRepository and HospitalSpecialistRepository
    public function __construct(DoctorRepository $doctorRepository, HospitalSpecialistRepository $hospitalSpecialistRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->hospitalSpecialistRepository = $hospitalSpecialistRepository;
    }

    // Method to get all doctors with specified fields
    public function getAll(array $fields)
    {
        return $this->doctorRepository->getAll($fields);
    }

    // Method to get a doctor by ID with specified fields
    public function getById($id, array $fields)
    {
        return $this->doctorRepository->getById($id, $fields);
    }

    // Method to create a new doctor with optional photo upload
    public function create(array $data)
    {
        // Validate that the selected specialist is available in the selected hospital
        if (!$this->hospitalSpecialistRepository->existsForHospitalAndSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages([
                'specialist_id' => ['The selected specialist is not available in the selected hospital.'],
            ]);
        }

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->doctorRepository->create($data);
    }
    
    // Method to update a doctor by ID with optional photo upload
    public function update($id, array $data)
    {
        // Validate that the selected specialist is available in the selected hospital
        if (!$this->hospitalSpecialistRepository->existsForHospitalAndSpecialist($data['hospital_id'], $data['specialist_id'])) {
            throw ValidationException::withMessages([
                'specialist_id' => ['The selected specialist is not available in the selected hospital.'],
            ]);
        }

        $doctor = $this->doctorRepository->getById($id, ['*']);
        
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            
            // Delete old photo if it exists
            if (!empty($doctor->photo)) {
                $this->deletePhoto($doctor->photo);
            }

            // Upload new photo
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        
        return $this->doctorRepository->update($id, $data);
    }

    // Method to delete a doctor by ID and its associated photo
    public function delete($id)
    {
        $doctor = $this->doctorRepository->getById($id, ['*']);
        
        // Delete associated photo if it exists
        if ($doctor->photo) {
            $this->deletePhoto($doctor->photo);
        }
        
        return $this->doctorRepository->delete($id);
    }

    // Helper method to handle photo upload and return the stored path
    private function uploadPhoto(UploadedFile $photo): string // type hint for return value
    {
        return $photo->store('doctors', 'public'); // yourdomain.com/storage/doctors/filename.jpg
    }

    // Helper method to delete a photo from storage
    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'doctors/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    // Method to filter doctors by specialist and hospital
    public function filterBySpecialistAndHospital(int $specialistId, int $hospitalId)
    {
        return $this->doctorRepository->filterBySpecialistAndHospital($specialistId, $hospitalId);
    }

    // Method to get available time slots for a doctor based on existing bookings
    public function getAvailableSlots(int $doctorId) // 11
    {
        $doctor = $this->doctorRepository->getById($doctorId, ['id']);
        
        $dates = collect([
            now()->addDays(1)->startOfDay(), // 21 Apr
            now()->addDays(2)->startOfDay(), // 22 Apr
            now()->addDays(3)->startOfDay(), // 23 Apr
        ]);

        $timeSlots = ['10:30', '11:30', '13:30', '14:30', '15:30', '16:30'];
        
        $availability = [];

        foreach ($dates as $date) {
            $dateStr = $date->toDateString();
            $availability[$dateStr] = [];

            foreach ($timeSlots as $time) {
                $isTaken = $doctor->bookingTransactions()
                    ->whereDate('started_at', $dateStr)
                    ->whereTime('time_at', $time)
                    ->exists();

                if (!$isTaken) {
                    $availability[$dateStr][] = $time;
                }
            }
        }

        return $availability;
    }
}