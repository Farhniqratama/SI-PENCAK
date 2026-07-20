import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'operator_screens.dart';
import 'admin_screens.dart';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  runApp(const SipencakApp());
}

String get _getDefaultApiBaseUrl {
  if (const bool.hasEnvironment('API_BASE_URL')) {
    return const String.fromEnvironment('API_BASE_URL');
  }
  if (kIsWeb) return 'http://127.0.0.1:8000';
  if (defaultTargetPlatform == TargetPlatform.android) {
    return 'http://10.0.2.2:8000';
  }
  return 'http://127.0.0.1:8000';
}

final defaultApiBaseUrl = _getDefaultApiBaseUrl;

const apiBaseUrlPrefsKey = 'api_base_url';

const kSynoxLime = Color(0xFFE4F0F9); // Light tint of primary blue
const kSynoxDark = Color(0xFF2B79B4); // Primary blue (#2B79B4)
const kSynoxSecondary = Color(0xFF1E557E); // Darker shade of primary blue
const kSynoxLight = Color(0xFFE4EEEF);
const kCardBorder = Color(0xFFE4EEEF);
const kTextMuted = Color(0xFF6B6B6B);
const kPageBg = Color(0xFFFFFFFF);

class SipencakApp extends StatelessWidget {
  const SipencakApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'SIPENCAK',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: kSynoxDark,
          primary: kSynoxDark,
          secondary: kSynoxLime,
          surface: Colors.white,
        ),
        scaffoldBackgroundColor: kPageBg,
        fontFamily: 'Roboto',
        cardTheme: const CardThemeData(
          elevation: 0,
          color: Colors.white,
          margin: EdgeInsets.zero,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.all(Radius.circular(14)),
            side: BorderSide(color: kCardBorder),
          ),
        ),
      ),
      home: const SipencakShell(),
    );
  }
}

class ApiService {
  ApiService({http.Client? client, String? baseUrl})
    : baseUrl = _normalizeBaseUrl(baseUrl ?? defaultApiBaseUrl),
      _client = client ?? http.Client();

  final http.Client _client;
  String baseUrl;

  void setBaseUrl(String value) {
    baseUrl = _normalizeBaseUrl(value);
  }

  Uri _uri(String path, [Map<String, String>? query]) {
    final base = Uri.parse(baseUrl);
    return base.replace(
      path: '${base.path.replaceAll(RegExp(r'/$'), '')}$path',
      queryParameters: query,
    );
  }

  Future<ConnectionStatus> ping() async {
    final response = await _client.get(_uri('/api/mobile/ping'));
    _throwIfBad(response);
    return ConnectionStatus.fromJson(jsonDecode(response.body)['data']);
  }

  Future<PublicStats> stats() async {
    final response = await _client.get(_uri('/api/mobile/stats'));
    _throwIfBad(response);
    return PublicStats.fromJson(jsonDecode(response.body)['data']);
  }

  Future<SearchResult> search(String keyword, {int page = 1}) async {
    final response = await _client.get(
      _uri('/api/mobile/mahasiswa/search', {
        'q': keyword,
        'page': '$page',
        'per_page': '10',
      }),
    );
    _throwIfBad(response);
    final body = jsonDecode(response.body);
    return SearchResult.fromJson(body);
  }

  Future<UserSession> login(String username, String password) async {
    final response = await _client.post(
      _uri('/api/mobile/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'username': username, 'password': password}),
    );
    _throwIfBad(response);
    return UserSession.fromJson(jsonDecode(response.body)['data']);
  }

  Future<DashboardStats> getDashboardStats() async {
    final response = await http.get(_uri('/api/mobile/dashboard'));
    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      return DashboardStats.fromJson(json['data']);
    }


    return DashboardStats(totalMahasiswa: 0, totalPt: 0, totalPencairan: 0);
  }


