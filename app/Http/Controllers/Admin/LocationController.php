<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $this->getPerPage($request, 'locations_per_page', 10);
        $locations = Location::withCount('attendanceLogs')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate($perPage);

        return view('admin.locations.index', compact('locations'));
    }

    private function getPerPage(Request $request, string $key, int $default = 10): int
    {
        $allowed = [5, 10, 15, 20];
        $perPage = $request->query('per_page', session($key, $default));
        $perPage = in_array((int) $perPage, $allowed) ? (int) $perPage : $default;
        session([$key => $perPage]);

        return $perPage;
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:50|max:500',
        ]);

        Location::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:50|max:500',
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function toggle(Location $location): RedirectResponse
    {
        $location->update(['is_active' => ! $location->is_active]);

        $status = $location->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Lokasi \"{$location->name}\" berhasil {$status}.");
    }
}
