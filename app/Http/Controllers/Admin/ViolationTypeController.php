<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ViolationType;
use Illuminate\Http\Request;

class ViolationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $violationTypes = ViolationType::latest()->get();
        return view('admin.violation_types.index', compact('violationTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.violation_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:violation_types',
            'description' => 'nullable|string',
        ]);

        ViolationType::create($validated);

        return redirect()->route('admin.violation-types.index')->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ViolationType $violationType)
    {
        return view('admin.violation_types.edit', compact('violationType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ViolationType $violationType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:violation_types,name,' . $violationType->id,
            'description' => 'nullable|string',
        ]);

        $violationType->update($validated);

        return redirect()->route('admin.violation-types.index')->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ViolationType $violationType)
    {
        if ($violationType->participants()->exists()) {
            return redirect()->route('admin.violation-types.index')->with('error', 'Tidak dapat menghapus jenis pelanggaran karena sedang digunakan oleh peserta.');
        }

        $violationType->delete();

        return redirect()->route('admin.violation-types.index')->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