  void _throwIfBad(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) return;
    try {
      final body = jsonDecode(response.body);
      throw ApiException(body['message'] ?? 'Terjadi kesalahan server.');
    } on FormatException {
      throw ApiException('Gagal terhubung ke server SIPENCAK.');
    }
  }


  Future<List<MahasiswaItem>> getMahasiswas([String q = '']) async {
    final response = await _client.get(_uri('/api/mobile/mahasiswa', {'q': q}));
    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      return (json['data'] as List).map((e) => MahasiswaItem.fromJson(e)).toList();
    }
    throw Exception('Gagal memuat data mahasiswa');
  }

  Future<void> createMahasiswa(MahasiswaItem item) async {
    final response = await _client.post(
      _uri('/api/mobile/mahasiswa'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(item.toJson()),
    );
    if (response.statusCode != 200) throw Exception('Gagal menyimpan data');
  }

  Future<void> updateMahasiswa(MahasiswaItem item) async {
    final response = await _client.put(
      _uri('/api/mobile/mahasiswa/${item.id}'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(item.toJson()),
    );
    if (response.statusCode != 200) throw Exception('Gagal memperbarui data');
  }

  Future<void> deleteMahasiswa(int id) async {
    final response = await _client.delete(_uri('/api/mobile/mahasiswa/$id'));
    if (response.statusCode != 200) throw Exception('Gagal menghapus data');
  }

  Future<List<Map<String, dynamic>>> getDynamicTable(String table) async {
    final response = await _client.get(_uri('/api/mobile/dynamic/$table'));
    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      return (json['data'] as List).cast<Map<String, dynamic>>();
    }
    try {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal memuat data tabel $table');
    } catch (_) {
      throw Exception('Gagal memuat data tabel $table (${response.statusCode})');
    }
  }

  Future<List<String>> getDynamicSchema(String table) async {
    final response = await _client.get(_uri('/api/mobile/dynamic/$table/schema'));
    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      return (json['data'] as List).cast<String>();
    }
    throw Exception('Gagal memuat struktur tabel');
  }

  Future<void> storeDynamicTable(String table, Map<String, dynamic> data) async {
    final response = await _client.post(
      _uri('/api/mobile/dynamic/$table'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(data),
    );
    if (response.statusCode != 200) throw Exception('Gagal menyimpan data');
  }

  Future<void> updateDynamicTable(String table, int id, Map<String, dynamic> data) async {
    final response = await _client.put(
      _uri('/api/mobile/dynamic/$table/$id'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(data),
    );
    if (response.statusCode != 200) throw Exception('Gagal memperbarui data');
  }

  Future<void> deleteDynamicTable(String table, int id) async {
    final response = await _client.delete(_uri('/api/mobile/dynamic/$table/$id'));
    if (response.statusCode != 200) throw Exception('Gagal menghapus data');
  }

  Future<void> storeOperatorPencairan(Map<String, dynamic> data) async {
    final response = await _client.post(
      _uri('/api/mobile/admin/pencairan'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(data),
    );
    if (response.statusCode != 200 && response.statusCode != 201) {
      throw Exception('Gagal mengajukan pencairan: ${response.body}');
    }
  }

  Future<List<Map<String, dynamic>>> getVerifikasiPencairan() async {
    final response = await _client.get(_uri('/api/mobile/verifikasi-pencairan'));
    _throwIfBad(response);
    final body = jsonDecode(response.body);
    return List<Map<String, dynamic>>.from(body['data'] ?? []);
  }

  Future<void> verifikasiAccept(int id) async {
    final response = await _client.post(_uri('/api/mobile/verifikasi-pencairan/$id/accept'));
    _throwIfBad(response);
  }

  Future<void> verifikasiReject(int id, String alasan) async {
    final response = await _client.post(
      _uri('/api/mobile/verifikasi-pencairan/$id/reject'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'alasan': alasan}),
    );
    _throwIfBad(response);
  }

  Future<List<Map<String, dynamic>>> getLaporan() async {
    final response = await _client.get(_uri('/api/mobile/laporan'));
    _throwIfBad(response);
    final body = jsonDecode(response.body);
    return List<Map<String, dynamic>>.from(body['data'] ?? []);
  }

  Future<List<Map<String, dynamic>>> getNotifikasi() async {
    final response = await _client.get(_uri('/api/mobile/notifikasi'));
    _throwIfBad(response);
    final body = jsonDecode(response.body);
    return List<Map<String, dynamic>>.from(body['data'] ?? []);
  }

}

String _normalizeBaseUrl(String value) {
  final cleaned = value.trim().replaceAll(RegExp(r'/+$'), '');
  return cleaned.isEmpty ? defaultApiBaseUrl : cleaned;


}

class ApiException implements Exception {
  ApiException(this.message);
  final String message;

  @override
  String toString() => message;
}

class ConnectionStatus {
  ConnectionStatus({
    required this.databaseConnected,
    required this.database,
    required this.message,
    required this.serverTime,
  });

  final bool databaseConnected;
  final String database;
  final String message;
  final String serverTime;

  factory ConnectionStatus.fromJson(Map<String, dynamic> json) {
    return ConnectionStatus(
      databaseConnected: json['database_connected'] == true,
      database: _text(json['database']),
      message: _text(json['message']),
      serverTime: _text(json['server_time']),
    );
  }
}

class PublicStats {
  PublicStats({
    required this.mahasiswa,
    required this.perguruanTinggi,
    required this.programStudi,
    required this.pencairan,
  });

  final int mahasiswa;
  final int perguruanTinggi;
  final int programStudi;
  final int pencairan;

  factory PublicStats.fromJson(Map<String, dynamic> json) {
    return PublicStats(
      mahasiswa: _int(json['mahasiswa']),
      perguruanTinggi: _int(json['perguruan_tinggi']),
      programStudi: _int(json['program_studi']),
      pencairan: _int(json['pencairan']),
    );
  }
}

class SearchResult {
  SearchResult({required this.items, required this.meta});

  final List<StudentRecord> items;
  final SearchMeta meta;

  factory SearchResult.fromJson(Map<String, dynamic> json) {
    return SearchResult(
      items: (json['data'] as List<dynamic>? ?? [])
          .map((item) => StudentRecord.fromJson(item))
          .toList(),
      meta: SearchMeta.fromJson(json['meta'] ?? {}),
    );
  }
}

class SearchMeta {
  SearchMeta({
    required this.currentPage,
    required this.lastPage,
    required this.total,
    required this.from,
    required this.to,
  });

  final int currentPage;
  final int lastPage;
  final int total;
  final int from;
  final int to;

  factory SearchMeta.fromJson(Map<String, dynamic> json) {
    return SearchMeta(
      currentPage: _int(json['current_page']),
      lastPage: _int(json['last_page']),
      total: _int(json['total']),
      from: _int(json['from']),
      to: _int(json['to']),
    );
  }
}

class StudentRecord {
  StudentRecord({
    required this.id,
    required this.nama,
    required this.nim,
    required this.jenjang,
    required this.angkatan,
    required this.kategori,
    required this.pembaruanStatus,
    required this.statusPengajuan,
    required this.namaProdi,
    required this.kodeProdi,
    required this.perguruanTinggi,
    required this.kodePt,
    required this.pencairan,
  });

  final int id;
  final String nama;
  final String nim;
  final String jenjang;
  final String angkatan;
  final String kategori;
  final String pembaruanStatus;
  final String statusPengajuan;
  final String namaProdi;
  final String kodeProdi;
  final String perguruanTinggi;
  final String kodePt;
  final Disbursement? pencairan;

  factory StudentRecord.fromJson(Map<String, dynamic> json) {
    final hasPencairan = json['pencairan_id'] != null;
    return StudentRecord(
      id: _int(json['id']),
      nama: _text(json['nama']),
      nim: _text(json['nim']),
      jenjang: _text(json['jenjang']),
      angkatan: _text(json['angkatan']),
      kategori: _text(json['kategori']),
      pembaruanStatus: _text(
        json['pembaruan_status'],
        fallback: 'Belum diperbarui',
      ),
      statusPengajuan: _text(json['status_pengajuan'], fallback: 'Terdaftar'),
      namaProdi: _text(json['nama_prodi']),
      kodeProdi: _text(json['kode_prodi']),
      perguruanTinggi: _text(json['perguruan_tinggi']),
      kodePt: _text(json['kode_pt']),
      pencairan: hasPencairan ? Disbursement.fromJson(json) : null,
    );
  }
}

class Disbursement {
  Disbursement({
    required this.status,
    required this.periode,
    required this.semester,
    required this.kategori,
    required this.jenis,
    required this.nominal,
    required this.jumlahMahasiswa,
    required this.noSk,
    required this.tanggalSurat,
    required this.tanggalEntry,
    required this.alasanTolak,
    required this.keterangan,
  });

  final String status;
  final String periode;
  final String semester;
  final String kategori;
  final String jenis;
  final double nominal;
  final int jumlahMahasiswa;
  final String noSk;
  final String tanggalSurat;
  final String tanggalEntry;
  final String alasanTolak;
  final String keterangan;

  factory Disbursement.fromJson(Map<String, dynamic> json) {
    return Disbursement(
      status: _text(json['pencairan_status'], fallback: 'Belum diproses'),
      periode: _text(json['pencairan_periode']),
      semester: _text(json['pencairan_semester']),
      kategori: _text(json['pencairan_kategori_penerima']),
      jenis: _text(json['pencairan_jenis_bantuan']),
      nominal: double.tryParse('${json['pencairan_nominal'] ?? 0}') ?? 0,
      jumlahMahasiswa: _int(json['pencairan_jumlah_mahasiswa']),
      noSk: _text(json['pencairan_no_sk']),
      tanggalSurat: _text(json['pencairan_tanggal_surat']),
      tanggalEntry: _text(json['pencairan_tanggal_entry']),
      alasanTolak: _text(json['pencairan_alasan_tolak']),
      keterangan: _text(json['pencairan_keterangan']),
    );
  }
}


class DashboardStats {
  final int totalMahasiswa;
  final int totalPt;
  final int totalPencairan;
  
  DashboardStats({
    required this.totalMahasiswa,
    required this.totalPt,
    required this.totalPencairan,
  });

  factory DashboardStats.fromJson(Map<String, dynamic> json) {
    return DashboardStats(
      totalMahasiswa: json['total_mahasiswa'] ?? 0,
      totalPt: json['total_pt'] ?? 0,
      totalPencairan: json['total_pencairan'] ?? 0,
    );
  }
}

class DashboardFeature {
  final String title;
  final IconData icon;
  
  DashboardFeature(this.title, this.icon);
}

class UserSession {
  UserSession({
    required this.role,
    required this.id,
    required this.username,
    required this.name,
    this.ptId,
  });

  final String role;
  final int id;
  final int? ptId;
  final String username;
  final String name;

  static const prefsKeys = ['role', 'id', 'pt_id', 'username', 'name'];

  factory UserSession.fromJson(Map<String, dynamic> json) {
    return UserSession(
      role: _text(json['role']),
      id: _int(json['id']),
      ptId: json['pt_id'] == null ? null : _int(json['pt_id']),
      username: _text(json['username']),
      name: _text(json['name'], fallback: 'User'),
    );
  }

  Map<String, String> toPrefs() => {
    'role': role,
    'id': '$id',
    'pt_id': '${ptId ?? ''}',
    'username': username,
    'name': name,
  };

  factory UserSession.fromPrefs(SharedPreferences prefs) {
    return UserSession(
      role: prefs.getString('role') ?? '',
      id: int.tryParse(prefs.getString('id') ?? '') ?? 0,
      ptId: int.tryParse(prefs.getString('pt_id') ?? ''),
      username: prefs.getString('username') ?? '',
      name: prefs.getString('name') ?? 'User',
    );
  }
}

class SipencakShell extends StatefulWidget {
  const SipencakShell({super.key});

  @override
  State<SipencakShell> createState() => _SipencakShellState();
}

class _SipencakShellState extends State<SipencakShell> {
  final api = ApiService();
  int index = 0;
  UserSession? session;

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    final prefs = await SharedPreferences.getInstance();
    final savedBaseUrl = prefs.getString(apiBaseUrlPrefsKey);
    if (savedBaseUrl != null && savedBaseUrl.trim().isNotEmpty) {
      api.setBaseUrl(savedBaseUrl);
    }

    if ((prefs.getString('username') ?? '').isNotEmpty) {
      setState(() => session = UserSession.fromPrefs(prefs));
    } else {
      setState(() {});
    }
  }

  Future<void> _saveApiBaseUrl(String value) async {
    final prefs = await SharedPreferences.getInstance();
    api.setBaseUrl(value);
    await prefs.setString(apiBaseUrlPrefsKey, api.baseUrl);
    setState(() {});
  }

  Future<void> _saveSession(UserSession value) async {
    final prefs = await SharedPreferences.getInstance();
    for (final entry in value.toPrefs().entries) {
      await prefs.setString(entry.key, entry.value);
    }
    setState(() {
      session = value;
      index = 3;
    });
  }

  Future<void> _logout() async {
    final prefs = await SharedPreferences.getInstance();
    for (final key in UserSession.prefsKeys) {
      await prefs.remove(key);
    }
    setState(() => session = null);
  }

  @override
  Widget build(BuildContext context) {
    final pages = [
      DashboardScreen(
        api: api,
        onSearchTap: () => setState(() => index = 1),
        onInfoTap: () => setState(() => index = 2),
        onAccountTap: () => setState(() => index = 3),
      ),
      SearchScreen(api: api),
      NotifikasiScreen(api: api),
      AccountScreen(
        api: api,
        session: session,
        onLoggedIn: _saveSession,
        onLogout: _logout,
        onBaseUrlSaved: _saveApiBaseUrl,
      ),
    ];

    return Scaffold(
      body: pages[index],
      bottomNavigationBar: NavigationBar(
        selectedIndex: index,
        onDestinationSelected: (value) => setState(() => index = value),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.dashboard_outlined),
            selectedIcon: Icon(Icons.dashboard),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(Icons.search),
            selectedIcon: Icon(Icons.manage_search),
            label: 'Cari',
          ),
          NavigationDestination(
            icon: Icon(Icons.notifications_none),
            selectedIcon: Icon(Icons.notifications),
            label: 'Notifikasi',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person),
            label: 'Akun',
          ),
        ],
      ),
    );
  }
}

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({
    super.key,
    required this.api,
    required this.onSearchTap,
    required this.onInfoTap,
    required this.onAccountTap,
  });

  final ApiService api;
  final VoidCallback onSearchTap;
  final VoidCallback onInfoTap;
  final VoidCallback onAccountTap;

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'SIPENCAK',
      subtitle: 'Sistem Pengelolaan Pencairan KIP Kuliah',
      child: FutureBuilder<PublicStats>(
        future: api.stats(),
        builder: (context, snapshot) {
          final stats = snapshot.data;
          return ListView(
            padding: const EdgeInsets.fromLTRB(18, 18, 18, 28),
            children: [
              HeroPanel(onSearchTap: onSearchTap),
              const SizedBox(height: 18),
              const SectionTitle('Layanan Mobile'),
              GridView.count(
                crossAxisCount: 2,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                mainAxisSpacing: 12,
                crossAxisSpacing: 12,
                childAspectRatio: 1.2,
                children: [
                  FeatureTile(
                    icon: Icons.manage_search,
                    title: 'Cari Mahasiswa',
                    subtitle: 'Nama, NIM, prodi, dan PT',
                    onTap: onSearchTap,
                  ),
                  FeatureTile(
                    icon: Icons.notifications_active_outlined,
                    title: 'Informasi',
                    subtitle: 'Notifikasi dan panduan',
                    onTap: onInfoTap,
                  ),
                  FeatureTile(
                    icon: Icons.account_circle_outlined,
                    title: 'Akun',
                    subtitle: 'Login pengelola',
                    onTap: onAccountTap,
                  ),
                  FeatureTile(
                    icon: Icons.verified_outlined,
                    title: 'Status',
                    subtitle: 'Riwayat pencairan publik',
                    onTap: onSearchTap,
                  ),
                ],
              ),
              const SizedBox(height: 18),
              if (snapshot.connectionState == ConnectionState.waiting)
                const LinearProgressIndicator(minHeight: 3)
              else if (snapshot.hasError)
                ErrorBox(message: '${snapshot.error}')
              else
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  mainAxisSpacing: 12,
                  crossAxisSpacing: 12,
                  childAspectRatio: 1.15,
                  children: [
                    StatTile(
                      label: 'Mahasiswa',
                      value: stats!.mahasiswa,
                      icon: Icons.groups_2_outlined,
                    ),
                    StatTile(
                      label: 'Perguruan Tinggi',
                      value: stats.perguruanTinggi,
                      icon: Icons.account_balance_outlined,
                    ),
                    StatTile(
                      label: 'Prodi',
                      value: stats.programStudi,
                      icon: Icons.school_outlined,
                    ),
                    StatTile(
                      label: 'Pengajuan',
                      value: stats.pencairan,
                      icon: Icons.payments_outlined,
                    ),
                  ],
                ),
              const SizedBox(height: 18),
              const SectionTitle('Alur Pencairan'),
              const FlowStep(
                icon: Icons.edit_document,
                title: 'Pengajuan',
                subtitle: 'Admin PT melengkapi data dan dokumen pencairan.',
              ),
              const FlowStep(
                icon: Icons.fact_check_outlined,
                title: 'Verifikasi',
                subtitle: 'LLDIKTI memeriksa dokumen dan daftar mahasiswa.',
              ),
              const FlowStep(
                icon: Icons.verified_outlined,
                title: 'Selesai',
                subtitle:
                    'Status pencairan dapat dipantau oleh publik dan pengelola.',
              ),
            ],
          );
        },
      ),
    );
  }
}

