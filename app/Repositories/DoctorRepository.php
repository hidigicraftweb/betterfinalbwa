<?php

namespace App\Repositories;

use App\Models\Doctor;

class DoctorRepository
{
    // Logic to retrieve all doctors from the database
    public function getAll(array $fields)
    {
        return Doctor::select($fields)->with(['specialist', 'hospital' ])->latest()->paginate(10);
    }

    // Logic to retrieve a doctor by ID from the database
    public function getById($id, array $fields)
    {
        return Doctor::select($fields)->with(['specialist', 'hospital', 'bookingTransactions.user' ])->findOrFail($id);
    }

    // Logic to create a new doctor in the database
    public function create(array $data)
    {
        return Doctor::create($data);
    }

    // Logic to update a doctor in the database
    public function update($id, array $data)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->update($data);
        return $doctor;
    }

    // Logic to delete a doctor from the database
    public function delete($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();
    }

    // Logic to filter doctors by specialist and hospital
    public function filterBySpecialistAndHospital(int $specialistId, int $hospitalId)
    {
        return Doctor::with(['specialist', 'hospital'])
            ->where('specialist_id', $specialistId)
            ->where('hospital_id', $hospitalId)
            // ->paginate(10);
            ->get();
    }
}