<?php

namespace App\Repositories;

use App\Models\BookingTransaction;

class BookingTransactionRepository
{
    // Logic to retrieve all booking transactions from the database for managers
    public function getAll()
    {
        return BookingTransaction::with(['doctor', 'doctor.specialist', 'doctor.hospital', 'user'])->latest()->paginate(10);
    }

    // Logic to retrieve a booking transaction by ID from the database for managers
    public function getByIdForManager(int $id)
    {
        return BookingTransaction::with(['doctor', 'doctor.specialist', 'doctor.hospital', 'user'])->findOrFail($id);
    }

    // Logic to update the status of a booking transaction by ID for managers
    public function updateStatus(int $id, string $status)
    {
        $transaction = $this->getByIdForManager($id);
        $transaction->update(['status' => $status]);
        return $transaction;
    }

    // Logic to retrieve all booking transactions from the database for a specific user
    public function getAllForUser(int $userId)
    {
        return BookingTransaction::where('user_id', $userId)
            ->with(['doctor', 'doctor.specialist', 'doctor.hospital'])
            ->latest()
            ->paginate(50);
    }

    // Logic to retrieve a booking transaction by ID from the database for a specific user
    public function getById(int $id, int $userId)
    {
        return BookingTransaction::where('id', $id)
            ->where('user_id', $userId)
            ->with(['doctor', 'doctor.specialist', 'doctor.hospital'])
            ->findOrFail($id);
    }

    // Logic to create a new booking transaction in the database for a specific user
    public function create(array $data)
    {
        return BookingTransaction::create($data);
    }

    // Logic to check if a time slot is already taken for a doctor
    public function isTimeSlotTakenForDoctor(int $doctorId, string $date, string $time)
    {
        return BookingTransaction::where('doctor_id', $doctorId)
            ->where('started_at', $date)
            ->where('time_at', $time)
            ->exists();
    }
}