class HeroPanel extends StatelessWidget {
  const HeroPanel({super.key, required this.onSearchTap});

  final VoidCallback onSearchTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: kSynoxDark,
        borderRadius: BorderRadius.circular(24),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: kSynoxLime.withOpacity(0.15),
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Text(
              'SIPENCAK LLDIKTI III',
              style: TextStyle(
                color: kSynoxLime,
                fontWeight: FontWeight.bold,
                fontSize: 12,
              ),
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'Lacak Status Data Mahasiswa KIP Kuliah',
            style: TextStyle(
              color: Colors.white,
              fontSize: 28,
              height: 1.15,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 12),
          const Text(
            'Pantau data terdaftar, status pengajuan, dan detail pencairan secara transparan dan terpadu.',
            style: TextStyle(
              color: kSynoxLight,
              height: 1.5,
              fontSize: 15,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            child: FilledButton.icon(
              style: FilledButton.styleFrom(
                backgroundColor: kSynoxLime,
                foregroundColor: kSynoxDark,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
              ),
              onPressed: onSearchTap,
              icon: const Icon(Icons.search, size: 20),
              label: const Text(
                'Cari Data Mahasiswa',
                style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
              ),
            ),
          ),
        ],
      ),
    );
  }
}


class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key, required this.api});

  final ApiService api;

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final controller = TextEditingController();
  SearchResult? result;
  bool loading = false;
  String? error;
  int page = 1;

  Future<void> search({int nextPage = 1}) async {
    setState(() {
      loading = true;
      error = null;
      page = nextPage;
    });

    try {
      final data = await widget.api.search(
        controller.text.trim(),
        page: nextPage,
      );
      setState(() => result = data);
    } catch (e) {
      if (mounted) setState(() => error = '$e');
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  @override
  void initState() {
    super.initState();
    search();
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Pencarian Mahasiswa',
      subtitle: 'Data terdaftar dan status pencairan',
      child: RefreshIndicator(
        onRefresh: () => search(nextPage: page),
        child: ListView(
          padding: const EdgeInsets.fromLTRB(18, 18, 18, 28),
          children: [
            SearchBox(
              controller: controller,
              loading: loading,
              onSubmit: () => search(),
            ),
            const SizedBox(height: 16),
            if (loading) const LinearProgressIndicator(minHeight: 3),
            if (error != null) ErrorBox(message: error!),
            if (result != null) ...[
              ResultSummary(
                meta: result!.meta,
                keyword: controller.text.trim(),
              ),
              const SizedBox(height: 12),
              for (final student in result!.items)
                StudentResultTile(student: student),
              if (result!.items.isEmpty)
                const EmptyBox(
                  title: 'Data belum ditemukan',
                  subtitle:
                      'Coba gunakan nama, NIM, prodi, atau perguruan tinggi lain.',
                ),
              if (result!.meta.lastPage > 1)
                Pager(
                  meta: result!.meta,
                  onPrev: page > 1 ? () => search(nextPage: page - 1) : null,
                  onNext: page < result!.meta.lastPage
                      ? () => search(nextPage: page + 1)
                      : null,
                ),
            ],
          ],
        ),
      ),
    );
  }
}

class StudentResultTile extends StatelessWidget {
  const StudentResultTile({super.key, required this.student});

  final StudentRecord student;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Material(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        child: InkWell(
          borderRadius: BorderRadius.circular(20),
          onTap: () => Navigator.of(context).push(
            MaterialPageRoute<void>(
              builder: (_) => StudentDetailScreen(student: student),
            ),
          ),
          child: Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              border: Border.all(color: kCardBorder),
              borderRadius: BorderRadius.circular(20),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.02),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: kSynoxLight,
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(Icons.person, color: kSynoxDark, size: 28),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            student.nama,
                            style: const TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w900,
                              color: kSynoxDark,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'NIM: ${student.nim}',
                            style: const TextStyle(
                              color: kTextMuted,
                              fontWeight: FontWeight.w700,
                              fontSize: 14,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                const Divider(height: 1, color: kCardBorder),
                const SizedBox(height: 16),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    StatusChip(student.statusPengajuan),
                    MiniChip(Icons.account_balance, student.perguruanTinggi),
                    MiniChip(Icons.school, '${student.namaProdi} - ${student.kodeProdi}'),
                    if (student.pencairan != null)
                      MiniChip(Icons.payments, student.pencairan!.status),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}


class StudentDetailScreen extends StatelessWidget {
  const StudentDetailScreen({super.key, required this.student});

  final StudentRecord student;

  Widget _buildSection(String title, List<Widget> children) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Text(
            title,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w900,
              color: kSynoxDark,
            ),
          ),
        ),
        Container(
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: kCardBorder),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Column(
            children: children,
          ),
        ),
      ],
    );
  }

  Widget _buildItem(String label, String value, {bool isLast = false, Widget? customValue}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        border: isLast ? null : const Border(bottom: BorderSide(color: kCardBorder)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 2,
            child: Text(
              label,
              style: const TextStyle(
                color: kTextMuted,
                fontWeight: FontWeight.w600,
                fontSize: 14,
              ),
            ),
          ),
          Expanded(
            flex: 3,
            child: customValue ?? Text(
              value,
              textAlign: TextAlign.right,
              style: const TextStyle(
                color: kSynoxDark,
                fontWeight: FontWeight.w800,
                fontSize: 14,
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final pencairan = student.pencairan;

    return Scaffold(
      backgroundColor: kPageBg,
      appBar: AppBar(
        backgroundColor: kSynoxDark,
        foregroundColor: kSynoxLime,
        title: const Text('Detail Mahasiswa', style: TextStyle(fontWeight: FontWeight.w800)),
        centerTitle: true,
        elevation: 0,
      ),
      body: ListView(
        padding: const EdgeInsets.only(top: 24, bottom: 40),
        children: [
          Center(
            child: Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: kSynoxLime,
                shape: BoxShape.circle,
              ),
              child: const CircleAvatar(
                radius: 40,
                backgroundColor: kSynoxDark,
                child: Icon(Icons.person, size: 40, color: kSynoxLime),
              ),
            ),
          ),
          const SizedBox(height: 16),
          Text(
            student.nama,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.w900,
              color: kSynoxDark,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            student.nim,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: kTextMuted,
            ),
          ),
          const SizedBox(height: 16),
          Center(child: StatusChip(student.statusPengajuan)),
          const SizedBox(height: 32),

          _buildSection('Informasi Akademik', [
            _buildItem('Perguruan Tinggi', student.perguruanTinggi),
            _buildItem('Kode PT', student.kodePt),
            _buildItem('Program Studi', student.namaProdi),
            _buildItem('Kode Prodi', student.kodeProdi),
            _buildItem('Jenjang', student.jenjang),
            _buildItem('Angkatan', student.angkatan),
            _buildItem('Kategori', student.kategori, isLast: true),
          ]),

          const SizedBox(height: 16),
          
          if (pencairan != null)
            _buildSection('Detail Pencairan', [
              _buildItem('Status Pencairan', '', customValue: Align(alignment: Alignment.centerRight, child: StatusChip(pencairan.status))),
              _buildItem('Periode / Smt', '${pencairan.periode} - ${pencairan.semester}'),
              _buildItem('Jenis Bantuan', pencairan.jenis),
              _buildItem('Kategori Penerima', pencairan.kategori),
              _buildItem('Nominal', rupiah(pencairan.nominal)),
              _buildItem('No SK', pencairan.noSk),
              _buildItem('Tanggal Surat', pencairan.tanggalSurat),
              _buildItem('Tanggal Entry', pencairan.tanggalEntry),
              _buildItem('Keterangan', pencairan.keterangan, isLast: true),
            ])
          else
            Container(
              margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: kSynoxLight,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: kCardBorder),
              ),
              child: const Column(
                children: [
                  Icon(Icons.info_outline, color: kSynoxDark, size: 32),
                  SizedBox(height: 12),
                  Text(
                    'Belum ada data pencairan untuk mahasiswa ini.',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: kSynoxDark, fontWeight: FontWeight.w600),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}


class DetailHero extends StatelessWidget {
  const DetailHero({super.key, required this.student});

  final StudentRecord student;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        gradient: const LinearGradient(
          colors: [kSynoxDark, kSynoxDark],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: .16),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.white24),
                ),
                child: const Icon(Icons.person, color: Colors.white),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      student.nama,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 22,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      'NIM ${student.nim}',
                      style: const TextStyle(
                        color: Colors.white70,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              LightChip(
                Icons.account_balance_outlined,
                student.perguruanTinggi,
              ),
              LightChip(
                Icons.school_outlined,
                '${student.namaProdi} - ${student.kodeProdi}',
              ),
              LightChip(Icons.verified_user_outlined, student.statusPengajuan),
            ],
          ),
        ],
      ),
    );
  }
}

