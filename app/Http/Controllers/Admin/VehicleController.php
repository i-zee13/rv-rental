<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with(['translations','images','category'])->paginate(20);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();

        $vehicle = Vehicle::create([
            'category_id' => $data['category_id'] ?? null,
            'make' => $data['make'] ?? null,
            'model' => $data['model'] ?? null,
            'year' => $data['year'] ?? null,
            'price_per_day' => $data['price_per_day'] ?? 0,
            'seats' => $data['seats'] ?? 4,
            'bags' => $data['bags'] ?? 2,
            'status' => $data['status'] ?? 'available',
        ]);

        // handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('vehicles', 'public');
                $vehicle->images()->create([
                    'path' => Storage::url($path),
                    'alt_text' => $data['make'].' '.$data['model'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle created.');
    }

    public function edit($id)
    {
        $vehicle = Vehicle::with(['translations','images'])->findOrFail($id);
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(UpdateVehicleRequest $request, $id)
    {
        $data = $request->validated();

        $vehicle = Vehicle::findOrFail($id);

        $vehicle->update([
            'category_id' => $data['category_id'] ?? $vehicle->category_id,
            'make' => $data['make'] ?? $vehicle->make,
            'model' => $data['model'] ?? $vehicle->model,
            'year' => $data['year'] ?? $vehicle->year,
            'price_per_day' => $data['price_per_day'] ?? $vehicle->price_per_day,
            'seats' => $data['seats'] ?? $vehicle->seats,
            'bags' => $data['bags'] ?? $vehicle->bags,
            'status' => $data['status'] ?? $vehicle->status,
        ]);

        // handle new images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('vehicles', 'public');
                $vehicle->images()->create([
                    'path' => Storage::url($path),
                    'alt_text' => ($data['make'] ?? $vehicle->make) . ' ' . ($data['model'] ?? $vehicle->model),
                ]);
            }
        }

        // handle deletion of selected images (array of ids)
        if (!empty($data['delete_image_ids']) && is_array($data['delete_image_ids'])) {
            foreach ($data['delete_image_ids'] as $imgId) {
                $img = $vehicle->images()->where('id', $imgId)->first();
                if ($img) {
                    // attempt to unlink underlying file path if stored under /storage
                    try {
                        // convert Storage::url path back to storage path if possible
                        $url = $img->path;
                        if (strpos($url, '/storage/') === 0 || strpos($url, asset('storage')) === 0) {
                            $relative = str_replace(url('/storage') , '', $url);
                        }
                    } catch (\Throwable $e) {
                        // ignore
                    }
                    $img->delete();
                }
            }
        }

        return redirect()->route('admin.vehicles.edit', $vehicle->id)->with('success', 'Vehicle updated.');
    }

    public function destroyImage(Request $request, $vehicleId, $imageId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $img = $vehicle->images()->where('id', $imageId)->firstOrFail();
        $img->delete();
        return back()->with('success', 'Image removed.');
    }
}
