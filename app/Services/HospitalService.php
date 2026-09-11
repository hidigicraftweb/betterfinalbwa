<?php

namespace App\Services;

use App\Repositories\HospitalRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HospitalService
{
    private $hospitalRepository;

    // Constructor injection of the HospitalRepository
    public function __construct(HospitalRepository $hospitalRepository)
    {
        $this->hospitalRepository = $hospitalRepository;
    }

    // Method to get all hospitals with specified fields
    public function getAll(array $fields)
    {
        return $this->hospitalRepository->getAll($fields);
    }

    // Method to get a hospital by ID with specified fields
    public function getById($id, array $fields)
    {
        return $this->hospitalRepository->getById($id, $fields);
    }

    // Method to create a new hospital with optional photo upload
    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        
        return $this->hospitalRepository->create($data);
    }
    
    // Method to update a hospital by ID with optional photo upload
    public function update($id, array $data)
    {
        $fields = ['*']; // select all fields to get the current photo path
        $hospital = $this->hospitalRepository->getById($id, $fields);
        
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            
            // Delete old photo if it exists
            if (!empty($hospital->photo)) {
                $this->deletePhoto($hospital->photo);
            }

            // Upload new photo
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        
        return $this->hospitalRepository->update($id, $data);
    }

    // Method to delete a hospital by ID and its associated photo
    public function delete($id)
    {
        $fields = ['*']; // select all fields to get the current photo path
        $hospital = $this->hospitalRepository->getById($id, $fields);
        
        // Delete associated photo if it exists
        if ($hospital->photo) {
            $this->deletePhoto($hospital->photo);
        }
        
        return $this->hospitalRepository->delete($id);
    }

    // Helper method to handle photo upload and return the stored path
    private function uploadPhoto(UploadedFile $photo): string // type hint for return value
    {
        return $photo->store('hospitals', 'public'); // yourdomain.com/storage/hospitals/filename.jpg
    }

    // Helper method to delete a photo from storage
    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'hospitals/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    // Methods to manage the relationship between hospitals and specialists
    public function attachSpecialists($hospitalId, array $specialistId)
    {
        $hospital = $this->hospitalRepository->getById($hospitalId, ['id']);
        $hospital->specialists()->syncWithoutDetaching($specialistId);
    }

    // Method to detach specialists from a hospital
    public function detachSpecialists($hospitalId, array $specialistId)
    {
        $hospital = $this->hospitalRepository->getById($hospitalId, ['id']);
        $hospital->specialists()->detach($specialistId);
    }
}