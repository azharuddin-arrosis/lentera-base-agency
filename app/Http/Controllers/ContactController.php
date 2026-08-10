<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        $this->seo([
            'title' => 'Kontak & Konsultasi — Lentera Project',
            'description' => 'Hubungi tim Lentera Project untuk konsultasi trading, kerjasama, atau pertanyaan seputar sinyal & mentoring.',
        ]);

        return Inertia::render('public/Contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Lead::create($data);

        $this->toast('Terima kasih! Pesan Anda sudah kami terima. Tim akan menghubungi Anda segera.');

        return back();
    }
}
