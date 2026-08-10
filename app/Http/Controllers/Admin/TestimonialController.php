<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Testimonial::orderBy('sort_order');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $query->where(fn ($q) => $q
                ->where('author_name', 'like', "%{$s}%")
                ->orWhere('author_role', 'like', "%{$s}%")
                ->orWhere('quote', 'like', "%{$s}%"));
        }
        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }

        return Inertia::render('admin/testimonials/Index', [
            'testimonials' => $query->paginate(12)->withQueryString(),
            'filters' => [
                'search' => $request->string('search')->toString() ?: null,
                'rating' => $request->integer('rating') ?: null,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/testimonials/Form', ['testimonial' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Testimonial::create($this->validateData($request));

        $this->toast('Testimoni ditambahkan.');

        return redirect()->route('admin.testimonials.index');
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('admin/testimonials/Form', ['testimonial' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->validateData($request));

        $this->toast('Testimoni diperbarui.');

        return redirect()->route('admin.testimonials.index');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        $this->toast('Testimoni dihapus.');

        return back();
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_role' => ['nullable', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
