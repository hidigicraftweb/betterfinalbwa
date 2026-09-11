<?php

namespace App\Repositories;

use App\Models\Hospital;

class HospitalRepository
{
    // Logic to retrieve all hospitals from the database
    public function getAll(array $fields)
    {
        return Hospital::select($fields)->latest()->paginate(10);
    }

    // Logic to retrieve a hospital by ID from the database
    public function getById($id, array $fields)
    {
        return Hospital::select($fields)->with(['doctors.specialist', 'specialists' ])->findOrFail($id);
    }

    // Logic to create a new hospital in the database
    public function create(array $data)
    {
        return Hospital::create($data);
    }

    // Logic to update a hospital in the database
    public function update($id, array $data)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->update($data);
        return $hospital;
    }

    // Logic to delete a hospital from the database
    public function delete($id)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->delete();
    }
}