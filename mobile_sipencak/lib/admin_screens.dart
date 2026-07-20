import 'package:flutter/material.dart';
import 'main.dart';

const _dark = Color(0xFF2B79B4); // Primary blue
const _lime = Color(0xFFE4F0F9); // Light blue
const _bg = Color(0xFFF4F6F9);

// ─────────────────────────────────────────────
//  VERIFIKASI PENCAIRAN SCREEN (Admin)
// ─────────────────────────────────────────────
class VerifikasiPencairanScreen extends StatefulWidget {
  final ApiService api;
  const VerifikasiPencairanScreen({super.key, required this.api});

  @override
  State<VerifikasiPencairanScreen> createState() => _VerifikasiPencairanScreenState();
}

class _VerifikasiPencairanScreenState extends State<VerifikasiPencairanScreen> {
  List<Map<String, dynamic>> _data = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final rows = await widget.api.getVerifikasiPencairan();
      if (mounted) setState(() { _data = rows; _loading = false; });
    } catch (e) {
      if (mounted) setState(() { _error = e.toString(); _loading = false; });
    }
  }

  Future<void> _accept(int id) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Setujui Pencairan?'),
        content: const Text('Status akan diubah menjadi Selesai.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
            child: const Text('Ya, Setujui', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
    if (ok != true || !mounted) return;
    try {
      await widget.api.verifikasiAccept(id);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('✅ Pencairan disetujui'), backgroundColor: Colors.green));
      _load();
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red));
    }
  }

  Future<void> _reject(int id) async {
    final alasanCtrl = TextEditingController();
    final ok = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Tolak Pencairan'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('Masukkan alasan penolakan:'),
            const SizedBox(height: 12),
            TextField(
              controller: alasanCtrl,
              maxLines: 3,
              decoration: const InputDecoration(
                hintText: 'Alasan...',
                border: OutlineInputBorder(),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Tolak', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
    if (ok != true || !mounted) return;
    try {
      await widget.api.verifikasiReject(id, alasanCtrl.text.trim());
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('❌ Pencairan ditolak'), backgroundColor: Colors.orange));
      _load();
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red));
    }
  }

  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'selesai': return Colors.green;
      case 'diproses': return Colors.blue;
      case 'ditolak': return Colors.red;
      case 'draft': return Colors.grey;
      default: return Colors.orange;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _bg,
      appBar: AppBar(
        title: const Text('Verifikasi Pencairan',
            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: _dark,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.error_outline, color: Colors.red, size: 60),
                    const SizedBox(height: 12),
                    Text('Gagal memuat data', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    Text(_error!, textAlign: TextAlign.center),
                    const SizedBox(height: 16),
                    ElevatedButton.icon(onPressed: _load, icon: const Icon(Icons.refresh), label: const Text('Coba lagi')),
                  ]))
              : _data.isEmpty
                  ? const Center(child: Text('Belum ada data pencairan'))
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.separated(
                        padding: const EdgeInsets.all(16),
                        separatorBuilder: (_, __) => const SizedBox(height: 12),
                        itemCount: _data.length,
                        itemBuilder: (ctx, i) {
                          final item = _data[i];
                          final id = (item['id'] as num).toInt();
                          final status = item['status']?.toString() ?? '-';
                          final isPending = status == 'Diproses';
                          return Card(
                            elevation: 3,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            child: InkWell(
                              borderRadius: BorderRadius.circular(16),
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => DetailPencairanScreen(
                                      item: item,
                                      api: widget.api,
                                    ),
                                  ),
                                );
                              },
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                  // Header
                                  Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                        decoration: BoxDecoration(
                                          color: _dark,
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Text(
                                          item['kode_pt']?.toString() ?? '-',
                                          style: const TextStyle(color: _lime, fontWeight: FontWeight.bold, fontSize: 13),
                                        ),
                                      ),
                                      const SizedBox(width: 10),
                                      Expanded(
                                        child: Text(
                                          item['perguruan_tinggi']?.toString() ?? '-',
                                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: _dark),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 12),
                                  // Info rows
                                  _row(Icons.calendar_month, 'Periode', item['periode']?.toString() ?? '-'),
                                  _row(Icons.send_rounded, 'Tgl Pengajuan', item['tanggal_pengajuan']?.toString() ?? '-'),
                                  _row(Icons.category, 'Kategori', '${item['kategori_penerima'] ?? '-'} / ${item['jenis_bantuan'] ?? '-'}'),
                                  _row(Icons.monetization_on, 'Nominal', 'Rp ${_fmt(item['nominal_pencairan'])}'),
                                  _row(Icons.people, 'Mahasiswa', '${item['jumlah_mahasiswa'] ?? 0} orang'),
                                  const SizedBox(height: 8),
                                  // Status badge
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                                    decoration: BoxDecoration(
                                      color: _statusColor(status).withAlpha(30),
                                      border: Border.all(color: _statusColor(status)),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Text(status,
                                        style: TextStyle(color: _statusColor(status), fontWeight: FontWeight.bold)),
                                  ),
                                  if (isPending) ...[
                                    const SizedBox(height: 12),
                                    Row(
                                      children: [
                                        Expanded(
                                          child: ElevatedButton.icon(
                                            onPressed: () => _accept(id),
                                            icon: const Icon(Icons.check_circle, size: 18),
                                            label: const Text('Setujui'),
                                            style: ElevatedButton.styleFrom(
                                              backgroundColor: Colors.green,
                                              foregroundColor: Colors.white,
                                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                            ),
                                          ),
                                        ),
                                        const SizedBox(width: 8),
                                        Expanded(
                                          child: ElevatedButton.icon(
                                            onPressed: () => _reject(id),
                                            icon: const Icon(Icons.cancel, size: 18),
                                            label: const Text('Tolak'),
                                            style: ElevatedButton.styleFrom(
                                              backgroundColor: Colors.red,
                                              foregroundColor: Colors.white,
                                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ],
                                  if (!isPending && item['alasan_tolak'] != null && (item['alasan_tolak'] as String).isNotEmpty) ...[
                                    const SizedBox(height: 8),
                                    Text('Alasan: ${item['alasan_tolak']}',
                                        style: const TextStyle(color: Colors.red, fontSize: 12)),
                                  ],
                                ],
                              ),
                            ),
                            ),
                          );
                        },
                      ),
                    ),
    );
  }

  Widget _row(IconData icon, String label, String value) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 3),
    child: Row(
      children: [
        Icon(icon, size: 16, color: _dark.withAlpha(150)),
        const SizedBox(width: 6),
        SizedBox(width: 100, child: Text('$label:', style: const TextStyle(fontSize: 12, color: Colors.grey))),
        Expanded(child: Text(value, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500, color: _dark))),
      ],
    ),
  );

  String _fmt(dynamic v) {
    if (v == null || v.toString().isEmpty) return '0';
    try {
      final num val = num.parse(v.toString());
      return val.toStringAsFixed(0).replaceAllMapped(
        RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
        (m) => '${m[1]}.',
      );
    } catch (_) {
      return v.toString();
    }
  }
}

