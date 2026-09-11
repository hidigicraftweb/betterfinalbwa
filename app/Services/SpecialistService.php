<?php

namespace App\Services;

use App\Repositories\SpecialistRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SpecialistService
{
    private $specialistRepository;

    // Constructor injection of the SpecialistRepository
    public function __construct(SpecialistRepository $specialistRepository)
    {
        $this->specialistRepository = $specialistRepository;
    }

    // Method to get all specialists with specified fields
    public function getAll(array $fields)
    {
        return $this->specialistRepository->getAll($fields);
    }

    // Method to get a specialist by ID with specified fields
    public function getById($id, array $fields)
    {
        return $this->specialistRepository->getById($id, $fields);
    }

    // Method to create a new specialist with optional photo upload
    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        
        return $this->specialistRepository->create($data);
    }
    
    // Method to update a specialist by ID with optional photo upload
    public function update($id, array $data)
    {
        $fields = ['*']; // select all fields to get the current photo path
        $specialist = $this->specialistRepository->getById($id, $fields);
        
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            
            // Delete old photo if it exists
            if (!empty($specialist->photo)) {
                $this->deletePhoto($specialist->photo);
            }

            // Upload new photo
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        
        return $this->specialistRepository->update($id, $data);
    }

    // Method to delete a specialist by ID and its associated photo
    public function delete($id)
    {
        $fields = ['*']; // select all fields to get the current photo path
        $specialist = $this->specialistRepository->getById($id, $fields);
        
        // Delete associated photo if it exists
        if ($specialist->photo) {
            $this->deletePhoto($specialist->photo);
        }
        
        return $this->specialistRepository->delete($id);
    }

    // Helper method to handle photo upload and return the stored path
    private function uploadPhoto(UploadedFile $photo): string // type hint for return value
    {
        return $photo->store('specialists', 'public'); // yourdomain.com/storage/specialists/filename.jpg
    }

    // Helper method to delete a photo from storage
    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'specialists/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}