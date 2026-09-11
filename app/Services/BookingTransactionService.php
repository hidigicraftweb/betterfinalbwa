<?php

namespace App\Services;

use App\Repositories\BookingTransactionRepository;
use App\Repositories\DoctorRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookingTransactionService
{
    private $bookingTransactionRepository;
    private $doctorRepository;

    // Constructor injection of the BookingTransactionRepository and DoctorRepository
    public function __construct(BookingTransactionRepository $bookingTransactionRepository, DoctorRepository $doctorRepository)
    {
        $this->bookingTransactionRepository = $bookingTransactionRepository;
        $this->doctorRepository = $doctorRepository;
    }

    // Method to get all booking transactions
    public function getAll()
    {
        return $this->bookingTransactionRepository->getAll();
    }

    // Method to get a booking transaction by ID for manager view
    public function getByIdForManager(int $id)
    {
        return $this->bookingTransactionRepository->getByIdForManager($id);
    }

    // Method to update the status of a booking transaction by ID for managers
    public function updateStatus(int $id, string $status)
    {
        if (!in_array($status, ['Approved', 'Rejected'])) {
            throw ValidationException::withMessages(['status' => ['Invalid status value. Allowed values are: Approved, Rejected.']]);
        }

        return $this->bookingTransactionRepository->updateStatus($id, $status);
    }

    // Method to get all booking transactions for a specific user
    public function getAllForUser(int $id)
    {
        return $this->bookingTransactionRepository->getAllForUser($id);
    }

    // Method to get a booking transaction by ID for a specific user
    public function getById(int $id, int $userId)
    {
        return $this->bookingTransactionRepository->getById($id, $userId);
    }

    // Method to create a new booking transaction for a specific user
    public function create(array $data)
    {
        $data['user_id'] = auth()->id();

        // Check if the time slot is already taken for the doctor
        if ($this->bookingTransactionRepository->isTimeSlotTakenForDoctor($data['doctor_id'], $data['started_at'], $data['time_at'])) {
            throw ValidationException::withMessages(['time_slot' => ['The selected time slot is already taken for this doctor. Please choose a different time.']]);
        }

        $doctor = $this->doctorRepository->getById($data['doctor_id'], ['*']);

        $price = $doctor->specialist->price;
        $tax = (int) round($price * 0.11);
        $grand = $price + $tax;

        $data['sub_total'] = $price;
        $data['tax_total'] = $tax;
        $data['grand_total'] = $grand;

        $data['status'] = 'Waiting' ;

        if (isset($data['proof']) && $data['proof'] instanceof UploadedFile) {
            $data['proof'] = $this->uploadProof($data['proof']);
        }

        return $this->bookingTransactionRepository->create($data);
    }

    // Helper method to handle proof upload and return the stored path
    private function uploadProof(UploadedFile $file): string
    {
        return $file->store('proofs', 'public');
    }
}