// ─────────────────────────────────────────────
//  LAPORAN PENCAIRAN SCREEN (Admin/Operator)
// ─────────────────────────────────────────────
class LaporanPencairanScreen extends StatefulWidget {
  final ApiService api;
  const LaporanPencairanScreen({super.key, required this.api});

  @override
  State<LaporanPencairanScreen> createState() => _LaporanPencairanScreenState();
}

class _LaporanPencairanScreenState extends State<LaporanPencairanScreen> {
  List<Map<String, dynamic>> _data = [];
  List<Map<String, dynamic>> _filtered = [];
  bool _loading = true;
  String? _error;


  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final rows = await widget.api.getLaporan();
      if (mounted) setState(() {
        _data = rows;
        _filtered = rows;
        _loading = false;
      });
    } catch (e) {
      if (mounted) setState(() { _error = e.toString(); _loading = false; });
    }
  }

  void _applySearch(String q) {
    setState(() {
      _filtered = q.isEmpty
          ? _data
          : _data.where((r) =>
              (r['kode_pt']?.toString() ?? '').toLowerCase().contains(q.toLowerCase()) ||
              (r['perguruan_tinggi']?.toString() ?? '').toLowerCase().contains(q.toLowerCase()) ||
              (r['status']?.toString() ?? '').toLowerCase().contains(q.toLowerCase())).toList();
    });
  }

  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'selesai': return Colors.green;
      case 'diproses': return Colors.blue;
      case 'ditolak': return Colors.red;
      default: return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _bg,
      appBar: AppBar(
        title: const Text('Laporan Pencairan',
            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: _dark,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
        ],
      ),
      body: Column(
        children: [
          // Search bar
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: TextField(
              onChanged: _applySearch,
              decoration: InputDecoration(
                hintText: 'Cari PT atau status...',
                prefixIcon: const Icon(Icons.search),
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              ),
            ),
          ),
          // Summary
          if (!_loading && _error == null)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
              child: Row(
                children: [
                  Text('Total: ${_filtered.length} data',
                      style: const TextStyle(color: Colors.grey, fontSize: 13)),
                ],
              ),
            ),
          // List
          Expanded(
            child: _loading
                ? const Center(child: CircularProgressIndicator())
                : _error != null
                    ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
                        const Icon(Icons.error_outline, color: Colors.red, size: 60),
                        const SizedBox(height: 12),
                        Text(_error!, textAlign: TextAlign.center),
                        const SizedBox(height: 16),
                        ElevatedButton.icon(onPressed: _load, icon: const Icon(Icons.refresh), label: const Text('Coba lagi')),
                      ]))
                    : _filtered.isEmpty
                        ? const Center(child: Text('Tidak ada data laporan'))
                        : RefreshIndicator(
                            onRefresh: _load,
                            child: ListView.separated(
                              padding: const EdgeInsets.fromLTRB(16, 4, 16, 16),
                              separatorBuilder: (_, __) => const SizedBox(height: 10),
                              itemCount: _filtered.length,
                              itemBuilder: (ctx, i) {
                                final item = _filtered[i];
                                final status = item['status']?.toString() ?? '-';
                                return Card(
                                  elevation: 2,
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                                  child: ListTile(
                                    contentPadding: const EdgeInsets.all(14),
                                    leading: CircleAvatar(
                                      backgroundColor: _dark,
                                      radius: 24,
                                      child: Text(
                                        (item['kode_pt']?.toString() ?? '??').substring(0, 3),
                                        style: const TextStyle(color: _lime, fontWeight: FontWeight.bold, fontSize: 11),
                                      ),
                                    ),
                                    title: Text(
                                      item['perguruan_tinggi']?.toString() ?? '-',
                                      style: const TextStyle(fontWeight: FontWeight.bold, color: _dark, fontSize: 14),
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    subtitle: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const SizedBox(height: 4),
                                        Text('Kode: ${item['kode_pt'] ?? '-'}  |  Periode: ${item['periode'] ?? '-'}',
                                            style: const TextStyle(fontSize: 12, color: Colors.grey)),
                                        Text('MHS: ${item['jumlah_mahasiswa'] ?? 0}  |  ${item['jenis_bantuan'] ?? '-'}',
                                            style: const TextStyle(fontSize: 12, color: Colors.grey)),
                                        const SizedBox(height: 6),
                                        Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                                          decoration: BoxDecoration(
                                            color: _statusColor(status).withAlpha(25),
                                            border: Border.all(color: _statusColor(status)),
                                            borderRadius: BorderRadius.circular(12),
                                          ),
                                          child: Text(status,
                                              style: TextStyle(color: _statusColor(status), fontSize: 12, fontWeight: FontWeight.bold)),
                                        ),
                                      ],
                                    ),
                                    trailing: InkWell(
                                      onTap: () => _showDetail(item),
                                      child: Container(
                                        padding: const EdgeInsets.all(8),
                                        decoration: BoxDecoration(color: _dark, borderRadius: BorderRadius.circular(8)),
                                        child: const Icon(Icons.visibility, color: _lime, size: 20),
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
                          ),
          ),
        ],
      ),
    );
  }

  void _showDetail(Map<String, dynamic> item) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey[300], borderRadius: BorderRadius.circular(2))),
            ),
            const SizedBox(height: 16),
            Text(item['perguruan_tinggi']?.toString() ?? '-',
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: _dark)),
            const SizedBox(height: 12),
            _detailRow('Kode PT', item['kode_pt']),
            _detailRow('Periode', item['periode']),
            _detailRow('Semester', item['semester']),
            _detailRow('Jenis Bantuan', item['jenis_bantuan']),
            _detailRow('Jumlah MHS', '${item['jumlah_mahasiswa'] ?? 0} orang'),
            _detailRow('Nominal', 'Rp ${item['nominal_pencairan'] ?? 0}'),
            _detailRow('Status', item['status']),
            _detailRow('Tanggal Pengajuan', item['tanggal_pengajuan']),
            _detailRow('Tanggal Entry', item['tanggal_entry']),
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }

  Widget _detailRow(String label, dynamic value) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 5),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(width: 130, child: Text('$label:', style: const TextStyle(color: Colors.grey, fontSize: 13))),
        Expanded(child: Text(value?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: _dark))),
      ],
    ),
  );
}