class QuickMetric extends StatelessWidget {
  const QuickMetric({
    super.key,
    required this.label,
    required this.value,
    required this.icon,
  });

  final String label;
  final String value;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: kSynoxDark),
            const SizedBox(height: 10),
            Text(
              label.toUpperCase(),
              style: const TextStyle(
                color: kTextMuted,
                fontSize: 11,
                fontWeight: FontWeight.w900,
                letterSpacing: .7,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              value,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
            ),
          ],
        ),
      ),
    );
  }
}

class TrackingTimeline extends StatelessWidget {
  const TrackingTimeline({super.key, required this.status});

  final String status;

  int get _activeIndex {
    final text = status.toLowerCase();
    if (text.contains('selesai') || text.contains('diterima')) return 3;
    if (text.contains('final') || text.contains('verifikasi')) return 2;
    if (text.contains('aju') || text.contains('proses')) return 1;
    return 0;
  }

  @override
  Widget build(BuildContext context) {
    const steps = [
      ('Draft', Icons.edit_document),
      ('Diajukan', Icons.send_outlined),
      ('Verifikasi', Icons.fact_check_outlined),
      ('Selesai', Icons.verified_outlined),
    ];
    final active = _activeIndex;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Tracking Pencairan',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900),
            ),
            const SizedBox(height: 14),
            for (var i = 0; i < steps.length; i++) ...[
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 34,
                    height: 34,
                    decoration: BoxDecoration(
                      color: i <= active ? kSynoxDark : kSynoxLight,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(
                      steps[i].$2,
                      size: 18,
                      color: i <= active ? Colors.white : kSynoxDark,
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(top: 6),
                      child: Text(
                        steps[i].$1,
                        style: TextStyle(
                          color: i <= active
                              ? const Color(0xFF102033)
                              : kTextMuted,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              if (i < steps.length - 1)
                Container(
                  margin: const EdgeInsets.only(left: 16, top: 4, bottom: 4),
                  width: 2,
                  height: 18,
                  color: i < active ? kSynoxDark : kCardBorder,
                ),
            ],
          ],
        ),
      ),
    );
  }
}

class LightChip extends StatelessWidget {
  const LightChip(this.icon, this.text, {super.key});

  final IconData icon;
  final String text;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: .14),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: Colors.white24),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 15, color: Colors.white),
          const SizedBox(width: 6),
          Flexible(
            child: Text(
              text,
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w800,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class StudentDetailSheet extends StatelessWidget {
  const StudentDetailSheet({super.key, required this.student});

  final StudentRecord student;

  @override
  Widget build(BuildContext context) {
    final pencairan = student.pencairan;

    return DraggableScrollableSheet(
      expand: false,
      initialChildSize: 0.88,
      minChildSize: 0.55,
      maxChildSize: 0.96,
      builder: (context, scrollController) {
        return ListView(
          controller: scrollController,
          padding: const EdgeInsets.fromLTRB(20, 0, 20, 28),
          children: [
            Text(
              student.nama,
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900),
            ),
            const SizedBox(height: 8),
            Text(
              'NIM ${student.nim}',
              style: const TextStyle(
                color: kTextMuted,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 18),
            DetailSection(
              title: 'Data Akademik',
              icon: Icons.school_outlined,
              fields: [
                DetailField('Perguruan Tinggi', student.perguruanTinggi),
                DetailField('Kode PT', student.kodePt),
                DetailField('Program Studi', student.namaProdi),
                DetailField('Kode Prodi', student.kodeProdi),
                DetailField(
                  'Jenjang / Angkatan',
                  '${student.jenjang} / ${student.angkatan}',
                ),
                DetailField('Kategori', student.kategori),
                DetailField('Pembaruan Status', student.pembaruanStatus),
                DetailField('Status Pengajuan', student.statusPengajuan),
              ],
            ),
            const SizedBox(height: 16),
            if (pencairan == null)
              const EmptyBox(
                title: 'Belum masuk data pencairan',
                subtitle:
                    'Mahasiswa ini belum terhubung dengan permohonan pencairan aktif.',
              )
            else
              DetailSection(
                title: 'Detail Pencairan',
                icon: Icons.money,
                fields: [
                  DetailField('Status Pencairan', pencairan.status),
                  DetailField('Periode', pencairan.periode),
                  DetailField('Semester', pencairan.semester),
                  DetailField('Kategori Bantuan', pencairan.kategori),
                  DetailField('Jenis Bantuan', pencairan.jenis),
                  DetailField('Nominal', rupiah(pencairan.nominal)),
                  DetailField(
                    'Jumlah Mahasiswa',
                    '${pencairan.jumlahMahasiswa} Mhs',
                  ),
                  DetailField('No. SK / Surat', pencairan.noSk),
                  DetailField('Tanggal Entry', pencairan.tanggalEntry),
                  DetailField('Tanggal Surat', pencairan.tanggalSurat),
                  DetailField('Keterangan', pencairan.keterangan),
                  if (pencairan.alasanTolak.isNotEmpty)
                    DetailField('Alasan Ditolak', pencairan.alasanTolak),
                ],
              ),
          ],
        );
      },
    );
  }
}

class NotificationsScreen extends StatelessWidget {
  const NotificationsScreen({super.key, required this.session});

  final UserSession? session;

  @override
  Widget build(BuildContext context) {
    final items = [
      (
        'Status pencairan',
        'Pantau perubahan status pengajuan dari menu pencarian publik.',
        Icons.payments_outlined,
      ),
      (
        'Dokumen dan verifikasi',
        'Login pengelola diperlukan untuk mengakses aksi admin/operator.',
        Icons.verified_user_outlined,
      ),
      (
        'Akun aktif',
        session == null
            ? 'Masuk untuk melihat identitas pengelola.'
            : 'Anda masuk sebagai ${session!.name}.',
        Icons.person_outline,
      ),
    ];

    return AppScaffold(
      title: 'Notifikasi',
      subtitle: 'Ringkasan informasi mobile',
      child: ListView.separated(
        padding: const EdgeInsets.all(18),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(height: 12),
        itemBuilder: (context, i) {
          final item = items[i];
          return InfoCard(icon: item.$3, title: item.$1, subtitle: item.$2);
        },
      ),
    );
  }
}

class AccountScreen extends StatefulWidget {
  const AccountScreen({
    super.key,
    required this.api,
    required this.session,
    required this.onLoggedIn,
    required this.onLogout,
    required this.onBaseUrlSaved,
  });

  final ApiService api;
  final UserSession? session;
  final ValueChanged<UserSession> onLoggedIn;
  final VoidCallback onLogout;
  final Future<void> Function(String) onBaseUrlSaved;

  @override
  State<AccountScreen> createState() => _AccountScreenState();
}

class _AccountScreenState extends State<AccountScreen> {
  final username = TextEditingController();
  final password = TextEditingController();
  final serverUrl = TextEditingController();
  bool loading = false;
  bool testingConnection = false;
  String? error;
  String? connectionMessage;
  bool? databaseConnected;

  @override
  void initState() {
    super.initState();
    serverUrl.text = widget.api.baseUrl;
  }

  Future<void> saveServerUrl() async {
    await widget.onBaseUrlSaved(serverUrl.text);
    if (!mounted) return;
    setState(() {
      serverUrl.text = widget.api.baseUrl;
      connectionMessage = 'URL server disimpan: ${widget.api.baseUrl}';
      databaseConnected = null;
      error = null;
    });
  }

  Future<void> testConnection() async {
    setState(() {
      testingConnection = true;
      error = null;
      connectionMessage = null;
      databaseConnected = null;
    });

    try {
      await widget.onBaseUrlSaved(serverUrl.text);
      final status = await widget.api.ping();
      setState(() {
        serverUrl.text = widget.api.baseUrl;
        databaseConnected = status.databaseConnected;
        connectionMessage =
            '${status.message} Database: ${status.database}. Waktu server: ${status.serverTime}.';
      });
    } catch (e) {
      if (mounted) setState(() {
        databaseConnected = false;
        connectionMessage =
            'Koneksi gagal. Pastikan Laravel sedang berjalan dan URL server benar. Detail: $e';
      });
    } finally {
      if (mounted) setState(() => testingConnection = false);
    }
  }

  Future<void> login() async {
    setState(() {
      loading = true;
      error = null;
    });

    try {
      final session = await widget.api.login(
        username.text.trim(),
        password.text,
      );
      widget.onLoggedIn(session);
    } catch (e) {
      if (mounted) setState(() => error = '$e');
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  
  @override
  Widget build(BuildContext context) {
    if (widget.session != null) {
      return AppScaffold(
        title: 'Akun Pengelola',
        subtitle: 'Panel Administratif Utama',
        child: DashboardView(
          api: widget.api,
          session: widget.session!,
          onLogout: widget.onLogout,
        ),
      );
    }

    return Scaffold(
      backgroundColor: kSynoxDark, // Modern Blue background
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                // White SIPENCAK Logo on Dark Background
                Image.asset(
                  'assets/images/sipencak3.png',
                  width: 240,
                  color: Colors.white,
                ),
                const SizedBox(height: 40),
                Container(
                  padding: const EdgeInsets.all(32),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(32),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.1),
                        blurRadius: 20,
                        offset: const Offset(0, 10),
                      ),
                    ],
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      if (error != null) ErrorBox(message: error!),
                      if (error != null) const SizedBox(height: 16),
                      AppInput(
                    controller: username,
                    label: 'Username',
                    icon: Icons.person_outline,
                  ),
                  const SizedBox(height: 20),
                  AppInput(
                    controller: password,
                    label: 'Password',
                    icon: Icons.lock_outline,
                    obscure: true,
                  ),
                  const SizedBox(height: 32),
                  FilledButton(
                    style: FilledButton.styleFrom(
                      backgroundColor: kSynoxDark,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 18),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      elevation: 0,
                    ),
                    onPressed: loading ? null : login,
                    child: loading
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 3,
                              color: Colors.white,
                            ),
                          )
                        : const Text(
                            'Masuk ke Sistem',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                              letterSpacing: 0.5,
                            ),
                          ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    ),
  ),
);
}
}
class AppScaffold extends StatelessWidget {
  const AppScaffold({
    super.key,
    required this.title,
    required this.subtitle,
    required this.child,
    this.floatingActionButton,
  });

