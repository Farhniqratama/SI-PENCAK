# SIPENCAK Mobile

Aplikasi Flutter untuk portal dan pengelolaan ringkas SIPENCAK.

## Menjalankan

Pastikan Laravel berjalan, lalu jalankan Flutter dengan alamat API yang sesuai.

Android emulator:

```bash
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000
```

iOS simulator atau browser lokal:

```bash
flutter run --dart-define=API_BASE_URL=http://127.0.0.1:8000
```

Jika memakai MAMP path project, gunakan URL host yang mengarah ke public Laravel, misalnya:

```bash
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8888/sipencak-lldikti/public
```

## Endpoint yang dipakai

- `GET /api/mobile/stats`
- `GET /api/mobile/mahasiswa/search?q=...`
- `POST /api/mobile/login`
