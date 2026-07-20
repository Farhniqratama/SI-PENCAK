    // ---- VERIFIKASI PENCAIRAN (Admin) ----
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

    // ---- PENGAJUAN PENCAIRAN (Operator) ----
    public function storeOperatorPencairan(\Illuminate\Http\Request $request)
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