  final String title;
  final String subtitle;
  final Widget child;
  final Widget? floatingActionButton;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      floatingActionButton: floatingActionButton,
      bottomNavigationBar: Navigator.canPop(context) ? BottomAppBar(
        color: Colors.white,
        elevation: 10,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          children: [
            TextButton.icon(
              onPressed: () => Navigator.pop(context),
              icon: const Icon(Icons.arrow_back, color: kSynoxDark),
              label: const Text('Kembali', style: TextStyle(color: kSynoxDark, fontWeight: FontWeight.bold)),
            ),
            TextButton.icon(
              onPressed: () => Navigator.of(context).popUntil((route) => route.isFirst),
              icon: const Icon(Icons.home, color: kSynoxDark),
              label: const Text('Beranda', style: TextStyle(color: kSynoxDark, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ) : null,
      body: SafeArea(
      child: Column(
        children: [
          Container(
            width: double.infinity,
            padding: const EdgeInsets.fromLTRB(20, 24, 20, 20),
            decoration: const BoxDecoration(
              color: kSynoxDark,
            ),
            child: Row(
              children: [
                if (Navigator.canPop(context)) ...[
                  IconButton(
                    icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
                    onPressed: () => Navigator.pop(context),
                  ),
                  const SizedBox(width: 8),
                ],
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (title.toUpperCase() == 'SIPENCAK')
                        Image.asset(
                          'assets/images/sipencak3.png',
                          height: 42,
                          fit: BoxFit.contain,
                          color: Colors.white,
                          errorBuilder: (context, error, stackTrace) {
                            return Text(
                              title,
                              style: const TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.w900,
                                color: Colors.white,
                              ),
                            );
                          },
                        )
                      else
                        Text(
                          title,
                          style: const TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            color: Colors.white,
                          ),
                        ),
                      if (subtitle.isNotEmpty && title.toUpperCase() != 'SIPENCAK') ...[
                        const SizedBox(height: 4),
                        Text(
                          subtitle,
                          style: const TextStyle(
                            color: kSynoxLight,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
              ],
            ),
          ),
          Expanded(child: child),
        ],
      ),
    ));
  }
}


class SearchBox extends StatelessWidget {
  const SearchBox({
    super.key,
    required this.controller,
    required this.loading,
    required this.onSubmit,
  });

  final TextEditingController controller;
  final bool loading;
  final VoidCallback onSubmit;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: AppInput(
            controller: controller,
            label: 'Cari nama, NIM, prodi, atau PT',
            icon: Icons.search,
            onSubmitted: (_) => onSubmit(),
          ),
        ),
        const SizedBox(width: 12),
        SizedBox(
          height: 56,
          width: 56,
          child: FilledButton(
            style: FilledButton.styleFrom(
              backgroundColor: kSynoxLime,
              foregroundColor: kSynoxDark,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              padding: EdgeInsets.zero,
            ),
            onPressed: loading ? null : onSubmit,
            child: loading
                ? const SizedBox(
                    width: 24,
                    height: 24,
                    child: CircularProgressIndicator(
                      strokeWidth: 3,
                      color: kSynoxDark,
                    ),
                  )
                : const Icon(Icons.arrow_forward_ios_rounded, size: 20),
          ),
        ),
      ],
    );
  }
}


class AppInput extends StatelessWidget {
  const AppInput({
    super.key,
    required this.controller,
    required this.label,
    required this.icon,
    this.obscure = false,
    this.keyboardType,
    this.onSubmitted,
  });

  final TextEditingController controller;
  final String label;
  final IconData icon;
  final bool obscure;
  final TextInputType? keyboardType;
  final ValueChanged<String>? onSubmitted;

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: controller,
      obscureText: obscure,
      onSubmitted: onSubmitted,
      keyboardType: keyboardType,
      style: const TextStyle(fontWeight: FontWeight.w600, color: kSynoxDark),
      decoration: InputDecoration(
        hintText: label,
        hintStyle: const TextStyle(color: kTextMuted, fontWeight: FontWeight.normal),
        prefixIcon: Icon(icon, color: kSynoxDark),
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(vertical: 18, horizontal: 16),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: kCardBorder),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: kCardBorder),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: kSynoxLime, width: 2),
        ),
      ),
    );
  }
}


class StatTile extends StatelessWidget {
  const StatTile({
    super.key,
    required this.label,
    required this.value,
    required this.icon,
  });

  final String label;
  final int value;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border.all(color: kCardBorder),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: kSynoxLight,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: kSynoxDark, size: 24),
          ),
          const Spacer(),
          Text(
            '$value',
            style: const TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.w900,
              color: kSynoxDark,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: const TextStyle(
              color: kTextMuted,
              fontWeight: FontWeight.w600,
              fontSize: 13,
            ),
          ),
        ],
      ),
    );
  }
}


class FeatureTile extends StatelessWidget {
  const FeatureTile({
    super.key,
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(15),
          decoration: BoxDecoration(
            border: Border.all(color: kCardBorder),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: kSynoxLight,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: kSynoxDark),
              ),
              const Spacer(),
              Text(title, style: const TextStyle(fontWeight: FontWeight.w900)),
              const SizedBox(height: 4),
              Text(
                subtitle,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                  color: kTextMuted,
                  height: 1.25,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class ResultSummary extends StatelessWidget {
  const ResultSummary({super.key, required this.meta, required this.keyword});

  final SearchMeta meta;
  final String keyword;

  @override
  Widget build(BuildContext context) {
    return InfoCard(
      icon: Icons.dataset_outlined,
      title: 'Hasil Data Mahasiswa',
      subtitle:
          'Menampilkan ${meta.from}-${meta.to} dari total ${meta.total} data${keyword.isNotEmpty ? ' untuk "$keyword"' : ''}.',
    );
  }
}

class DetailSection extends StatelessWidget {
  const DetailSection({
    super.key,
    required this.title,
    required this.icon,
    required this.fields,
  });

  final String title;
  final IconData icon;
  final List<DetailField> fields;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, color: kSynoxDark),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            for (final field in fields) ...[
              Text(
                field.label.toUpperCase(),
                style: const TextStyle(
                  fontSize: 11,
                  letterSpacing: .8,
                  color: kTextMuted,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                field.value.isEmpty ? '-' : field.value,
                style: const TextStyle(fontWeight: FontWeight.w800),
              ),
              const SizedBox(height: 12),
            ],
          ],
        ),
      ),
    );
  }
}

class DetailField {
  DetailField(this.label, this.value);
  final String label;
  final String value;
}

class FlowStep extends StatelessWidget {
  const FlowStep({
    super.key,
    required this.icon,
    required this.title,
    required this.subtitle,
  });

  final IconData icon;
  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: InfoCard(icon: icon, title: title, subtitle: subtitle),
    );
  }
}

class InfoCard extends StatelessWidget {
  const InfoCard({
    super.key,
    required this.icon,
    required this.title,
    required this.subtitle,
  });

