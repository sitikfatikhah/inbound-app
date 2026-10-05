<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Companies;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Companies::latest()->get();

        return Inertia::render('company/index', [
            'companies' => $companies,
        ]);
    }

    public function create()
    {
        return Inertia::render('company/create', [
            'companies' => Companies::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);

    }

    public function store(CompanyRequest $request)
    {
        Companies::create(
            $request->validated()
        );

        return redirect()
            ->route('company.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(Companies $company)
    {
        return Inertia::render('company/show', [
            'company' => $company,
        ]);
    }

    public function edit(Companies $company)
    {
        return Inertia::render('company/edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified shipment.
     */
    public function update(CompanyRequest $request, Companies $company)
    {
        $company->update($request->validated());

        return redirect()
            ->route('company.index')
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified shipment.
     */
    public function destroy(Companies $company)
    {
        $company->delete();

        return redirect()
            ->route('company.index')
            ->with('success', 'Company deleted successfully.');
    }
}
