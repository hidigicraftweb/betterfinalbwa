<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialistRequest;
use App\Http\Resources\SpecialistResource;
use App\Services\SpecialistService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    private $specialistService;

    // Constructor injection of the SpecialistService
    public function __construct(SpecialistService $specialistService)
    {
        $this->specialistService = $specialistService;
    }

    // Method to get all specialists with specified fields
    public function index()
    {
        $fields = ['id', 'name', 'photo', 'price']; // specify the fields you want to retrieve
        $specialists = $this->specialistService->getAll($fields);
        return response()->json(SpecialistResource::collection($specialists));
    }

    // Method to get a specialist by ID with specified fields
    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $specialist = $this->specialistService->getById($id, $fields);
            return response()->json(new SpecialistResource($specialist));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }
    }

    // Method to create a new specialist with optional photo upload
    public function store(SpecialistRequest $request)
    {
        $specialist = $this->specialistService->create($request->validated());
        return response()->json(new SpecialistResource($specialist), 201);
    }

    // Method to update a specialist by ID with optional photo upload
    public function update(SpecialistRequest $request, int $id)
    {
        try {
            $specialist = $this->specialistService->update($id, $request->validated());
            return response()->json(new SpecialistResource($specialist));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }
    }

    // Method to delete a specialist by ID
    public function destroy(int $id)
    {
        try {
            $this->specialistService->delete($id);
            return response()->json(['message' => 'Specialist deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }
    }
}
