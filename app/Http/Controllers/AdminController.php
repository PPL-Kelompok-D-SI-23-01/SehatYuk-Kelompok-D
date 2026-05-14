<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// 🔥 IMPORT MODEL
use App\Models\Resep;
use App\Models\User;
use App\Models\Artikel;

class AdminController extends Controller
{
    /**
     * Tampilan Utama Dashboard Admin
     */
    public function index(Request $request)
    {
        // Proteksi Admin
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        try {
            // LANGKAH 1 — TAMBAHKAN VARIABLE TAB
            $tab = $request->tab ?? 'pengguna';
            $search = $request->search;
            $umur = $request->umur;

            // LANGKAH 2 — QUERY USER HANYA SAAT TAB PENGGUNA
            $users = User::with('client');

            if ($tab === 'pengguna') {
                if ($search) {
                    $users->where(function($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                    });
                }

                if ($umur) {
                    $users->whereHas('client', function($c) use ($umur) {
                        $c->where('umur', $umur);
                    });
                }
            }
            // UBAH KE PAGINATE
            $users = $users->latest()->paginate(10, ['*'], 'users_page');

            // LANGKAH 3 — FILTER ARTIKEL
            $artikels = Artikel::query();

            if ($tab === 'artikel') {
                if ($search) {
                    $artikels->where(function($q) use ($search) {
                        $q->where('judul', 'like', "%$search%")
                          ->orWhere('kategori', 'like', "%$search%")
                          ->orWhere('tipe', 'like', "%$search%");
                    });
                }
            }
            // UBAH KE PAGINATE
            $artikels = $artikels->latest()->paginate(10, ['*'], 'artikel_page');

            // LANGKAH 4 — FILTER RESEP
            $reseps = Resep::query();

            if ($tab === 'resep') {
                if ($search) {
                    $reseps->where(function($q) use ($search) {
                        $q->where('nama_makanan', 'like', "%$search%")
                          ->orWhere('kategori', 'like', "%$search%")
                          ->orWhere('gi', 'like', "%$search%");
                    });
                }
            }
            // UBAH KE PAGINATE
            $reseps = $reseps->latest()->paginate(10, ['*'], 'resep_page');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengambil data: ' . $e->getMessage());
        }

        // 🔥 DATA UNTUK TAB LAINNYA & SINKRONISASI BLADE
        $videos = []; 
        $editResep = null;
        $editArtikel = null;
        $editVideo = null;

        return view('admin.index', compact(
            'users',
            'reseps',
            'artikels',
            'videos',
            'editResep',
            'editArtikel',
            'editVideo'
        ));
    }

    /**
     * 🔥 METHOD DETAIL USER (UNTUK AJAX/MODAL)
     */
    public function detailUser($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::with('client')->findOrFail($id);
        return response()->json($user);
    }

    // ==========================================
    // 🔥 KELOLA RESEP (RESEPS)
    // ==========================================