  final IconData icon;
  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            CircleAvatar(
              backgroundColor: kSynoxLight,
              child: Icon(icon, color: kSynoxDark),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: const TextStyle(
                      color: kTextMuted,
                      height: 1.35,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class EmptyBox extends StatelessWidget {
  const EmptyBox({super.key, required this.title, required this.subtitle});

  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(22),
        child: Column(
          children: [
            const Icon(
              Icons.folder_off_outlined,
              color: kSynoxDark,
              size: 42,
            ),
            const SizedBox(height: 10),
            Text(title, style: const TextStyle(fontWeight: FontWeight.w900)),
            const SizedBox(height: 4),
            Text(
              subtitle,
              textAlign: TextAlign.center,
              style: const TextStyle(
                color: kTextMuted,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class ErrorBox extends StatelessWidget {
  const ErrorBox({super.key, required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFFFF1F2),
        border: Border.all(color: const Color(0xFFFECACA)),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        message,
        style: const TextStyle(
          color: Color(0xFF991B1B),
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}

class SectionTitle extends StatelessWidget {
  const SectionTitle(this.text, {super.key});
  final String text;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Text(
        text,
        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
      ),
    );
  }
}

class StatusChip extends StatelessWidget {
  const StatusChip(this.status, {super.key});

  final String status;

  @override
  Widget build(BuildContext context) {
    final lower = status.toLowerCase();
    Color bg = kSynoxLight;
    Color fg = kSynoxDark;

    if (lower.contains('selesai') || lower.contains('final')) {
      bg = kSynoxLime;
      fg = kSynoxDark;
    } else if (lower.contains('tolak') || lower.contains('henti')) {
      bg = const Color(0xFFFEE2E2);
      fg = const Color(0xFF991B1B);
    } else if (lower.contains('proses') || lower.contains('ajukan')) {
      bg = kSynoxDark;
      fg = kSynoxLime;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        status.toUpperCase(),
        style: TextStyle(
          color: fg,
          fontSize: 11,
          fontWeight: FontWeight.w900,
          letterSpacing: 0.5,
        ),
      ),
    );
  }
}


class MiniChip extends StatelessWidget {
  const MiniChip(this.icon, this.label, {super.key});

  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: kSynoxLight,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: kSynoxDark),
          const SizedBox(width: 6),
          Flexible(
            child: Text(
              label,
              style: const TextStyle(
                color: kSynoxDark,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }
}


class Pager extends StatelessWidget {
  const Pager({
    super.key,
    required this.meta,
    required this.onPrev,
    required this.onNext,
  });

  final SearchMeta meta;
  final VoidCallback? onPrev;
  final VoidCallback? onNext;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            'Halaman ${meta.currentPage} dari ${meta.lastPage}',
            style: const TextStyle(fontWeight: FontWeight.w800),
          ),
          Row(
            children: [
              IconButton.outlined(
                onPressed: onPrev,
                icon: const Icon(Icons.chevron_left),
              ),
              const SizedBox(width: 8),
              IconButton.outlined(
                onPressed: onNext,
                icon: const Icon(Icons.chevron_right),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

String roleLabel(String role) =>
    role == 'operator' ? 'Operator LLDIKTI' : 'Admin PT';

String rupiah(double value) {
  final raw = value.round().toString();
  final buffer = StringBuffer();
  for (var i = 0; i < raw.length; i++) {
    final fromEnd = raw.length - i;
    buffer.write(raw[i]);
    if (fromEnd > 1 && fromEnd % 3 == 1) buffer.write('.');
  }
  return 'Rp ${buffer.toString()}';
}

String _text(dynamic value, {String fallback = '-'}) {
  final text = '${value ?? ''}'.trim();
  return text.isEmpty ? fallback : text;
}

int _int(dynamic value) => int.tryParse('${value ?? 0}') ?? 0;







String _formatLabel(String key) {
  const map = {
    'nama_pt': 'Nama Perguruan Tinggi',
    'id_pt': 'ID PT',
    'kode_pt': 'Kode PT',
    'alamat': 'Alamat',
    'no_telp': 'No. Telepon',
    'no_hp': 'No. HP',
    'nama_pimpinan': 'Nama Pimpinan',
    'website': 'Website',
    'email': 'Email',
    'status': 'Status',
    'judul': 'Judul',
    'deskripsi': 'Deskripsi',
    'keterangan': 'Keterangan',
    'tanggal': 'Tanggal',
    'nominal': 'Nominal (Rp)',
    'nama_prodi': 'Nama Program Studi',
    'jenjang': 'Jenjang',
    'akreditasi': 'Akreditasi',
    'nim': 'NIM',
    'nama_mahasiswa': 'Nama Mahasiswa',
    'semester': 'Semester',
    'ipk': 'IPK',
    'jenis_kelamin': 'Jenis Kelamin',
    'tempat_lahir': 'Tempat Lahir',
    'tanggal_lahir': 'Tanggal Lahir',
    'nik': 'NIK',
    'nama_ibu': 'Nama Ibu',
    'nama_ayah': 'Nama Ayah',
    'pekerjaan_ibu': 'Pekerjaan Ibu',
    'pekerjaan_ayah': 'Pekerjaan Ayah',
    'penghasilan_ibu': 'Penghasilan Ibu',
    'penghasilan_ayah': 'Penghasilan Ayah',
    'no_rek': 'Nomor Rekening',
    'nama_bank': 'Nama Bank',
    'cabang_bank': 'Cabang Bank',
    'nama_rekening': 'Nama Pemilik Rekening',
    'user_id': 'Pengguna',
    'role': 'Peran',
    'username': 'Username',
    'password': 'Password',
    'created_at': 'Dibuat Pada',
    'updated_at': 'Diperbarui Pada',
    'deleted_at': 'Dihapus Pada',
  };
  
  if (map.containsKey(key)) return map[key]!;
  
  // Format camelCase or snake_case to Title Case
  return key.replaceAll('_', ' ').split(' ').map((s) => s.isNotEmpty ? '${s[0].toUpperCase()}${s.substring(1)}' : '').join(' ');
}

bool _isHidden(String key) {
  const hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password', 'password_hash'];
  return hidden.contains(key.toLowerCase());
}


String _getDynamicTitle(Map<String, dynamic> item) {
  final possibleKeys = ['perguruan_tinggi', 'nama_pt', 'nama', 'name', 'username', 'nama_prodi', 'judul', 'title', 'periode', 'email', 'kode_pt', 'action'];
  for (var key in possibleKeys) {
    if (item.containsKey(key) && item[key] != null && item[key].toString().isNotEmpty) {
      return item[key].toString();
    }
  }
  
  // fallback to the first string value we can find
  for (var entry in item.entries) {
     if (entry.key != 'id' && !_isHidden(entry.key) && entry.value != null && entry.value.toString().isNotEmpty) {
        return entry.value.toString();
     }
  }
  return 'Data ID: ${item['id']}';
}

class DynamicCrudScreen extends StatefulWidget {
  final ApiService api;
  final String title;
  final String table;
  const DynamicCrudScreen({super.key, required this.api, required this.title, required this.table});

  @override
  State<DynamicCrudScreen> createState() => _DynamicCrudScreenState();
}

class _DynamicCrudScreenState extends State<DynamicCrudScreen> {
  List<Map<String, dynamic>> items = [];
  bool loading = false;
  String? error;

  @override
  void initState() {
    super.initState();
    loadData();
  }

  Future<void> loadData() async {
    setState(() { loading = true; error = null; });
    try {
      final res = await widget.api.getDynamicTable(widget.table);
      if (mounted) setState(() { items = res; loading = false; });
    } catch (e) {
      if (mounted) setState(() { error = e.toString(); loading = false; });
    }
  }

  Future<void> deleteItem(int id) async {
    bool? confirm = await showDialog(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Hapus Data'),
        content: const Text('Anda yakin ingin menghapus data ini?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Batal')),
          TextButton(onPressed: () => Navigator.pop(c, true), child: const Text('Hapus', style: TextStyle(color: Colors.red))),
        ],
      )
    );
    if (confirm != true) return;
    try {
      await widget.api.deleteDynamicTable(widget.table, id);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Berhasil dihapus')));
      loadData();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final res = await Navigator.push(context, MaterialPageRoute(builder: (_) => DynamicFormScreen(api: widget.api, table: widget.table, isEdit: false)));
          if (res == true) loadData();
        },
        backgroundColor: kSynoxDark,
        child: const Icon(Icons.add, color: Colors.white),
      ),
      body: AppScaffold(
        title: widget.title,
        subtitle: 'Menampilkan data dari tabel ${widget.table}',
        child: Column(
          children: [
            if (error != null) Padding(padding: const EdgeInsets.all(16), child: ErrorBox(message: error!)),
            Expanded(
              child: loading
                ? const Center(child: CircularProgressIndicator(color: kSynoxDark))
                : items.isEmpty
                  ? const Center(child: Text('Tidak ada data'))
                  : ListView.builder(
                      itemCount: items.length,
                      padding: const EdgeInsets.only(bottom: 80, left: 16, right: 16, top: 16),
                      itemBuilder: (context, index) {
                         final item = items[index];
                         return Card(
                          elevation: 0,
                          margin: const EdgeInsets.only(bottom: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                            side: const BorderSide(color: kCardBorder),
                          ),
                          child: ExpansionTile(
                            leading: const CircleAvatar(backgroundColor: kSynoxLight, child: Icon(Icons.table_chart, color: kSynoxDark)),
                            title: Text(_getDynamicTitle(item), style: const TextStyle(fontWeight: FontWeight.bold, color: kSynoxDark)),
                            subtitle: Text('Klik untuk detail'),
                            children: [
                              ...item.entries.where((e) => !_isHidden(e.key)).map((e) => ListTile(
                                dense: true,
                                title: Text(_formatLabel(e.key.toString()), style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.grey)),
                                subtitle: Text(e.value?.toString() ?? '-', style: const TextStyle(color: kSynoxDark, fontSize: 16)),
                              )).toList(),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.end,
                                children: [
                                  TextButton.icon(
                                    icon: const Icon(Icons.edit, color: Colors.blue),
                                    label: const Text('Edit', style: TextStyle(color: Colors.blue)),
                                    onPressed: () async {
                                      final res = await Navigator.push(context, MaterialPageRoute(builder: (_) => DynamicFormScreen(api: widget.api, table: widget.table, isEdit: true, initialData: item)));
                                      if (res == true) loadData();
                                    },
                                  ),
                                  TextButton.icon(
                                    icon: const Icon(Icons.delete, color: Colors.red),
                                    label: const Text('Hapus', style: TextStyle(color: Colors.red)),
                                    onPressed: () => deleteItem(item['id']),
                                  ),
                                ],
                              )
                            ],
                          ),
                        );
                      },
                    ),
            )
          ],
        ),
      ),
    );
  }
}

class DynamicFormScreen extends StatefulWidget {
  final ApiService api;
  final String table;
  final bool isEdit;
  final Map<String, dynamic>? initialData;

  const DynamicFormScreen({super.key, required this.api, required this.table, required this.isEdit, this.initialData});

  @override
  State<DynamicFormScreen> createState() => _DynamicFormScreenState();
}

class _DynamicFormScreenState extends State<DynamicFormScreen> {
  bool loading = true;
  bool saving = false;
  String? error;
  List<String> columns = [];
  final Map<String, TextEditingController> _controllers = {};

  @override
  void initState() {
    super.initState();
    loadSchema();
  }

  Future<void> loadSchema() async {
    try {
      final colsRaw = await widget.api.getDynamicSchema(widget.table);
      final cols = colsRaw.where((c) => !_isHidden(c)).toList();
      for (var col in cols) {
        _controllers[col] = TextEditingController(text: widget.initialData?[col]?.toString() ?? '');
      }
      if (mounted) setState(() { columns = cols; loading = false; });
    } catch (e) {
      if (mounted) setState(() { error = e.toString(); loading = false; });
    }
  }

  Future<void> save() async {
    setState(() { saving = true; error = null; });
    try {
      final data = <String, dynamic>{};
      for (var col in columns) {
        data[col] = _controllers[col]!.text;
      }
      if (widget.isEdit) {
        await widget.api.updateDynamicTable(widget.table, widget.initialData!['id'], data);
      } else {
        await widget.api.storeDynamicTable(widget.table, data);
      }
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      if (mounted) setState(() { error = e.toString(); saving = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: widget.isEdit ? 'Edit ${widget.table}' : 'Tambah ${widget.table}',
      subtitle: 'Formulir dinamis',
      child: loading 
        ? const Center(child: CircularProgressIndicator())
        : SingleChildScrollView(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                if (error != null) ErrorBox(message: error!),
                if (error != null) const SizedBox(height: 16),
                                ...columns.map((col) => Padding(
                  padding: const EdgeInsets.only(bottom: 16),
                  child: AppInput(label: _formatLabel(col), controller: _controllers[col]!, icon: Icons.text_fields),
                )),
                const SizedBox(height: 16),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: kSynoxDark,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    onPressed: saving ? null : save,
                    child: saving
                        ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                        : const Text('Simpan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                  ),
                )
              ],
            ),
        ),
    );
  }
}

class MahasiswaItem {
  final int id;
  final String nama;
  final String nim;
  final String? kodePt;
  final String? kodeProdi;
  final String? jenjang;
  final String? semester;
  final String? status;
  final String? kategori;

  MahasiswaItem({
    required this.id,
    required this.nama,
    required this.nim,
    this.kodePt,
    this.kodeProdi,
    this.jenjang,
    this.semester,
    this.status,
    this.kategori,
  });

  factory MahasiswaItem.fromJson(Map<String, dynamic> json) {
    return MahasiswaItem(
      id: json['id'],
      nama: json['nama'] ?? '-',
      nim: json['nim'] ?? '-',
      kodePt: json['kode_pt'],
      kodeProdi: json['kode_prodi'],
      jenjang: json['jenjang'],
      semester: json['semester'],
      status: json['status'],
      kategori: json['kategori'],
    );
  }

  Map<String, dynamic> toJson() => {
    'nama': nama,
    'nim': nim,
    'kode_pt': kodePt,
    'kode_prodi': kodeProdi,
    'jenjang': jenjang,
    'semester': semester,
    'status': status,
    'kategori': kategori,
  };
}

class MahasiswaAdminListScreen extends StatefulWidget {
  final ApiService api;
  const MahasiswaAdminListScreen({super.key, required this.api});

  @override
  State<MahasiswaAdminListScreen> createState() => _MahasiswaAdminListScreenState();
}

class _MahasiswaAdminListScreenState extends State<MahasiswaAdminListScreen> {
  final searchController = TextEditingController();
  List<MahasiswaItem> items = [];
  bool loading = false;
  String? error;

  @override
  void initState() {
    super.initState();
    loadData();
  }

  Future<void> loadData([String q = '']) async {
    setState(() { loading = true; error = null; });
    try {
      final res = await widget.api.getMahasiswas(q);
      if (mounted) setState(() { items = res; loading = false; });
    } catch (e) {
      if (mounted) setState(() { error = e.toString(); loading = false; });
    }
  }

  Future<void> deleteData(int id) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Hapus Data'),
        content: const Text('Yakin ingin menghapus mahasiswa ini?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Batal')),
          FilledButton(onPressed: () => Navigator.pop(c, true), style: FilledButton.styleFrom(backgroundColor: Colors.red), child: const Text('Hapus')),
        ],
      ),
    );
    if (confirm != true) return;
    