// ─────────────────────────────────────────────
//  NOTIFIKASI SCREEN
// ─────────────────────────────────────────────
class NotifikasiScreen extends StatefulWidget {
  final ApiService api;
  const NotifikasiScreen({super.key, required this.api});

  @override
  State<NotifikasiScreen> createState() => _NotifikasiScreenState();
}

class _NotifikasiScreenState extends State<NotifikasiScreen> {
  List<Map<String, dynamic>> _data = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final rows = await widget.api.getNotifikasi();
      if (mounted) setState(() { _data = rows; _loading = false; });
    } catch (e) {
      if (mounted) setState(() { _error = e.toString(); _loading = false; });
    }
  }

  Color _typeColor(String type) {
    switch (type.toLowerCase()) {
      case 'success': return Colors.green;
      case 'danger': return Colors.red;
      case 'warning': return Colors.orange;
      default: return Colors.blue;
    }
  }

  IconData _typeIcon(String type) {
    switch (type.toLowerCase()) {
      case 'success': return Icons.check_circle;
      case 'danger': return Icons.cancel;
      case 'warning': return Icons.warning;
      default: return Icons.notifications;
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Notifikasi',
      subtitle: 'Pemberitahuan dan aktivitas terbaru',
      floatingActionButton: FloatingActionButton(
        onPressed: _load,
        backgroundColor: _dark,
        child: const Icon(Icons.refresh, color: Colors.white),
      ),
      child: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
                  const Icon(Icons.error_outline, color: Colors.red, size: 60),
                  const SizedBox(height: 12),
                  Text(_error!, textAlign: TextAlign.center),
                  const SizedBox(height: 16),
                  ElevatedButton.icon(onPressed: _load, icon: const Icon(Icons.refresh), label: const Text('Coba lagi')),
                ]))
              : _data.isEmpty
                  ? const Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
                      Icon(Icons.notifications_off, size: 60, color: Colors.grey),
                      SizedBox(height: 12),
                      Text('Tidak ada notifikasi', style: TextStyle(color: Colors.grey)),
                    ]))
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.separated(
                        padding: const EdgeInsets.all(16),
                        separatorBuilder: (_, __) => const SizedBox(height: 8),
                        itemCount: _data.length,
                        itemBuilder: (ctx, i) {
                          final item = _data[i];
                          final type = item['type']?.toString() ?? 'primary';
                          final isRead = item['is_read'] == 1 || item['is_read'] == true;
                          return Card(
                            elevation: isRead ? 0 : 3,
                            color: isRead ? Colors.white : Colors.white,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                              side: isRead ? BorderSide.none : BorderSide(color: _typeColor(type).withAlpha(100)),
                            ),
                            child: ListTile(
                              contentPadding: const EdgeInsets.all(12),
                              leading: CircleAvatar(
                                backgroundColor: _typeColor(type).withAlpha(30),
                                child: Icon(_typeIcon(type), color: _typeColor(type)),
                              ),
                              title: Text(
                                item['title']?.toString() ?? 'Notifikasi',
                                style: TextStyle(
                                  fontWeight: isRead ? FontWeight.normal : FontWeight.bold,
                                  color: _dark,
                                ),
                              ),
                              subtitle: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const SizedBox(height: 4),
                                  Text(item['message']?.toString() ?? '',
                                      style: const TextStyle(fontSize: 13, color: Colors.grey)),
                                  const SizedBox(height: 4),
                                  Text(item['created_at']?.toString() ?? '',
                                      style: const TextStyle(fontSize: 11, color: Colors.grey)),
                                ],
                              ),
                              trailing: isRead
                                  ? null
                                  : Container(
                                      width: 10, height: 10,
                                      decoration: BoxDecoration(
                                        color: _typeColor(type),
                                        shape: BoxShape.circle,
                                      ),
                                    ),
                            ),
                          );
                        },
                      ),
                    ),
    );
  }
}

