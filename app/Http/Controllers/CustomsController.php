<?php

namespace App\Http\Controllers;

use App\Contracts\CustomsServiceInterface;
use App\Models\CustomsClearances;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Pest\ArchPresets\Custom;

class CustomsController extends Controller
{
    public function __construct(
        protected CustomsServiceInterface $customs_service
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $custom = CustomsClearances::latest()->get();
        return Inertia::render('customClearance/Index', [
            'custom' => $custom,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('customClearance/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $custom = $this->customs_service->create(
            $request->validated()
        );

        return response()->json($custom);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $custom = CustomsClearances::findOrFail($id);

        return Inertia::render('CustomClearance/Show', [
            'customs' => $custom,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $custom = CustomsClearances::findOrFail($id);

        return Inertia::render('CustomClearance/Edit', [
            'customs' => $custom,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Custom $custom)
    {
        $custom = $this->customs_service->update(
            $custom,
            $request->validate()
        );
        return response()->json($custom);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->customs_service->delete($custom);
        return response()->json([
            'message' => 'Customs deleted successfully.'
        ]);
    }
}
