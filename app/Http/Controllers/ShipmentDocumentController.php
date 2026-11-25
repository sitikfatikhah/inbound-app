<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentDocumentRequest;
use App\Http\Requests\UpdateShipmentDocumentRequest;
use App\Models\ShipmentDocuments;
use App\Models\Shipments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ShipmentDocumentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('shipment-documents/index', [
            'documents' => ShipmentDocuments::query()
                ->with('shipment:id,shipment_number')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create()
    {
        $shipment = Shipments::latest()->get();

        return Inertia::render('shipment-documents/create', [
            'shipment' => Shipments::query()
                ->select('id', 'bl_number', 'invoice_number')
                ->orderBy('bl_number')
                ->get(),
        
        ]);
    }

    public function store(StoreShipmentDocumentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $path = $request->file('document_file')->store(
            'shipment-documents/'.$validated['shipment_id'],
            'public',
        );

        try {
            $document = ShipmentDocuments::query()->create([
                ...$validated,
                'document_file' => $path,
                'verification_status' => 'pending',
            ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('shipments.show', $document->shipment_id)
            ->with('success', 'Shipment document uploaded and awaits verification.');
    }

    public function show(ShipmentDocuments $shipmentDocument): RedirectResponse
    {
        return redirect()->route('shipments.show', $shipmentDocument->shipment_id);
    }

    public function update(
        UpdateShipmentDocumentRequest $request,
        ShipmentDocuments $shipmentDocument,
    ): RedirectResponse {
        $validated = $request->validated();
        $oldPath = $shipmentDocument->document_file;
        $newPath = null;

        if ($request->hasFile('document_file')) {
            $newPath = $request->file('document_file')->store(
                'shipment-documents/'.$shipmentDocument->shipment_id,
                'public',
            );
            $validated['document_file'] = $newPath;
            $validated['verification_status'] = 'pending';
            $validated['verification_notes'] = null;
            $validated['verified_at'] = null;
            $validated['verified_by'] = null;
        } elseif (array_key_exists('verification_status', $validated)) {
            $validated['verified_at'] = $validated['verification_status'] === 'complete' ? now() : null;
            $validated['verified_by'] = $validated['verification_status'] === 'complete'
                ? $request->user()->id
                : null;
        }

        try {
            DB::transaction(fn () => $shipmentDocument->update($validated));
        } catch (Throwable $exception) {
            if ($newPath !== null) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath !== null && $oldPath !== null) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('success', 'Shipment document updated.');
    }

    public function destroy(ShipmentDocuments $shipmentDocument): RedirectResponse
    {
        $path = $shipmentDocument->document_file;
        $shipmentId = $shipmentDocument->shipment_id;

        DB::transaction(fn () => $shipmentDocument->delete());

        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('shipments.show', $shipmentId)
            ->with('success', 'Shipment document deleted.');
    }
}