// ─────────────────────────────────────────────
//  DETAIL PENCAIRAN SCREEN (Admin)
// ─────────────────────────────────────────────
class DetailPencairanScreen extends StatelessWidget {
  final Map<String, dynamic> item;
  final ApiService api;
  const DetailPencairanScreen({super.key, required this.item, required this.api});

  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'selesai': return Colors.green;
      case 'diproses': return Colors.blue;
      case 'ditolak': return Colors.red;
      case 'draft': return Colors.grey;
      default: return Colors.orange;
    }
  }

  String _fmt(dynamic v) {
    if (v == null || v.toString().isEmpty) return '0';
    try {
      final num val = num.parse(v.toString());
      return val.toStringAsFixed(0).replaceAllMapped(
        RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
        (m) => '${m[1]}.',
      );
    } catch (_) {
      return v.toString();
    }
  }

  Widget _buildInfoRow(String label, String value, {bool isStatus = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 12)),
          const SizedBox(height: 4),
          if (isStatus)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
              decoration: BoxDecoration(
                color: _statusColor(value).withAlpha(30),
                border: Border.all(color: _statusColor(value)),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(value, style: TextStyle(color: _statusColor(value), fontWeight: FontWeight.bold)),
            )
          else
            Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: Color(0xFF2B79B4))),
          const Divider(),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final status = item['status']?.toString() ?? '-';
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Pencairan', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF2B79B4),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Card(
          elevation: 4,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildInfoRow('Kode PT', item['kode_pt']?.toString() ?? '-'),
                _buildInfoRow('Perguruan Tinggi', item['perguruan_tinggi']?.toString() ?? '-'),
                _buildInfoRow('Periode', item['periode']?.toString() ?? '-'),
                _buildInfoRow('Tanggal Pengajuan', item['tanggal_pengajuan']?.toString() ?? '-'),
                _buildInfoRow('Kategori Penerima', item['kategori_penerima']?.toString() ?? '-'),
                _buildInfoRow('Jenis Bantuan', item['jenis_bantuan']?.toString() ?? '-'),
                _buildInfoRow('Nominal Pencairan', 'Rp ${_fmt(item['nominal_pencairan'])}'),
                _buildInfoRow('Jumlah Mahasiswa', '${item['jumlah_mahasiswa'] ?? 0} orang'),
                _buildInfoRow('Keterangan', item['keterangan']?.toString() ?? '-'),
                _buildInfoRow('Status', status, isStatus: true),
                if (status.toLowerCase() == 'ditolak' && item['alasan_tolak'] != null)
                  _buildInfoRow('Alasan Ditolak', item['alasan_tolak']?.toString() ?? '-'),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