    public function resep()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return redirect()->route('admin', ['tab' => 'resep']);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'nama_makanan' => 'required',
            'kategori' => 'nullable',
            'protein' => 'required|numeric',
            'karbohidrat' => 'required|numeric',
            'lemak' => 'required|numeric',
            'gi' => 'nullable|in:rendah,sedang,tinggi',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable',
            'bahan' => 'nullable',
            'langkah' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'waktu' => 'nullable|integer|min:1',
            'kesulitan' => 'nullable|in:Mudah,Sedang,Sulit',
            'porsi' => 'nullable|integer|min:1',
        ]);

        $data['kalori'] = ($data['karbohidrat'] * 4) + ($data['protein'] * 4) + ($data['lemak'] * 9);

        if (!$request->gi) {
            if ($data['karbohidrat'] < 20) {
                $data['gi'] = 'rendah';
            } elseif ($data['karbohidrat'] <= 40) {
                $data['gi'] = 'sedang';
            } else {
                $data['gi'] = 'tinggi';
            }
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('resep', 'public');
        }

        $data['user_id'] = Auth::id();

        Resep::create($data);

        return redirect()->route('admin', ['tab' => 'resep'])->with('success', 'Resep berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $resep = Resep::findOrFail($id);

        $data = $request->validate([
            'nama_makanan' => 'required',
            'kategori' => 'nullable',
            'protein' => 'required|numeric',
            'karbohidrat' => 'required|numeric',
            'lemak' => 'required|numeric',
            'gi' => 'nullable|in:rendah,sedang,tinggi',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable',
            'bahan' => 'nullable',
            'langkah' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'waktu' => 'nullable|integer|min:1',
            'kesulitan' => 'nullable|in:Mudah,Sedang,Sulit',
            'porsi' => 'nullable|integer|min:1',
        ]);

        $data['kalori'] = ($data['karbohidrat'] * 4) + ($data['protein'] * 4) + ($data['lemak'] * 9);

        if (!$request->gi) {
            if ($data['karbohidrat'] < 20) {
                $data['gi'] = 'rendah';
            } elseif ($data['karbohidrat'] <= 40) {
                $data['gi'] = 'sedang';
            } else {
                $data['gi'] = 'tinggi';
            }
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('resep', 'public');
        }

        $resep->update($data);

        return redirect()->route('admin', ['tab' => 'resep'])->with('success', 'Resep berhasil diupdate');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        Resep::findOrFail($id)->delete();

        return redirect()->route('admin', ['tab' => 'resep'])->with('success', 'Resep berhasil dihapus');
    }

    // ==========================================
    // 🔥 KELOLA USER
    // ==========================================

    public function deleteUser($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        User::findOrFail($id)->delete();

        return redirect()->route('admin', ['tab' => 'pengguna'])->with('success', 'User berhasil dihapus');
    }

    // ==========================================
    // 🔥 KELOLA ARTIKEL & VIDEO
    // ==========================================

    public function storeArtikel(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'judul' => 'required',
            'tipe' => 'required|in:artikel,video',
            'kategori' => 'required',
            'isi' => 'nullable',
            'link' => 'nullable',
            'gambar_edukasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('gambar_edukasi')) {
            $file = $request->file('gambar_edukasi');
            $namaFile = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('gambar_artikel'), $namaFile);
            $data['gambar_edukasi'] = $namaFile;
        }

        Artikel::create($data);

        return redirect()->route('admin', ['tab' => 'artikel'])->with('success', 'Data berhasil ditambahkan');
    }

    public function updateArtikel(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $artikel = Artikel::findOrFail($id);

        $data = $request->validate([
            'judul' => 'required',
            'tipe' => 'required|in:artikel,video',
            'kategori' => 'required',
            'isi' => 'nullable',
            'link' => 'nullable',
            'gambar_edukasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('gambar_edukasi')) {
            if ($artikel->gambar_edukasi && file_exists(public_path('gambar_artikel/'.$artikel->gambar_edukasi))) {
                unlink(public_path('gambar_artikel/'.$artikel->gambar_edukasi));
            }

            $file = $request->file('gambar_edukasi');
            $namaFile = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('gambar_artikel'), $namaFile);

            $data['gambar_edukasi'] = $namaFile;
        }

        $artikel->update($data);

        return redirect()->route('admin', ['tab' => 'artikel'])
            ->with('success', 'Artikel berhasil diupdate');
    }

    public function deleteArtikel($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $artikel = Artikel::findOrFail($id);
        
        if ($artikel->gambar_edukasi && file_exists(public_path('gambar_artikel/'.$artikel->gambar_edukasi))) {
            unlink(public_path('gambar_artikel/'.$artikel->gambar_edukasi));
        }

        $artikel->delete();

        return redirect()->route('admin', ['tab' => 'artikel'])->with('success', 'Artikel berhasil dihapus');
    }
}