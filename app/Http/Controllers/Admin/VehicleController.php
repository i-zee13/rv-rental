<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Support\PublicMedia;
use Illuminate\Support\Str;
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

        $this->syncTranslations($vehicle, $data);

        // handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $vehicle->images()->create([
                    'path' => PublicMedia::store($file, 'vehicles'),
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

        $this->syncTranslations($vehicle, $data);

        // handle new images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $vehicle->images()->create([
                    'path' => PublicMedia::store($file, 'vehicles'),
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

    protected function syncTranslations(Vehicle $vehicle, array $data): void
    {
        $titleEn = trim($data['title_en'] ?? '');
        if ($titleEn === '') {
            $titleEn = trim(($data['make'] ?? $vehicle->make ?? '') . ' ' . ($data['model'] ?? $vehicle->model ?? ''));
        }

        $vehicle->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'title' => $titleEn,
                'description' => $data['description_en'] ?? null,
                'meta_title' => Str::limit($titleEn . ' | ' . config('app.name'), 255, ''),
                'meta_description' => isset($data['description_en'])
                    ? Str::limit(trim(strip_tags($data['description_en'])), 320, '')
                    : null,
            ]
        );

        $vehicle->translations()->updateOrCreate(
            ['locale' => 'es'],
            [
                'title' => trim($data['title_es'] ?? '') ?: $titleEn,
                'description' => $data['description_es'] ?? null,
                'meta_title' => Str::limit(trim($data['title_es'] ?? $titleEn) . ' | ' . config('app.name'), 255, ''),
                'meta_description' => isset($data['description_es'])
                    ? Str::limit(trim(strip_tags($data['description_es'])), 320, '')
                    : (isset($data['description_en']) ? Str::limit(trim(strip_tags($data['description_en'])), 320, '') : null),
            ]
        );
    }
}
