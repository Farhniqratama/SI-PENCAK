import 'package:flutter/material.dart';
import 'main.dart';

const kSynoxDarkOp = Color(0xFF2B79B4); // Primary blue
const kSynoxLimeOp = Color(0xFFE4F0F9); // Light blue

class PengajuanPencairanScreen extends StatefulWidget {
  final ApiService api;
  final UserSession session;

  const PengajuanPencairanScreen({
    super.key,
    required this.api,
    required this.session,
  });

  @override
  State<PengajuanPencairanScreen> createState() =>
      _PengajuanPencairanScreenState();
}

class _PengajuanPencairanScreenState
    extends State<PengajuanPencairanScreen> {
  final _formKey = GlobalKey<FormState>();
  final _periodeController = TextEditingController();
  final _kategoriController = TextEditingController();
  final _noSkController = TextEditingController();
  final _tanggalController = TextEditingController();
  final _keteranganController = TextEditingController();

  bool _loading = false;
  String? _selectedSemester;
  String? _selectedJenisBantuan;

  final _semesterItems = ['Ganjil', 'Genap'];
  final _jenisBantuanItems = ['KIP-K', 'Bidikmisi', 'ADik', 'Beasiswa Lainnya'];

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime(2030),
    );
    if (picked != null && mounted) {
      setState(() {
        _tanggalController.text =
            '${picked.year.toString().padLeft(4, '0')}-${picked.month.toString().padLeft(2, '0')}-${picked.day.toString().padLeft(2, '0')}';
      });
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    try {
      await widget.api.storeOperatorPencairan({
        'id_pt': widget.session.ptId,
        'periode': _periodeController.text.trim(),
        'kategori_penerima': _kategoriController.text.trim(),
        'no_sk': _noSkController.text.trim(),
        'tanggal': _tanggalController.text.trim(),
        'semester': _selectedSemester,
        'jenis_bantuan': _selectedJenisBantuan,
        'keterangan': _keteranganController.text.trim(),
        'status': 'Draft',
      });
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pengajuan berhasil disimpan!'),
          backgroundColor: Colors.green,
        ),
      );
      Navigator.pop(context, true);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  InputDecoration _dec(String label, IconData icon) => InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: kSynoxDarkOp),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: kSynoxDarkOp, width: 2),
        ),
      );

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF4F6F9),
      appBar: AppBar(
        title: const Text('Pengajuan Pencairan',
            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: kSynoxDarkOp,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: kSynoxDarkOp,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.description, color: kSynoxLimeOp, size: 32),
                    const SizedBox(height: 8),
                    const Text('Form Pengajuan Pencairan',
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                    Text('PT: ${widget.session.name}',
                        style: const TextStyle(color: Colors.white70)),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              const Text('Informasi Dasar',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              const SizedBox(height: 12),
              TextFormField(
                controller: _periodeController,
                decoration: _dec('Periode (contoh: 2024)', Icons.calendar_today),
                validator: (v) =>
                    v == null || v.isEmpty ? 'Periode wajib diisi' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _kategoriController,
                decoration: _dec('Kategori Penerima', Icons.category),
                validator: (v) =>
                    v == null || v.isEmpty ? 'Kategori wajib diisi' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _noSkController,
                decoration: _dec('Nomor SK', Icons.article),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _tanggalController,
                readOnly: true,
                onTap: _pickDate,
                decoration: _dec('Tanggal SK', Icons.date_range),
              ),
              const SizedBox(height: 20),
              const Text('Detail Pencairan',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: _selectedSemester,
                decoration: _dec('Semester', Icons.school),
                items: _semesterItems
                    .map((s) => DropdownMenuItem(value: s, child: Text(s)))
                    .toList(),
                onChanged: (v) => setState(() => _selectedSemester = v),
                validator: (v) => v == null ? 'Semester wajib dipilih' : null,
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: _selectedJenisBantuan,
                decoration: _dec('Jenis Bantuan', Icons.monetization_on),
                items: _jenisBantuanItems
                    .map((s) => DropdownMenuItem(value: s, child: Text(s)))
                    .toList(),
                onChanged: (v) => setState(() => _selectedJenisBantuan = v),
                validator: (v) => v == null ? 'Jenis bantuan wajib dipilih' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _keteranganController,
                maxLines: 3,
                decoration: _dec('Keterangan (opsional)', Icons.notes),
              ),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 54,
                child: _loading
                    ? const Center(child: CircularProgressIndicator())
                    : ElevatedButton.icon(
                        onPressed: _submit,
                        icon: const Icon(Icons.send_rounded),
                        label: const Text('Simpan sebagai Draft',
                            style: TextStyle(
                                fontSize: 16, fontWeight: FontWeight.bold)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: kSynoxLimeOp,
                          foregroundColor: kSynoxDarkOp,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                      ),
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }
}
