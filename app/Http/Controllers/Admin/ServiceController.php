<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Service::orderBy('sort_order');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $query->where(fn ($q) => $q->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        return Inertia::render('admin/services/Index', [
            'services' => $query->paginate(12)->withQueryString(),
            'filters' => [
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/services/Form', ['service' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Service::create($this->validateData($request));

        $this->toast('Layanan ditambahkan.');

        return redirect()->route('admin.services.index');
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('admin/services/Form', ['service' => $service]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($this->validateData($request));

        $this->toast('Layanan diperbarui.');

        return redirect()->route('admin.services.index');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        $this->toast('Layanan dihapus.');

        return back();
    }

    protected function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:60'],
            'price_label' => ['nullable', 'string', 'max:120'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:180'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['features'] = array_values(array_filter($data['features'] ?? [], fn ($f) => filled($f)));

        return $data;
    }
}
