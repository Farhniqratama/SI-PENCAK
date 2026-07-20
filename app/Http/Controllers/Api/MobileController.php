<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Userpt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileController extends Controller
{
    public function ping()
    {
        try {
            DB::connection()->getPdo();

            return response()->json([
                'data' => [
                    'app' => 'SIPENCAK',
                    'database_connected' => true,
                    'database' => DB::connection()->getDatabaseName(),
                    'message' => 'Server dan database SIPENCAK terhubung.',
                    'server_time' => now()->toDateTimeString(),
                ],
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'data' => [
                    'app' => 'SIPENCAK',
                    'database_connected' => false,
                    'database' => null,
                    'message' => 'Server terhubung, tetapi database belum bisa diakses.',
                    'server_time' => now()->toDateTimeString(),
                ],
            ], 500);
        }
    }

    public function stats()
    {
        return response()->json([
            'data' => [
                'mahasiswa' => DB::table('mahasiswas')->count(),
                'perguruan_tinggi' => DB::table('pts')->count(),
                'program_studi' => DB::table('prodis')->count(),
                'pencairan' => DB::table('pencairans')->count(),
            ],
        ]);
    }

    public function searchMahasiswa(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));
        $perPage = max(1, min((int) $request->query('per_page', 10), 50));

        $query = DB::table('mahasiswas')
            ->leftJoin('prodis', 'prodis.id', '=', 'mahasiswas.id_prodi')
            ->leftJoin('pts', 'pts.id', '=', 'mahasiswas.id_pt')
            ->leftJoin('pencairans', 'pencairans.id', '=', 'mahasiswas.id_pencairan')
            ->select([
                'mahasiswas.id',
                'mahasiswas.nim',
                'mahasiswas.nama',
                'mahasiswas.jenjang',
                'mahasiswas.angkatan',
                'mahasiswas.kategori',
                'mahasiswas.pembaruan_status',
                'mahasiswas.status_pengajuan',
                'prodis.nama_prodi',
                'prodis.kode_prodi',
                'pts.perguruan_tinggi',
                'pts.kode_pt',
                'pencairans.id as pencairan_id',
                'pencairans.periode as pencairan_periode',
                'pencairans.semester as pencairan_semester',
                'pencairans.kategori_penerima as pencairan_kategori_penerima',
                'pencairans.jenis_bantuan as pencairan_jenis_bantuan',
                'pencairans.nominal_pencairan as pencairan_nominal',
                'pencairans.jumlah_mahasiswa as pencairan_jumlah_mahasiswa',
                'pencairans.no_sk as pencairan_no_sk',
                'pencairans.tanggal as pencairan_tanggal_surat',
                'pencairans.tanggal_entry as pencairan_tanggal_entry',
                'pencairans.status as pencairan_status',
                'pencairans.alasan_tolak as pencairan_alasan_tolak',
                'pencairans.keterangan as pencairan_keterangan',
            ]);

        if ($keyword !== '') {
            $query->where(function ($query) use ($keyword) {
                $query->where('mahasiswas.nama', 'like', "%{$keyword}%")
                    ->orWhere('mahasiswas.nim', 'like', "%{$keyword}%")
                    ->orWhere('prodis.nama_prodi', 'like', "%{$keyword}%")
                    ->orWhere('pts.perguruan_tinggi', 'like', "%{$keyword}%");
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        $results = $query->orderBy('mahasiswas.nama')->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
            ],
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('username', $credentials['username'])->first();

        if ($user && password_verify($credentials['password'], $user->password)) {
            return response()->json([
                'data' => [
                    'role' => 'operator',
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->nama ?? 'Operator',
                ],
            ]);
        }

        $userpt = Userpt::query()->where('username', $credentials['username'])->first();

        if ($userpt && $userpt->status === 'aktif' && password_verify($credentials['password'], $userpt->password)) {
            return response()->json([
                'data' => [
                    'role' => 'admin',
                    'id' => $userpt->id,
                    'pt_id' => $userpt->id_pt,
                    'username' => $userpt->username,
                    'name' => $userpt->penanggung_jawab ?? $userpt->username,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Username atau password salah.',
        ], 422);
    }

    public function dashboard(Request $request)
    {
        $totalMahasiswa = DB::table('mahasiswas')->count();
        $totalPt = DB::table('pts')->count();
        $totalPencairan = DB::table('pencairans')->count();
        
        $role = $request->header('X-User-Role', 'operator');
        
        return response()->json([
            'data' => [
                'total_mahasiswa' => $totalMahasiswa,
                'total_pt' => $totalPt,
                'total_pencairan' => $totalPencairan,
            ],
            'message' => 'Dashboard data retrieved successfully'
        ]);
    }


    // Mahasiswa CRUD
    public function getMahasiswas(Request $request)
    {
        $query = DB::table('mahasiswas');
        if ($request->has('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('nim', 'like', '%' . $request->q . '%');
        }
        $data = $query->orderBy('id', 'desc')->limit(50)->get();
        return response()->json(['data' => $data]);
    }

    public function storeMahasiswa(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:50',
            'kode_pt' => 'nullable|string|max:50',
            'kode_prodi' => 'nullable|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'kategori' => 'nullable|string|max:100',
        ]);
        
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $id = DB::table('mahasiswas')->insertGetId($validated);
        
        return response()->json([
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data' => DB::table('mahasiswas')->where('id', $id)->first()
        ]);
    }

    public function updateMahasiswa(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:50',
            'kode_pt' => 'nullable|string|max:50',
            'kode_prodi' => 'nullable|string|max:50',
            'jenjang' => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'kategori' => 'nullable|string|max:100',
        ]);
        
        $validated['updated_at'] = now();

        DB::table('mahasiswas')->where('id', $id)->update($validated);
        
        return response()->json([
            'message' => 'Mahasiswa berhasil diperbarui',
            'data' => DB::table('mahasiswas')->where('id', $id)->first()
        ]);
    }

    public function deleteMahasiswa($id)
    {
        DB::table('mahasiswas')->where('id', $id)->delete();
        return response()->json(['message' => 'Mahasiswa berhasil dihapus']);
    }


    // Dynamic Table Viewer for Phase 2 Workaround
    public function getDynamicTable(Request $request, $table)
    {
        $allowed = ['activity_logs', 'audit_logs', 'informasis', 'notifications', 'pencairans', 'periodes', 'prodis', 'pts', 'userpts', 'users'];
        if (!in_array($table, $allowed)) {
            return response()->json(['message' => 'Table not allowed'], 403);
        }
        $data = DB::table($table)->orderBy('id', 'desc')->limit(50)->get();
        return response()->json(['data' => $data]);
    }


    // Dynamic CRUD Additions
    public function getDynamicSchema($table)
    {
        $allowed = ['activity_logs', 'audit_logs', 'informasis', 'notifications', 'pencairans', 'periodes', 'prodis', 'pts', 'userpts', 'users'];
        if (!in_array($table, $allowed)) {
            return response()->json(['message' => 'Table not allowed'], 403);
        }
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing($table);
        // Exclude some internal columns
        $filtered = array_values(array_filter($columns, function($c) {
            return !in_array($c, ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password']);
        }));
        return response()->json(['data' => $filtered]);
    }

    public function storeDynamicTable(Request $request, $table)
    {
        $allowed = ['activity_logs', 'audit_logs', 'informasis', 'notifications', 'pencairans', 'periodes', 'prodis', 'pts', 'userpts', 'users'];
        if (!in_array($table, $allowed)) {
            return response()->json(['message' => 'Table not allowed'], 403);
        }
        $data = $request->except(['id', 'created_at', 'updated_at', 'deleted_at']);
        if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_at')) {
            $data['created_at'] = now();
            $data['updated_at'] = now();
        }
        DB::table($table)->insert($data);
        return response()->json(['message' => 'Data berhasil ditambahkan']);
    }

    public function updateDynamicTable(Request $request, $table, $id)
    {
        $allowed = ['activity_logs', 'audit_logs', 'informasis', 'notifications', 'pencairans', 'periodes', 'prodis', 'pts', 'userpts', 'users'];
        if (!in_array($table, $allowed)) {
            return response()->json(['message' => 'Table not allowed'], 403);
        }
        $data = $request->except(['id', 'created_at', 'updated_at', 'deleted_at']);
        if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'updated_at')) {
            $data['updated_at'] = now();
        }
        DB::table($table)->where('id', $id)->update($data);
        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function deleteDynamicTable($table, $id)
    {
        $allowed = ['activity_logs', 'audit_logs', 'informasis', 'notifications', 'pencairans', 'periodes', 'prodis', 'pts', 'userpts', 'users'];
        if (!in_array($table, $allowed)) {
            return response()->json(['message' => 'Table not allowed'], 403);
        }
        DB::table($table)->where('id', $id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }


    // ---- VERIFIKASI PENCAIRAN (Operator) ----
    public function getVerifikasiPencairan()
    {
        $rows = \Illuminate\Support\Facades\DB::table('pencairans')
            ->join('pts', 'pts.id', '=', 'pencairans.id_pt')
            ->select(
                'pencairans.id',
                'pts.kode_pt',
                'pts.perguruan_tinggi',
                'pencairans.periode',
                'pencairans.tanggal_pengajuan',
                'pencairans.kategori_penerima',
                'pencairans.jenis_bantuan',
                'pencairans.nominal_pencairan',
                'pencairans.jumlah_mahasiswa',
                'pencairans.status',
                'pencairans.alasan_tolak'
            )
            ->orderBy('pencairans.id', 'desc')
            ->get();
        return response()->json(['data' => $rows]);
    }

    public function verifikasiAccept($id)
    {
        \Illuminate\Support\Facades\DB::table('pencairans')->where('id', $id)->update(['status' => 'Selesai']);
        return response()->json(['message' => 'Pencairan disetujui']);
    }

    public function verifikasiReject(\Illuminate\Http\Request $request, $id)
    {
        $alasan = $request->input('alasan', '');
        \Illuminate\Support\Facades\DB::table('pencairans')->where('id', $id)->update([
            'status' => 'Ditolak',
            'alasan_tolak' => $alasan
        ]);
        \Illuminate\Support\Facades\DB::table('mahasiswas')->where('id_pencairan', $id)->update([
            'status_pengajuan' => 'Belum Diajukan',
            'id_pencairan' => null,
        ]);
        return response()->json(['message' => 'Pencairan ditolak']);
    }

    // ---- LAPORAN PENCAIRAN ----
    public function getLaporan()
    {
        $rows = \Illuminate\Support\Facades\DB::table('pencairans')
            ->join('pts', 'pts.id', '=', 'pencairans.id_pt')
            ->select(
                'pencairans.id',
                'pts.kode_pt',
                'pts.perguruan_tinggi',
                'pencairans.periode',
                'pencairans.semester',
                'pencairans.jenis_bantuan',
                'pencairans.jumlah_mahasiswa',
                'pencairans.nominal_pencairan',
                'pencairans.status',
                'pencairans.tanggal_pengajuan',
                'pencairans.tanggal_entry'
            )
            ->orderBy('pencairans.id', 'desc')
            ->get();
        return response()->json(['data' => $rows]);
    }

    // ---- NOTIFIKASI ----
    public function getNotifikasi()
    {
        $rows = \Illuminate\Support\Facades\DB::table('notifications')
            ->select('id', 'title', 'message', 'type', 'link', 'is_read', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        return response()->json(['data' => $rows]);
    }

    // ---- PENGAJUAN PENCAIRAN (Admin) ----
    public function storeAdminPencairan(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'id_pt'             => 'nullable|integer',
            'periode'           => 'required|string|max:20',
            'kategori_penerima' => 'required|string|max:100',
            'no_sk'             => 'nullable|string|max:100',
            'tanggal'           => 'nullable|date',
            'semester'          => 'nullable|string|max:20',
            'jenis_bantuan'     => 'nullable|string|max:100',
            'keterangan'        => 'nullable|string',
            'status'            => 'nullable|string|max:30',
        ]);

        $data['tanggal_entry'] = now()->toDateString();
        $data['status'] = $data['status'] ?? 'Draft';

        $id = \Illuminate\Support\Facades\DB::table('pencairans')->insertGetId($data);
        return response()->json(['message' => 'Pengajuan berhasil disimpan', 'id' => $id], 201);
    }

}
