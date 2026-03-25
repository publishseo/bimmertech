<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareVersion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoftwareAdminController extends Controller
{
    public function index()
    {
        $latest_versions = SoftwareVersion::latest()->get();

        return Inertia::render('Admin/Software/Index', [
            'versions' =>  $latest_versions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'system_version' => 'required|string',
            'system_version_alt' => 'required|string',
            'link' => 'nullable|string',
            'st' => 'nullable|string',
            'gd' => 'nullable|string',
            'latest' => 'required|boolean',
        ]);

        SoftwareVersion::create($data);
        return back()->with('success', 'Version added successfully');
    }

    public function update(Request $request, SoftwareVersion $softwareVersion)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'system_version' => 'required|string',
            'system_version_alt' => 'required|string',
            'link' => 'nullable|string',
            'st' => 'nullable|string',
            'gd' => 'nullable|string',
            'latest' => 'required|boolean',
        ]);

        $softwareVersion->update($data);
        return back()->with('success', 'Version updated successfully');
    }

    public function destroy(SoftwareVersion $softwareVersion)
    {
        $softwareVersion->delete();
        return back();
    }
}