    setState(() => loading = true);
    try {
      await widget.api.deleteMahasiswa(id);
      loadData(searchController.text);
    } catch (e) {
      if (mounted) {
        setState(() => loading = false);
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Gagal menghapus: $e')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Data Mahasiswa',
      subtitle: 'Kelola data mahasiswa (CRUD)',
      floatingActionButton: FloatingActionButton(
        backgroundColor: kSynoxLime,
        foregroundColor: kSynoxDark,
        onPressed: () async {
          final res = await Navigator.push(context, MaterialPageRoute(builder: (_) => MahasiswaFormScreen(api: widget.api)));
          if (res == true) loadData(searchController.text);
        },
        child: const Icon(Icons.add),
      ),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                Expanded(
                  child: AppInput(
                    controller: searchController,
                    label: 'Cari Nama / NIM',
                    icon: Icons.search,
                    onSubmitted: loadData,
                  ),
                ),
                const SizedBox(width: 12),
                FilledButton(
                  style: FilledButton.styleFrom(
                    backgroundColor: kSynoxDark,
                    foregroundColor: kSynoxLime,
                    padding: const EdgeInsets.all(16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  onPressed: () => loadData(searchController.text),
                  child: const Icon(Icons.search),
                ),
              ],
            ),
          ),
          if (error != null) Padding(padding: const EdgeInsets.all(16), child: ErrorBox(message: error!)),
          Expanded(
            child: loading
              ? const Center(child: CircularProgressIndicator(color: kSynoxDark))
              : items.isEmpty
                ? const Center(child: Text('Tidak ada data'))
                : ListView.builder(
                    itemCount: items.length,
                    padding: const EdgeInsets.only(bottom: 80, left: 16, right: 16),
                    itemBuilder: (context, index) {
                      final item = items[index];
                      return Card(
                        elevation: 0,
                        margin: const EdgeInsets.only(bottom: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                          side: const BorderSide(color: kCardBorder),
                        ),
                        child: ListTile(
                          contentPadding: const EdgeInsets.all(16),
                          leading: const CircleAvatar(backgroundColor: kSynoxLight, child: Icon(Icons.person, color: kSynoxDark)),
                          title: Text(item.nama, style: const TextStyle(fontWeight: FontWeight.bold, color: kSynoxDark)),
                          subtitle: Text('${item.nim} • PT: ${item.kodePt ?? '-'}'),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              IconButton(
                                icon: const Icon(Icons.edit, color: Colors.blue),
                                onPressed: () async {
                                  final res = await Navigator.push(context, MaterialPageRoute(builder: (_) => MahasiswaFormScreen(api: widget.api, item: item)));
                                  if (res == true) loadData(searchController.text);
                                },
                              ),
                              IconButton(
                                icon: const Icon(Icons.delete, color: Colors.red),
                                onPressed: () => deleteData(item.id),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
          )
        ],
      ),
    );
  }
}

class MahasiswaFormScreen extends StatefulWidget {
  final ApiService api;
  final MahasiswaItem? item;
  const MahasiswaFormScreen({super.key, required this.api, this.item});

  @override
  State<MahasiswaFormScreen> createState() => _MahasiswaFormScreenState();
}

class _MahasiswaFormScreenState extends State<MahasiswaFormScreen> {
  final nama = TextEditingController();
  final nim = TextEditingController();
  final kodePt = TextEditingController();
  final kodeProdi = TextEditingController();
  final status = TextEditingController();
  bool loading = false;
  String? error;

  @override
  void initState() {
    super.initState();
    if (widget.item != null) {
      nama.text = widget.item!.nama;
      nim.text = widget.item!.nim;
      kodePt.text = widget.item!.kodePt ?? '';
      kodeProdi.text = widget.item!.kodeProdi ?? '';
      status.text = widget.item!.status ?? '';
    }
  }

  Future<void> save() async {
    setState(() { loading = true; error = null; });
    try {
      final req = MahasiswaItem(
        id: widget.item?.id ?? 0,
        nama: nama.text,
        nim: nim.text,
        kodePt: kodePt.text,
        kodeProdi: kodeProdi.text,
        status: status.text,
      );
      if (widget.item == null) {
        await widget.api.createMahasiswa(req);
      } else {
        await widget.api.updateMahasiswa(req);
      }
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      if (mounted) setState(() { error = e.toString(); loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: widget.item == null ? 'Tambah Mahasiswa' : 'Edit Mahasiswa',
      subtitle: 'Formulir data mahasiswa',
      child: ListView(
        padding: const EdgeInsets.all(24),
        children: [
          if (error != null) ErrorBox(message: error!),
          const SizedBox(height: 16),
          AppInput(controller: nama, label: 'Nama Mahasiswa', icon: Icons.person),
          const SizedBox(height: 16),
          AppInput(controller: nim, label: 'Nomor Induk Mahasiswa (NIM)', icon: Icons.badge),
          const SizedBox(height: 16),
          AppInput(controller: kodePt, label: 'Kode Perguruan Tinggi', icon: Icons.account_balance),
          const SizedBox(height: 16),
          AppInput(controller: kodeProdi, label: 'Kode Program Studi', icon: Icons.school),
          const SizedBox(height: 16),
          AppInput(controller: status, label: 'Status Pengajuan', icon: Icons.assignment_turned_in),
          const SizedBox(height: 32),
          SizedBox(
            width: double.infinity,
            child: FilledButton.icon(
              style: FilledButton.styleFrom(
                backgroundColor: kSynoxLime,
                foregroundColor: kSynoxDark,
                padding: const EdgeInsets.symmetric(vertical: 18),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              onPressed: loading ? null : save,
              icon: loading ? const SizedBox(width:20, height:20, child: CircularProgressIndicator(color: kSynoxDark)) : const Icon(Icons.save),
              label: Text(widget.item == null ? 'Simpan Baru' : 'Perbarui Data', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            ),
          )
        ],
      ),
    );
  }
}

class DummyFeatureScreen extends StatelessWidget {
  final String title;
  const DummyFeatureScreen({super.key, required this.title});

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: title,
      subtitle: 'Modul dalam pengembangan (Fase 2)',
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.build_circle, size: 80, color: kCardBorder),
            const SizedBox(height: 16),
            Text(
              'Modul $title',
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: kSynoxDark),
            ),
            const SizedBox(height: 8),
            const Text(
              'Akan diimplementasikan pada fase berikutnya.',
              style: TextStyle(color: kTextMuted),
            ),
            const SizedBox(height: 24),
            OutlinedButton.icon(
              onPressed: () => Navigator.pop(context),
              icon: const Icon(Icons.arrow_back),
              label: const Text('Kembali ke Dashboard'),
            )
          ],
        ),
      ),
    );
  }
}


