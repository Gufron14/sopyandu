<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\FamilyChildren;
use App\Models\FamilyParent;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['child', 'parent']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pendaftaran', '>=', $request->start_date);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pendaftaran', '<=', $request->end_date);
        }

        // Filter berdasarkan sasaran
        if ($request->filled('sasaran')) {
            $query->where('sasaran', $request->sasaran);
        }

        // Pencarian nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('child', function($childQuery) use ($search) {
                    $childQuery->where('fullname', 'like', "%{$search}%");
                })->orWhereHas('parent', function($parentQuery) use ($search) {
                    $parentQuery->where('mother_fullname', 'like', "%{$search}%");
                });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal_pendaftaran');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'nama') {
            // Sorting berdasarkan nama lebih kompleks karena bisa dari 2 tabel berbeda
            $query->leftJoin('family_children', 'pendaftarans.children_id', '=', 'family_children.id')
                  ->leftJoin('family_parents', 'pendaftarans.parent_id', '=', 'family_parents.id')
                  ->select('pendaftarans.*')
                  ->orderByRaw("COALESCE(family_children.fullname, family_parents.mother_fullname) {$sortOrder}");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $pendaftarans = $query->paginate(10)->withQueryString();

        // Data untuk dropdown
        $children = FamilyChildren::select('id', 'fullname')->get();
        $parents = FamilyParent::select('id', 'mother_fullname')->get(); // Asumsi semua bisa hamil

        return view('dashboard.master-data.pendaftaran.index', compact('pendaftarans', 'children', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pendaftaran' => 'required|date',
            'sasaran' => 'required|in:balita,ibu_hamil',
            'children_id' => 'required_if:sasaran,balita|exists:family_children,id',
            'parent_id' => 'required_if:sasaran,ibu_hamil|exists:family_parents,id',
            'status' => 'nullable|in:menunggu,sudah_dilayani',
        ]);

        $data = $request->only(['tanggal_pendaftaran', 'sasaran', 'status']);
        
        if ($request->sasaran === 'balita') {
            $data['children_id'] = $request->children_id;
            $data['parent_id'] = null;
        } else {
            $data['parent_id'] = $request->parent_id;
            $data['children_id'] = null;
        }

        Pendaftaran::create($data);

        return redirect()->back()->with('success', 'Data pendaftaran berhasil ditambahkan');
    }

    public function show($id)
    {
        $pendaftaran = Pendaftaran::with(['child', 'parent'])->findOrFail($id);
        return response()->json($pendaftaran);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_pendaftaran' => 'required|date',
            'sasaran' => 'required|in:balita,ibu_hamil',
            'children_id' => 'required_if:sasaran,balita|exists:family_children,id',
            'parent_id' => 'required_if:sasaran,ibu_hamil|exists:family_parents,id',
            'status' => 'required|in:menunggu,sudah_dilayani',
        ]);

        $pendaftaran = Pendaftaran::findOrFail($id);
        
        $data = $request->only(['tanggal_pendaftaran', 'sasaran', 'status']);
        
        if ($request->sasaran === 'balita') {
            $data['children_id'] = $request->children_id;
            $data['parent_id'] = null;
        } else {
            $data['parent_id'] = $request->parent_id;
            $data['children_id'] = null;
        }

        $pendaftaran->update($data);

        return redirect()->back()->with('success', 'Data pendaftaran berhasil diubah');
    }

    public function destroy($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        $pendaftaran->delete();

        return redirect()->back()->with('success', 'Data pendaftaran berhasil dihapus');
    }

    public function updateStatus($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        
        if ($pendaftaran->status_changed) {
            return redirect()->back()->with('error', 'Status sudah pernah diubah');
        }

        $pendaftaran->update([
            'status' => 'sudah_dilayani',
            'status_changed' => true
        ]);

        return redirect()->back()->with('success', 'Status berhasil diubah');
    }

    public function getChildrenByParent($parentId)
    {
        $children = FamilyChildren::where('parent_id', $parentId)->select('id', 'fullname')->get();
        return response()->json($children);
    }

    public function print(Request $request)
    {
        $query = Pendaftaran::with(['child', 'parent']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pendaftaran', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pendaftaran', '<=', $request->end_date);
        }

        if ($request->filled('sasaran')) {
            $query->where('sasaran', $request->sasaran);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('child', function($childQuery) use ($search) {
                    $childQuery->where('fullname', 'like', "%{$search}%");
                })->orWhereHas('parent', function($parentQuery) use ($search) {
                    $parentQuery->where('mother_fullname', 'like', "%{$search}%");
                });
            });
        }

        $pendaftarans = $query->orderBy('tanggal_pendaftaran', 'des`    c')->get();

        $pdf = Pdf::loadView('dashboard.master-data.pendaftaran.print', compact('pendaftarans'));
        return $pdf->download('laporan-pendaftaran.pdf');
    }
}
