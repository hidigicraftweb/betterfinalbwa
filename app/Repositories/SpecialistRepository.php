<?php

namespace App\Repositories;

use App\Models\Specialist;

class SpecialistRepository
{
    // Logic to retrieve all specialists from the database
    public function getAll(array $fields)
    {
        return Specialist::select($fields)->latest()->with(['hospitals', 'doctors'])->paginate(10);
    }

    // Logic to retrieve a specialist by ID from the database
    public function getById($id, array $fields)
    {
        return Specialist::select($fields)
            ->with([
                'hospitals' => function ($q) use ($id) {
                    $q->withCount(['doctors as doctors_count' => function ($query) use ($id) {
                        $query->where('specialist_id', $id);
                    }]);
                },
                'doctors' => function ($q) {
                    $q->select('id', 'name', 'photo', 'yoe', 'gender', 'specialist_id', 'hospital_id')
                        ->with('hospital:id,name,city,post_code');
                }
            ])
            ->findOrFail($id);
    }

    // Logic to create a new specialist in the database
    public function create(array $data)
    {
        return Specialist::create($data);
    }

    // Logic to update a specialist in the database
    public function update($id, array $data)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->update($data);
        return $specialist;
    }

    // Logic to delete a specialist from the database
    public function delete($id)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->delete();
    }
} 