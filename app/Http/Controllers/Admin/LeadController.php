<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Lead::orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('subject', 'like', "%{$s}%")
                ->orWhere('message', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $leads = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/Leads', [
            'leads' => $leads->through(fn ($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'email' => $l->email,
                'phone' => $l->phone,
                'subject' => $l->subject,
                'message' => $l->message,
                'status' => $l->status,
                'created_at' => $l->created_at?->format('d M Y H:i'),
            ]),
            'filters' => [
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ],
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:new,read,done']]);
        $lead->update($data);

        $this->toast('Status pesan diperbarui.');

        return back();
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        $this->toast('Pesan dihapus.');

        return back();
    }
}
