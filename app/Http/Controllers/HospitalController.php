<?php

namespace App\Http\Controllers;

use App\Http\Requests\HospitalRequest;
use App\Http\Resources\HospitalResource;
use App\Services\HospitalService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    private $hospitalService;

    // Constructor injection of the HospitalService
    public function __construct(HospitalService $hospitalService)
    {
        $this->hospitalService = $hospitalService;
    }

    // Method to get all hospitals with specified fields
    public function index()
    {
        $fields = ['id', 'name', 'photo', 'city', 'phone']; // specify the fields you want to retrieve
        $hospitals = $this->hospitalService->getAll($fields);
        return response()->json(HospitalResource::collection($hospitals));
    }

    // Method to get a hospital by ID with specified fields
    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $hospital = $this->hospitalService->getById($id, $fields);
            return response()->json(new HospitalResource($hospital));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }
    }
    
    // Method to create a new hospital with optional photo upload
    public function store(HospitalRequest $request)
    {
        $hospital = $this->hospitalService->create($request->validated());
        return response()->json(new HospitalResource($hospital), 201);
    }

    // Method to update a hospital by ID with optional photo upload
    public function update(HospitalRequest $request, int $id)
    {
        try {
            $hospital = $this->hospitalService->update($id, $request->validated());
            return response()->json(new HospitalResource($hospital));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }
    }

    // Method to delete a hospital by ID
    public function destroy(int $id)
    {
        try {
            $this->hospitalService->delete($id);
            return response()->json(['message' => 'Hospital deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }
    }
}