class DashboardMainScreen extends StatefulWidget {
  final ApiService api;
  final UserSession session;
  final VoidCallback onLogout;

  const DashboardMainScreen({super.key, required this.api, required this.session, required this.onLogout});

  @override
  State<DashboardMainScreen> createState() => _DashboardMainScreenState();
}

class _DashboardMainScreenState extends State<DashboardMainScreen> {
  int _currentIndex = 0;

  @override
  Widget build(BuildContext context) {
    final isAdmin = widget.session.role == 'admin';
    
    // Bottom bar items specific to Dashboard
    final destinations = <NavigationDestination>[
      const NavigationDestination(icon: Icon(Icons.dashboard_outlined), selectedIcon: Icon(Icons.dashboard), label: 'Beranda'),
    ];
    
    final pages = <Widget>[
      DashboardView(api: widget.api, session: widget.session, onLogout: () {
        widget.onLogout();
        Navigator.pop(context); // Go back to main app
      }),
    ];

    if (!isAdmin) {
      // OPERATOR (LLDIKTI)
      destinations.add(const NavigationDestination(icon: Icon(Icons.account_balance_outlined), selectedIcon: Icon(Icons.account_balance), label: 'Data PT'));
      pages.add(DynamicCrudScreen(api: widget.api, title: 'Data Perguruan Tinggi', table: 'pts'));
      
      destinations.add(const NavigationDestination(icon: Icon(Icons.verified_outlined), selectedIcon: Icon(Icons.verified), label: 'Verifikasi'));
      pages.add(VerifikasiPencairanScreen(api: widget.api));

      destinations.add(const NavigationDestination(icon: Icon(Icons.bar_chart_outlined), selectedIcon: Icon(Icons.bar_chart), label: 'Laporan'));
      pages.add(LaporanPencairanScreen(api: widget.api));
    } else {
      // ADMIN (University)
      destinations.add(const NavigationDestination(icon: Icon(Icons.people_outline), selectedIcon: Icon(Icons.people), label: 'Mahasiswa'));
      pages.add(MahasiswaAdminListScreen(api: widget.api));
      
      destinations.add(const NavigationDestination(icon: Icon(Icons.send_outlined), selectedIcon: Icon(Icons.send), label: 'Pengajuan'));
      pages.add(PengajuanPencairanScreen(api: widget.api, session: widget.session));

      destinations.add(const NavigationDestination(icon: Icon(Icons.bar_chart_outlined), selectedIcon: Icon(Icons.bar_chart), label: 'Laporan'));
      pages.add(LaporanPencairanScreen(api: widget.api));
    }

    destinations.add(const NavigationDestination(icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Profil'));
    pages.add(Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.person, size: 100, color: kSynoxDark),
          const SizedBox(height: 16),
          Text(widget.session.role.toUpperCase(), style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: kSynoxDark)),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: () {
              widget.onLogout();
              Navigator.pop(context);
            },
            icon: const Icon(Icons.logout),
            label: const Text('Keluar / Logout'),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
            ),
          )
        ],
      )
    ));

    return Scaffold(
      body: pages[_currentIndex],
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: (idx) => setState(() => _currentIndex = idx),
        destinations: destinations,
      ),
    );
  }
}

class DashboardView extends StatefulWidget {
  final ApiService api;
  final UserSession session;
  final VoidCallback onLogout;

  const DashboardView({super.key, required this.api, required this.session, required this.onLogout});

  @override
  State<DashboardView> createState() => _DashboardViewState();
}

class _DashboardViewState extends State<DashboardView> {
  DashboardStats? stats;
  bool loading = true;

  List<DashboardFeature> get features {
    final role = widget.session.role;
    if (role != 'admin') {
      // operator (LLDIKTI)
      return [
        DashboardFeature('Data User PT', Icons.manage_accounts),
        DashboardFeature('Data Perguruan Tinggi', Icons.account_balance),
        DashboardFeature('Papan Informasi', Icons.campaign),
        DashboardFeature('Verifikasi Pencairan', Icons.account_balance_wallet),
        DashboardFeature('Laporan Pencairan', Icons.assessment),
        DashboardFeature('Audit Trail', Icons.history),
        DashboardFeature('Notifikasi', Icons.notifications_active),
      ];
    } else {
      // admin (University)
      return [
        DashboardFeature('Data Prodi', Icons.menu_book),
        DashboardFeature('Data Mahasiswa', Icons.people),
        DashboardFeature('Pembaharuan Status', Icons.update),
        DashboardFeature('Draft Permohonan', Icons.drafts),
        DashboardFeature('Ajukan Permohonan', Icons.send),
        DashboardFeature('Riwayat Pencairan', Icons.assessment),
        DashboardFeature('Papan Informasi', Icons.info),
        DashboardFeature('Notifikasi', Icons.notifications_active),
      ];
    }
  }

  @override
  void initState() {
    super.initState();
    loadStats();
  }

  Future<void> loadStats() async {
    try {
      final s = await widget.api.getDashboardStats();
      if (mounted) setState(() { stats = s; loading = false; });
    } catch (e) {
      if (mounted) setState(() => loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.all(24),
      children: [
        // Profile & Stats Card
        Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: kSynoxDark,
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(color: kSynoxDark.withValues(alpha: 0.2), blurRadius: 10, offset: const Offset(0, 4)),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const CircleAvatar(
                    radius: 30,
                    backgroundColor: kSynoxLime,
                    child: Icon(Icons.admin_panel_settings, color: kSynoxDark, size: 30),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(widget.session.name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                        Text(widget.session.username, style: const TextStyle(color: kSynoxLight)),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.logout, color: Colors.white70),
                    onPressed: widget.onLogout,
                    tooltip: 'Keluar',
                  ),
                ],
              ),
              const SizedBox(height: 24),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  _StatItem('Mahasiswa', loading ? '...' : '${stats?.totalMahasiswa ?? 0}'),
                  _StatItem('Kampus', loading ? '...' : '${stats?.totalPt ?? 0}'),
                  _StatItem('Pencairan', loading ? '...' : '${stats?.totalPencairan ?? 0}'),
                ],
              ),
            ],
          ),
        ),
        const SizedBox(height: 32),
        const Text('Menu Utama', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: kSynoxDark)),
        const SizedBox(height: 16),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 3,
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
            childAspectRatio: 0.85,
          ),
          itemCount: features.length,
          itemBuilder: (context, index) {
            final f = features[index];
            return InkWell(
              
              onTap: () {
                Widget screen;
                // --- ADMIN FEATURES (University) ---
                if (f.title == 'Data Mahasiswa') {
                  screen = MahasiswaAdminListScreen(api: widget.api);
                } else if (f.title == 'Data Prodi') {
                  screen = DynamicCrudScreen(api: widget.api, title: f.title, table: 'prodis');
                } else if (f.title == 'Draft Permohonan' || f.title == 'Ajukan Permohonan') {
                  screen = PengajuanPencairanScreen(api: widget.api, session: widget.session);
                } 
                // --- OPERATOR FEATURES (LLDIKTI) ---
                else if (f.title == 'Data Perguruan Tinggi') {
                  screen = DynamicCrudScreen(api: widget.api, title: f.title, table: 'pts');
                } else if (f.title == 'Data User PT') {
                  screen = DynamicCrudScreen(api: widget.api, title: f.title, table: 'userpts');
                } else if (f.title == 'Audit Trail') {
                  screen = DynamicCrudScreen(api: widget.api, title: f.title, table: 'activity_logs');
                } else if (f.title == 'Verifikasi Pencairan' || f.title == 'Pembaharuan Status') {
                  screen = VerifikasiPencairanScreen(api: widget.api);
                }
                // --- SHARED FEATURES ---
                else if (f.title == 'Laporan Pencairan' || f.title == 'Riwayat Pencairan') {
                  screen = LaporanPencairanScreen(api: widget.api);
                } else if (f.title == 'Papan Informasi') {
                  screen = DynamicCrudScreen(api: widget.api, title: f.title, table: 'informasis');
                } else if (f.title == 'Notifikasi') {
                  screen = NotifikasiScreen(api: widget.api);
                } else {
                  screen = DummyFeatureScreen(title: f.title);
                }
                Navigator.push(context, MaterialPageRoute(builder: (_) => screen));
              },

              borderRadius: BorderRadius.circular(16),
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border.all(color: kCardBorder),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: const BoxDecoration(
                        color: kSynoxLight,
                        shape: BoxShape.circle,
                      ),
                      child: Icon(f.icon, color: kSynoxDark, size: 28),
                    ),
                    const SizedBox(height: 12),
                    Text(
                      f.title,
                      textAlign: TextAlign.center,
                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: kSynoxDark),
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ],
    );
  }
}

class _StatItem extends StatelessWidget {
  final String label;
  final String value;
  const _StatItem(this.label, this.value);

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(value, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: kSynoxLime)),
        const SizedBox(height: 4),
        Text(label, style: const TextStyle(fontSize: 12, color: Colors.white70)),
      ],
    );
  }
}
