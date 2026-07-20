import os
from fpdf import FPDF

class PDF(FPDF):
    def header(self):
        # Arial bold 15
        self.set_font('Arial', 'B', 15)
        # Move to the right
        self.cell(80)
        # Title
        self.cell(30, 10, 'Buku Panduan Penggunaan SIPENCAK', 0, 0, 'C')
        # Line break
        self.ln(20)

    def footer(self):
        # Position at 1.5 cm from bottom
        self.set_y(-15)
        # Arial italic 8
        self.set_font('Arial', 'I', 8)
        # Page number
        self.cell(0, 10, 'Halaman ' + str(self.page_no()) + '/{nb}', 0, 0, 'C')

def chapter_title(pdf, num, label):
    # Arial 12
    pdf.set_font('Arial', '', 12)
    # Background color
    pdf.set_fill_color(200, 220, 255)
    # Title
    pdf.cell(0, 6, 'Bab %d : %s' % (num, label), 0, 1, 'L', 1)
    # Line break
    pdf.ln(4)

def chapter_body(pdf, body):
    # Read text file
    # Times 12
    pdf.set_font('Times', '', 12)
    # Output justified text
    pdf.multi_cell(0, 5, body)
    # Line break
    pdf.ln()

def print_chapter(pdf, num, title, body):
    pdf.add_page()
    chapter_title(pdf, num, title)
    chapter_body(pdf, body)

pdf = PDF()
pdf.alias_nb_pages()
pdf.add_page()
pdf.set_font('Times', '', 12)

# Intro
intro_text = """
SIPENCAK (Sistem Informasi Pencairan) LLDIKTI adalah aplikasi berbasis web dan mobile yang dikembangkan untuk memfasilitasi dan mendigitalisasi proses pengajuan dan pencairan dana bantuan atau beasiswa untuk mahasiswa di berbagai Perguruan Tinggi (PT). 

Aplikasi ini menjembatani tiga pihak utama:
1. LLDIKTI (Operator): Bertugas melakukan verifikasi, penyetujuan, dan pengelolaan data master Perguruan Tinggi.
2. Perguruan Tinggi (Admin PT): Bertugas mendata mahasiswa, program studi, dan mengajukan draf permohonan pencairan dana.
3. Publik/Mahasiswa: Dapat mencari dan melihat status data mahasiswa yang terdaftar.

Tujuan utama dari SIPENCAK adalah untuk mewujudkan transparansi, kecepatan, dan akurasi dalam proses pencairan dana bantuan pendidikan.
"""
pdf.multi_cell(0, 5, intro_text)
pdf.ln(5)

# Bab 1
bab1_title = "Fungsi dan Kegunaan Utama"
bab1_body = """
Secara garis besar, aplikasi ini memiliki fungsi dan kegunaan sebagai berikut:

1. Digitalisasi Pengajuan Pencairan
   Aplikasi ini mengubah proses manual pengajuan pencairan menjadi digital. Admin PT dapat mengunggah data mahasiswa yang berhak dan mengajukan pencairan, sementara LLDIKTI dapat langsung memverifikasi data tersebut di dalam sistem.

2. Manajemen Data Induk (Master Data)
   Menyimpan dan mengelola data Perguruan Tinggi, Program Studi, dan Mahasiswa secara terpusat. Hal ini meminimalisir redundansi dan memastikan keakuratan data.

3. Monitoring dan Pelaporan (Tracking & Reporting)
   Semua tahapan mulai dari "Draft", "Verifikasi", "Selesai", hingga "Ditolak" dapat dipantau secara real-time. Tersedia juga fitur unduh laporan dalam bentuk Excel untuk keperluan administrasi dan pengarsipan.

4. Papan Informasi (Announcement)
   Fitur ini memungkinkan LLDIKTI untuk memberikan pengumuman resmi ke seluruh admin PT terkait jadwal, syarat, dan informasi penting lainnya mengenai proses pencairan.
"""
print_chapter(pdf, 1, bab1_title, bab1_body)

# Bab 2
bab2_title = "Panduan Akses Publik"
bab2_body = """
Bagian publik adalah halaman yang dapat diakses oleh siapa saja tanpa perlu melakukan proses login.

1. Halaman Beranda (Home)
   Menampilkan halaman utama portal SIPENCAK.

2. Pencarian Mahasiswa
   Publik dapat melakukan pencarian data mahasiswa untuk melihat status pendaftaran dan detail mahasiswa yang berkaitan dengan bantuan.
   Fitur ini berguna bagi mahasiswa untuk memastikan datanya sudah diusulkan oleh Perguruan Tingginya.
"""
print_chapter(pdf, 2, bab2_title, bab2_body)

# Bab 3
bab3_title = "Panduan Akses Admin (Perguruan Tinggi)"
bab3_body = """
Admin PT adalah perwakilan dari masing-masing Perguruan Tinggi. Fungsi yang dapat dilakukan meliputi:

1. Manajemen Program Studi (Prodi)
   - Menambahkan data prodi baru.
   - Melakukan Import data prodi (via Excel).
   - Memperbarui atau menghapus data prodi.

2. Manajemen Mahasiswa
   - Mengelola data mahasiswa yang akan diusulkan.
   - Melakukan Import data mahasiswa secara massal.
   - Melakukan sinkronisasi data mahasiswa.

3. Proses Pengajuan Pencairan
   - Mengelompokkan mahasiswa yang memenuhi syarat dan membuat "Draft Pencairan".
   - Mengajukan data ke LLDIKTI (Permohonan Pencairan).
   - Memeriksa detail verifikasi, mengetahui mana yang berstatus Selesai atau Ditolak.
   - Mengunduh data mahasiswa dan excel laporan pencairan.

4. Papan Informasi
   Melihat pengumuman terbaru yang dirilis oleh LLDIKTI.
"""
print_chapter(pdf, 3, bab3_title, bab3_body)

# Bab 4
bab4_title = "Panduan Akses Operator (LLDIKTI)"
bab4_body = """
Operator adalah pihak dari LLDIKTI yang bertindak sebagai verifikator dan pengelola sistem secara keseluruhan. Fungsi yang dapat dilakukan meliputi:

1. Manajemen Perguruan Tinggi
   - Menambahkan, mengedit, dan menghapus entri Perguruan Tinggi (PT).
   - Upload Excel untuk mengimport daftar PT.

2. Manajemen Akun Admin PT (User PT)
   - Membuat akun untuk Admin Perguruan Tinggi.
   - Memanajemen (reset password, hapus) akses pengguna perwakilan PT.

3. Pengelolaan Informasi (Papan Informasi)
   - Membuat dan menyiarkan pengumuman kepada seluruh PT yang terdaftar di dalam sistem.

4. Verifikasi dan Penetapan Pencairan
   - Melihat daftar ajuan pencairan yang dikirim oleh Admin PT.
   - Memeriksa detail mahasiswa per ajuan.
   - Menetapkan status pencairan menjadi "Selesai" (Disetujui) atau "Ditolak".
   - Mengunduh rekap Excel pencairan untuk keperluan pencetakan dokumen legal/keuangan.

5. Activity Logs (Log Aktivitas)
   - Memantau rekam jejak sistem (siapa yang login, siapa yang mengubah data, dsb) untuk keamanan dan audit.
"""
print_chapter(pdf, 4, bab4_title, bab4_body)

pdf.output('Buku_Panduan_SIPENCAK.pdf', 'F')
print("PDF generated successfully.")
