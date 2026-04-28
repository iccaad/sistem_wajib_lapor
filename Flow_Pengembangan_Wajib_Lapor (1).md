# Flow Pengembangan — Sistem Wajib Lapor Digital
### Panduan Vibe Coder + AI Agent Workflow

---

## Cara Baca Dokumen Ini

```
[CLAUDE]      → Konsultasikan / evaluasi dengan Claude (kamu lagi baca ini)
[AI AGENT]    → Berikan prompt ini ke AI Agent (Antigravity / Claude Opus)
[KAMU]        → Tindakan manual oleh kamu (buka browser, cek terminal, dll)
[CHECKPOINT]  → Wajib review sebelum lanjut ke fase berikutnya
```

---

## Prinsip Dasar Vibe Coding Proyek Ini

```
1. SATU chunk per sesi AI Agent — jangan gabung dua prompt sekaligus
2. Selalu cek hasilnya di browser/terminal sebelum lanjut
3. Jika AI Agent mulai keluar jalur → stop, konsultasi ke Claude
4. Checkpoint = gerbang wajib, jangan dilompati
5. Urutan fase HARUS diikuti — ada dependency antar fase
```

**Alur kerja per chunk:**
```
Claude (dapat prompt chunk ini) → AI Agent eksekusi → Kamu cek hasilnya
→ Berhasil? Lanjut chunk berikutnya
→ Ada masalah? Konsultasi Claude dulu
```

---

## FASE 0 — Persiapan Environment

### [KAMU] Langkah 0.1 — Pastikan Laragon Berjalan

```
1. Buka Laragon
2. Start semua service (Apache/Nginx + PostgreSQL)
3. Pastikan PostgreSQL running di port 5432
4. Buka HeidiSQL yang ada di Laragon
5. Buat koneksi baru:
   - Network type: PostgreSQL
   - Host: 127.0.0.1
   - Port: 5432
   - User: postgres
   - Password: (kosong, atau sesuai yang kamu set)
6. Konek → buat database baru dengan nama: wajib_lapor_db
```

### [KAMU] Langkah 0.2 — Install Laravel 13

Buka terminal di folder `www` atau `htdocs` Laragon:

```bash
composer create-project laravel/laravel wajib-lapor

cd wajib-lapor

# Verifikasi Laravel 13
php artisan --version
```

### [KAMU] Langkah 0.3 — Install Laravel Breeze

```bash
composer require laravel/breeze --dev

php artisan breeze:install blade

# Saat ditanya, pilih:
# - Stack: blade
# - Testing: tidak perlu (tekan enter/skip)

npm install
npm run build
```

### [AI AGENT] Prompt Chunk 0.1 — Konfigurasi Awal Project

```
Kamu adalah Laravel 13 expert. Lakukan konfigurasi awal project Laravel 13 berikut.
Project menggunakan Laravel Breeze yang sudah terinstall dengan stack Blade + Tailwind.

TUGAS 1 — config/app.php:
- Set name = "Sistem Wajib Lapor"
- Set timezone = "Asia/Jakarta"
- Set locale = "id"
- Set faker_locale = "id_ID"

TUGAS 2 — File .env, ubah bagian database:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=wajib_lapor_db
DB_USERNAME=postgres
DB_PASSWORD=

TUGAS 3 — Breeze secara default membuat route auth di /login, /register, dll.
Kita perlu memodifikasi agar:
- Route auth Breeze (login admin) berada di prefix /admin
- /login akan dipakai oleh peserta (NIK only), bukan Breeze

Lakukan langkah berikut:
a. Buka routes/auth.php yang dibuat Breeze
b. Tambahkan prefix 'admin' dan name 'admin.' ke semua route yang ada di sana
   Contoh: Route::get('login') → Route::get('admin/login')
   Atau bungkus dalam: Route::prefix('admin')->name('admin.')->group(function() { ... })
c. Pastikan route name login Breeze berubah menjadi 'admin.login'
d. Di routes/web.php, hapus atau comment baris: require __DIR__.'/auth.php';
   Lalu tambahkan: require __DIR__.'/auth.php'; di dalam atau setelah perubahan

TUGAS 4 — Di app/Http/Controllers/Auth/AuthenticatedSessionController.php (Breeze):
- Ubah redirect setelah login berhasil dari RouteServiceProvider::HOME ke '/admin/dashboard'
- Ubah redirect setelah logout ke '/admin/login'

TUGAS 5 — Di app/Providers/RouteServiceProvider.php atau config:
- Pastikan HOME constant atau redirect default setelah auth mengarah ke '/admin/dashboard'

TUGAS 6 — Di middleware Authenticate.php:
- Pastikan redirect jika tidak authenticated mengarah ke 'admin.login' untuk route /admin/*
- Untuk route /peserta/* akan ditangani middleware terpisah (belum dibuat di fase ini)

Jangan buat file baru selain yang disebutkan. Hanya modifikasi file yang ada.
```

### [KAMU] Cek Fase 0

```bash
php artisan config:clear
php artisan route:clear
php artisan route:list | grep admin
```

Pastikan route `/admin/login` (GET dan POST) sudah ada di daftar route.

---

## FASE 1 — Database Migration

> **Urutan migration WAJIB diikuti.** PostgreSQL enforce foreign key — jika urutan salah, migration akan error.

### [AI AGENT] Prompt Chunk 1.1 — Migration: Modifikasi Tabel Users

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.

Laravel Breeze sudah membuat migration untuk tabel users.
Buat satu migration BARU (bukan edit yang lama) untuk MEMODIFIKASI tabel users yang sudah ada.

Nama migration: add_custom_columns_to_users_table

Kolom yang ditambahkan:
1. role: string, panjang 10, NOT NULL, default 'peserta'
   - Nilai yang valid: 'admin' atau 'peserta'
   - Tambahkan check constraint jika PostgreSQL: CHECK (role IN ('admin', 'peserta'))
   
2. nik: char(16), NULLABLE, UNIQUE
   - Ini identifier login untuk peserta
   - Admin akan memiliki nik = NULL
   - Catatan PostgreSQL: unique constraint dengan nullable berarti boleh banyak NULL, tapi nilai non-null harus unik

3. is_active: boolean, NOT NULL, default true

Untuk kolom email yang sudah ada (dibuat Breeze):
- Ubah menjadi NULLABLE (karena peserta tidak punya email)
- Tetap UNIQUE

Untuk kolom password yang sudah ada (dibuat Breeze):
- Ubah menjadi NULLABLE (karena peserta tidak butuh password — login dengan NIK saja)

Di method down() (rollback), kembalikan semua perubahan.

Setelah migration dibuat:
php artisan migrate
```

### [AI AGENT] Prompt Chunk 1.2 — Migration: Tabel participants

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.
Buat migration: create_participants_table

Kolom (dalam urutan ini):
- id: bigIncrements
- user_id: foreignId → constrained ke tabel users → onDelete cascade
- assigned_admin_id: unsignedBigInteger → nullable → foreign key ke users(id) → onDelete set null
- name: string(255) → NOT NULL
- nik: char(16) → NOT NULL (copy dari users.nik untuk akses cepat)
- address: text → nullable
- phone: string(20) → nullable
- violation_type: string(255) → NOT NULL
- case_notes: text → nullable
- supervision_start: date → NOT NULL
- supervision_end: date → NOT NULL
- quota_type: string(10) → NOT NULL → CHECK IN ('weekly', 'monthly')
- quota_amount: integer → NOT NULL
- timestamps()

Tambahkan foreign key untuk assigned_admin_id secara manual setelah kolom dibuat:
$table->foreign('assigned_admin_id')->references('id')->on('users')->onDelete('set null');

Jalankan: php artisan migrate
```

### [AI AGENT] Prompt Chunk 1.3 — Migration: Tabel locations

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.
Buat migration: create_locations_table

Kolom:
- id: bigIncrements
- name: string(255) NOT NULL
- address: text nullable
- latitude: decimal(10,8) NOT NULL
- longitude: decimal(11,8) NOT NULL
- radius_meters: integer NOT NULL default(100)
- is_active: boolean NOT NULL default(true)
- timestamps()

Jalankan: php artisan migrate
```

### [AI AGENT] Prompt Chunk 1.4 — Migration: Tabel attendance_periods

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.
Buat migration: create_attendance_periods_table

Kolom:
- id: bigIncrements
- participant_id: foreignId constrained ke participants → onDelete cascade
- period_start: date NOT NULL
- period_end: date NOT NULL
- quota_target: integer NOT NULL  (snapshot dari quota_amount saat periode dibuat)
- created_at: timestamp nullable  (hanya created_at, tidak ada updated_at)

CATATAN: Gunakan $table->timestamp('created_at')->nullable(); dan JANGAN gunakan $table->timestamps() karena tabel ini tidak butuh updated_at.

Jalankan: php artisan migrate
```

### [AI AGENT] Prompt Chunk 1.5 — Migration: Tabel attendance_logs

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.
Buat migration: create_attendance_logs_table

Kolom:
- id: bigIncrements
- participant_id: foreignId constrained ke participants → onDelete cascade
- location_id: unsignedBigInteger nullable → foreign key ke locations(id) → onDelete set null
- period_id: unsignedBigInteger NOT NULL → foreign key ke attendance_periods(id)
- attended_at: date NOT NULL
- latitude: decimal(10,8) nullable
- longitude: decimal(11,8) nullable
- accuracy_meters: decimal(8,2) nullable
- photo_path: string(500) nullable
- is_manual_override: boolean NOT NULL default(false)
- override_reason: text nullable
- override_by: unsignedBigInteger nullable → foreign key ke users(id) → onDelete set null
- timestamps()

Tambahkan UNIQUE constraint: unique(['participant_id', 'attended_at'])
Ini enforce aturan "hanya 1 absensi per hari per peserta" di level database.

Tambahkan semua foreign key secara manual untuk kolom nullable.

Jalankan: php artisan migrate
```

### [AI AGENT] Prompt Chunk 1.6 — Migration: Tabel attendance_attempts

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.
Buat migration: create_attendance_attempts_table

Kolom:
- id: bigIncrements
- participant_id: foreignId constrained ke participants → onDelete cascade
- attempted_at: timestamp NOT NULL
- latitude: decimal(10,8) nullable
- longitude: decimal(11,8) nullable
- accuracy_meters: decimal(8,2) nullable
- nearest_location_id: unsignedBigInteger nullable → foreign key ke locations(id) → onDelete set null
- distance_meters: decimal(10,2) nullable
- rejection_reason: string(255) NOT NULL
- created_at: timestamp nullable (hanya created_at, tidak updated_at)

Tambahkan foreign key untuk nearest_location_id secara manual.

Jalankan: php artisan migrate
```

### [AI AGENT] Prompt Chunk 1.7 — Migration: Tabel warnings

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.
Buat migration: create_warnings_table

Kolom:
- id: bigIncrements
- participant_id: foreignId constrained ke participants → onDelete cascade
- triggered_period_id: unsignedBigInteger NOT NULL → foreign key ke attendance_periods(id)
- level: smallInteger NOT NULL → CHECK (level IN (1, 2, 3))
- message: text NOT NULL
- is_resolved: boolean NOT NULL default(false)
- resolved_at: timestamp nullable
- timestamps()

Tambahkan foreign key triggered_period_id secara manual.

Jalankan: php artisan migrate
```

### [AI AGENT] Prompt Chunk 1.8 — Migration: Tabel activity_logs

```
Kamu adalah Laravel 13 expert dengan PostgreSQL.
Buat migration: create_activity_logs_table

Kolom:
- id: bigIncrements
- user_id: foreignId constrained ke users → onDelete cascade
- action: string(100) NOT NULL
- target_type: string(50) nullable
- target_id: bigInteger nullable
- description: text nullable
- ip_address: string(45) nullable
- created_at: timestamp nullable (hanya created_at, tidak updated_at)

Jalankan: php artisan migrate
```

### [KAMU] Cek Fase 1

```bash
# Cek semua tabel terbuat
php artisan migrate:status

# Cek di HeidiSQL → wajib_lapor_db → pastikan ada 9+ tabel:
# users, password_reset_tokens (Breeze), sessions (Breeze),
# participants, locations, attendance_periods, attendance_logs,
# attendance_attempts, warnings, activity_logs
```

### **[CHECKPOINT 1]** — Semua migration berhasil tanpa error → Konsultasi Claude jika ada yang aneh → Lanjut ke Fase 2

---

## FASE 2 — Eloquent Models

### [AI AGENT] Prompt Chunk 2.1 — Model User (Update)

```
Kamu adalah Laravel 13 expert.
Update Model User yang sudah ada (dibuat Breeze) di app/Models/User.php.

TAMBAHKAN / UBAH:

$fillable:
['name', 'email', 'nik', 'password', 'role', 'is_active']

$hidden:
['password', 'remember_token']

$casts:
[
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'is_active' => 'boolean',
]

TAMBAHKAN relasi:
- participant(): hasOne(Participant::class) → peserta yang terhubung ke akun ini (via user_id)
- assignedParticipants(): hasMany(Participant::class, 'assigned_admin_id') → peserta yang ditangani admin ini
- activityLogs(): hasMany(ActivityLog::class)

TAMBAHKAN helper methods:
- isAdmin(): bool → return $this->role === 'admin';
- isPeserta(): bool → return $this->role === 'peserta';

Jangan hapus atau ubah method/interface yang sudah ada dari Breeze (Authenticatable, dll).
```

### [AI AGENT] Prompt Chunk 2.2 — Model Participant

```
Kamu adalah Laravel 13 expert.
Buat Model: app/Models/Participant.php

$fillable:
['user_id', 'assigned_admin_id', 'name', 'nik', 'address', 'phone',
 'violation_type', 'case_notes', 'supervision_start', 'supervision_end',
 'quota_type', 'quota_amount']

$casts:
['supervision_start' => 'date', 'supervision_end' => 'date']

Relasi:
- user(): belongsTo(User::class)
- assignedAdmin(): belongsTo(User::class, 'assigned_admin_id')
- attendancePeriods(): hasMany(AttendancePeriod::class)
- attendanceLogs(): hasMany(AttendanceLog::class)
- attendanceAttempts(): hasMany(AttendanceAttempt::class)
- warnings(): hasMany(Warning::class)

Helper methods:
- isActive(): bool
  return today()->between($this->supervision_start, $this->supervision_end);

- getRemainingDays(): int
  return max(0, now()->startOfDay()->diffInDays($this->supervision_end->startOfDay(), false));

- getCurrentPeriod(): ?AttendancePeriod
  return $this->attendancePeriods()
              ->where('period_start', '<=', today())
              ->where('period_end', '>=', today())
              ->first();

- hasAbsentToday(): bool
  return $this->attendanceLogs()
              ->where('attended_at', today())
              ->exists();
```

### [AI AGENT] Prompt Chunk 2.3 — Model Location

```
Kamu adalah Laravel 13 expert.
Buat Model: app/Models/Location.php

$fillable:
['name', 'address', 'latitude', 'longitude', 'radius_meters', 'is_active']

$casts:
['latitude' => 'decimal:8', 'longitude' => 'decimal:8', 'is_active' => 'boolean']

Relasi:
- attendanceLogs(): hasMany(AttendanceLog::class)
- attendanceAttempts(): hasMany(AttendanceAttempt::class, 'nearest_location_id')

Scope:
- scopeActive($query): return $query->where('is_active', true);
```

### [AI AGENT] Prompt Chunk 2.4 — Model AttendancePeriod

```
Kamu adalah Laravel 13 expert.
Buat Model: app/Models/AttendancePeriod.php

$fillable:
['participant_id', 'period_start', 'period_end', 'quota_target']

$casts:
['period_start' => 'date', 'period_end' => 'date']

Timestamps: set public $timestamps = false; lalu tambahkan:
protected $dates = ['created_at'];
const CREATED_AT = 'created_at';

Relasi:
- participant(): belongsTo(Participant::class)
- attendanceLogs(): hasMany(AttendanceLog::class, 'period_id')
- warnings(): hasMany(Warning::class, 'triggered_period_id')

Helper methods:
- getAttendanceCount(): int
  return $this->attendanceLogs()->count();

- getRemainingCount(): int
  return max(0, $this->quota_target - $this->getAttendanceCount());

- getRemainingDays(): int
  return max(0, now()->startOfDay()->diffInDays($this->period_end->startOfDay(), false));

- isCompleted(): bool
  return $this->getAttendanceCount() >= $this->quota_target;

- hasEnded(): bool
  return today()->isAfter($this->period_end);
```

### [AI AGENT] Prompt Chunk 2.5 — Model AttendanceLog

```
Kamu adalah Laravel 13 expert.
Buat Model: app/Models/AttendanceLog.php

$fillable:
['participant_id', 'location_id', 'period_id', 'attended_at', 'latitude',
 'longitude', 'accuracy_meters', 'photo_path', 'is_manual_override',
 'override_reason', 'override_by']

$casts:
['attended_at' => 'date', 'is_manual_override' => 'boolean']

Relasi:
- participant(): belongsTo(Participant::class)
- location(): belongsTo(Location::class)
- period(): belongsTo(AttendancePeriod::class, 'period_id')
- overriddenBy(): belongsTo(User::class, 'override_by')
```

### [AI AGENT] Prompt Chunk 2.6 — Model AttendanceAttempt

```
Kamu adalah Laravel 13 expert.
Buat Model: app/Models/AttendanceAttempt.php

$fillable:
['participant_id', 'attempted_at', 'latitude', 'longitude', 'accuracy_meters',
 'nearest_location_id', 'distance_meters', 'rejection_reason']

$casts:
['attempted_at' => 'datetime']

public $timestamps = false;
protected $dates = ['created_at'];
const CREATED_AT = 'created_at';

Relasi:
- participant(): belongsTo(Participant::class)
- nearestLocation(): belongsTo(Location::class, 'nearest_location_id')
```

### [AI AGENT] Prompt Chunk 2.7 — Model Warning

```
Kamu adalah Laravel 13 expert.
Buat Model: app/Models/Warning.php

$fillable:
['participant_id', 'triggered_period_id', 'level', 'message', 'is_resolved', 'resolved_at']

$casts:
['is_resolved' => 'boolean', 'resolved_at' => 'datetime']

Relasi:
- participant(): belongsTo(Participant::class)
- triggeredPeriod(): belongsTo(AttendancePeriod::class, 'triggered_period_id')

Scope:
- scopeActive($query): return $query->where('is_resolved', false);
```

### [AI AGENT] Prompt Chunk 2.8 — Model ActivityLog

```
Kamu adalah Laravel 13 expert.
Buat Model: app/Models/ActivityLog.php

$fillable:
['user_id', 'action', 'target_type', 'target_id', 'description', 'ip_address']

public $timestamps = false;
protected $dates = ['created_at'];
const CREATED_AT = 'created_at';

Relasi:
- user(): belongsTo(User::class)
```

### [KAMU] Cek Fase 2

```bash
php artisan tinker

# Coba satu per satu:
User::count();
Participant::with('user')->get();
Location::active()->get();
```

### **[CHECKPOINT 2]** — Semua model bisa diakses via tinker tanpa error → Lanjut ke Fase 3

---

## FASE 3 — Authentication (Dua Jalur Terpisah)

> **Fase paling kritis.** Admin menggunakan Breeze (sudah ada), Peserta pakai custom NIK-only login. Baca seluruh prompt sebelum dikerjakan.

### [AI AGENT] Prompt Chunk 3.1 — Login Peserta (NIK Only)

```
Kamu adalah Laravel 13 expert.
Buat sistem login KHUSUS PESERTA yang sepenuhnya terpisah dari Breeze.
Peserta login HANYA dengan NIK (16 digit angka) — tidak ada password sama sekali.

BAGIAN 1: PesertaAuthController (app/Http/Controllers/PesertaAuthController.php)

Method showLogin():
- Jika sudah login (Auth::check()) dan role = peserta → redirect ke /peserta/dashboard
- Jika sudah login dan role = admin → redirect ke /admin/dashboard
- Return view('peserta-auth.login')

Method login(Request $request):
Validasi:
- nik: required | digits:16

Logic:
$user = User::where('nik', $request->nik)
            ->where('role', 'peserta')
            ->first();

if (!$user) {
    return back()->withErrors(['nik' => 'NIK tidak terdaftar dalam sistem. Hubungi petugas Polres.']);
}

if (!$user->is_active) {
    return back()->withErrors(['nik' => 'Akun Anda dinonaktifkan. Hubungi petugas Polres.']);
}

// Login tanpa password check
Auth::login($user, false); // false = jangan remember
$request->session()->regenerate();
return redirect()->intended('/peserta/dashboard');

Method logout(Request $request):
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
return redirect('/login');

BAGIAN 2: View Login Peserta (resources/views/peserta-auth/login.blade.php)

Tampilan bersih dan fokus:
- Background abu-abu gelap atau biru tua (kesan formal instansi)
- Card di tengah halaman
- Header: logo + "SISTEM WAJIB LAPOR" + nama Polres (bisa dikonfigurasi)
- Satu input field: NIK
  * type="text"
  * maxlength="16"
  * pattern="[0-9]*"
  * inputmode="numeric" (keyboard angka di mobile)
  * placeholder="Contoh: 3374010101990001"
  * autofocus
- Tombol: "Masuk ke Sistem Wajib Lapor"
- Teks kecil di bawah: "Jika NIK tidak terdaftar, hubungi petugas Polres."
- TIDAK ada link apapun ke halaman admin
- TIDAK ada link "lupa password" (karena tidak ada password)
- Tampilkan @error('nik') dalam kotak merah jika ada error
- Sertakan @csrf

Gunakan Tailwind CSS (sudah terinstall via Breeze).
Jangan pakai layout admin — ini halaman standalone.

BAGIAN 3: Route di routes/web.php

// Peserta auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [PesertaAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [PesertaAuthController::class, 'login'])->name('peserta.login.post');
});

Route::post('/logout', [PesertaAuthController::class, 'logout'])->name('peserta.logout')->middleware('auth');

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect('/admin/dashboard')
            : redirect('/peserta/dashboard');
    }
    return redirect('/login');
});

CATATAN PENTING: Jangan ganggu route /admin/login yang sudah dihandle Breeze di routes/auth.php.
```

### [AI AGENT] Prompt Chunk 3.2 — Middleware Role & Log Activity

```
Kamu adalah Laravel 13 expert.

BAGIAN 1: RoleMiddleware (app/Http/Middleware/RoleMiddleware.php)

Middleware ini menerima parameter $role ('admin' atau 'peserta').

Logic:
if (!Auth::check()) {
    // Tentukan halaman login yang tepat berdasarkan prefix URL
    if (str_starts_with($request->path(), 'admin')) {
        return redirect()->route('admin.login'); // Breeze route
    }
    return redirect()->route('login'); // Peserta route
}

$user = Auth::user();

if ($user->role !== $role) {
    // User login tapi role salah
    if ($user->isAdmin()) {
        return redirect('/admin/dashboard');
    }
    return redirect('/peserta/dashboard');
}

if (!$user->is_active) {
    Auth::logout();
    $request->session()->invalidate();
    if ($user->isAdmin()) {
        return redirect()->route('admin.login')->withErrors(['email' => 'Akun dinonaktifkan.']);
    }
    return redirect()->route('login')->withErrors(['nik' => 'Akun dinonaktifkan.']);
}

return $next($request);

BAGIAN 2: LogActivityMiddleware (app/Http/Middleware/LogActivityMiddleware.php)

Jalankan SETELAH response (terminate middleware).

Catat ke ActivityLog hanya jika:
- Method = POST, PUT, PATCH, atau DELETE
- User terautentikasi DAN role = admin

Data yang disimpan:
- user_id: Auth::id()
- action: $request->method() . ' ' . ($request->route()->getName() ?? $request->path())
- target_type: ambil nama model dari route parameter jika ada (misal 'participant' dari route participant/{participant})
- target_id: ambil value parameter numerik dari route jika ada
- ip_address: $request->ip()

BAGIAN 3: Daftarkan middleware di bootstrap/app.php (Laravel 13 style)

Di withMiddleware():
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'log.activity' => \App\Http\Middleware\LogActivityMiddleware::class,
]);

BAGIAN 4: Route Groups di routes/web.php

Tambahkan dua route group ini (kosong dulu, isi di fase berikutnya):

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['role:admin', 'log.activity'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    // (route lain akan ditambahkan di fase berikutnya)
});

// Peserta routes  
Route::prefix('peserta')->name('peserta.')->middleware(['role:peserta'])->group(function () {
    Route::get('/dashboard', [Participant\DashboardController::class, 'index'])->name('dashboard');
    // (route lain akan ditambahkan di fase berikutnya)
});

Pastikan import namespace controller sudah ada di atas file routes/web.php.
```

### [KAMU] Cek Fase 3

```
1. Buka /login → pastikan form NIK muncul (hanya satu field)
2. Buka /admin/login → pastikan form Breeze (email + password) muncul
3. Buka / → pastikan redirect ke /login
4. Coba akses /admin/dashboard tanpa login → redirect ke /admin/login
5. Coba akses /peserta/dashboard tanpa login → redirect ke /login
   (error 404 wajar di sini karena controller belum ada)
```

> Seeder belum ada, jadi belum bisa test login. Itu akan dicek di akhir Fase 4.

### **[CHECKPOINT 3]** — Struktur route sudah benar, dua halaman login muncul → Lanjut ke Fase 4

---

## FASE 4 — Seeder & Data Awal

### [AI AGENT] Prompt Chunk 4.1 — Seeder Admin & Lokasi

```
Kamu adalah Laravel 13 expert.

SEEDER 1: database/seeders/AdminSeeder.php

Buat 2 akun admin:
User::create([
    'name' => 'Admin Polres',
    'email' => 'admin@polres.id',
    'password' => Hash::make('password123'),
    'role' => 'admin',
    'nik' => null,
    'is_active' => true,
]);

User::create([
    'name' => 'Petugas Piket',
    'email' => 'piket@polres.id',
    'password' => Hash::make('password123'),
    'role' => 'admin',
    'nik' => null,
    'is_active' => true,
]);

SEEDER 2: database/seeders/LocationSeeder.php

Buat 2 lokasi (koordinat area Semarang):
Location::create([
    'name' => 'Sasana Olahraga Polres',
    'address' => 'Jl. Pemuda No. 1, Semarang Tengah, Kota Semarang',
    'latitude' => -6.9667,
    'longitude' => 110.4167,
    'radius_meters' => 100,
    'is_active' => true,
]);

Location::create([
    'name' => 'Balai Pembinaan',
    'address' => 'Jl. Mgr. Sugiyopranoto No. 5, Semarang Barat, Kota Semarang',
    'latitude' => -6.9700,
    'longitude' => 110.4100,
    'radius_meters' => 150,
    'is_active' => true,
]);

SEEDER 3: database/seeders/DatabaseSeeder.php

$this->call([
    AdminSeeder::class,
    LocationSeeder::class,
    // ParticipantSeeder akan ditambahkan setelah dibuat
]);

Jalankan: php artisan db:seed
```

### [AI AGENT] Prompt Chunk 4.2 — Seeder Peserta

```
Kamu adalah Laravel 13 expert.

PENTING tentang akun peserta:
- Peserta login HANYA dengan NIK, tidak ada password
- Kolom password di tabel users diisi NULL untuk peserta
- Kolom email diisi NULL untuk peserta

Buat: database/seeders/ParticipantSeeder.php

Ambil admin pertama: $admin1 = User::where('email', 'admin@polres.id')->first();
Ambil admin kedua: $admin2 = User::where('email', 'piket@polres.id')->first();

PESERTA 1:
$user1 = User::create([
    'name' => 'Budi Santoso',
    'email' => null,
    'password' => null,
    'nik' => '3374010101990001',
    'role' => 'peserta',
    'is_active' => true,
]);
Participant::create([
    'user_id' => $user1->id,
    'assigned_admin_id' => $admin1->id,
    'name' => 'Budi Santoso',
    'nik' => '3374010101990001',
    'address' => 'Jl. Anggrek No. 12, Semarang Utara',
    'phone' => '081234567890',
    'violation_type' => 'Balap Liar',
    'case_notes' => 'Tertangkap balap liar di Jl. Pemuda tanggal ...',
    'supervision_start' => now()->toDateString(),
    'supervision_end' => now()->addDays(60)->toDateString(),
    'quota_type' => 'weekly',
    'quota_amount' => 2,
]);

PESERTA 2:
$user2 = User::create([
    'name' => 'Andi Prasetyo',
    'email' => null,
    'password' => null,
    'nik' => '3374010202000002',
    'role' => 'peserta',
    'is_active' => true,
]);
Participant::create([
    'user_id' => $user2->id,
    'assigned_admin_id' => $admin1->id,
    'name' => 'Andi Prasetyo',
    'nik' => '3374010202000002',
    'address' => 'Jl. Melati No. 5, Semarang Selatan',
    'phone' => '082345678901',
    'violation_type' => 'Tawuran',
    'case_notes' => 'Terlibat tawuran antar kelompok di ...',
    'supervision_start' => now()->toDateString(),
    'supervision_end' => now()->addDays(90)->toDateString(),
    'quota_type' => 'monthly',
    'quota_amount' => 6,
]);

PESERTA 3:
$user3 = User::create([
    'name' => 'Riko Hidayat',
    'email' => null,
    'password' => null,
    'nik' => '3374010303010003',
    'role' => 'peserta',
    'is_active' => true,
]);
Participant::create([
    'user_id' => $user3->id,
    'assigned_admin_id' => $admin2->id,
    'name' => 'Riko Hidayat',
    'nik' => '3374010303010003',
    'address' => 'Jl. Mawar No. 8, Semarang Timur',
    'phone' => '083456789012',
    'violation_type' => 'Gangguan Ketertiban Umum',
    'case_notes' => 'Membuat keributan di ...',
    'supervision_start' => now()->toDateString(),
    'supervision_end' => now()->addDays(30)->toDateString(),
    'quota_type' => 'weekly',
    'quota_amount' => 3,
]);

// TODO: Setelah PeriodService dibuat di Fase 5, panggil generateFirstPeriod untuk setiap peserta
// $periodService = new PeriodService();
// $periodService->generateFirstPeriod(Participant::find(1));
// dst.

Tambahkan ParticipantSeeder ke DatabaseSeeder:
$this->call([
    AdminSeeder::class,
    LocationSeeder::class,
    ParticipantSeeder::class,
]);

Jalankan: php artisan db:seed --class=ParticipantSeeder
```

### [KAMU] Cek Fase 4 — Test Login

```
TEST 1 — Login Admin:
→ Buka /admin/login
→ Email: admin@polres.id | Password: password123
→ Harus redirect ke /admin/dashboard (belum ada controller = 404, itu normal)

TEST 2 — Login Peserta:
→ Buka /login
→ NIK: 3374010101990001
→ Harus redirect ke /peserta/dashboard (belum ada controller = 404, itu normal)

TEST 3 — NIK Salah:
→ Buka /login
→ NIK: 9999999999999999 (tidak ada)
→ Harus muncul error: "NIK tidak terdaftar dalam sistem."

TEST 4 — Cross-access:
→ Login sebagai peserta → coba buka /admin/dashboard → harus redirect ke /peserta/dashboard
```

### **[CHECKPOINT 4]** — Dua jalur login berfungsi dengan benar → Konsultasi Claude sebelum lanjut jika ada masalah → Lanjut ke Fase 5

---

## FASE 5 — Service Layer (Logika Bisnis Inti)

> Fase ini adalah jantung sistem. Jangan skip atau gabung chunk. Setelah selesai, **wajib konsultasi Claude** sebelum lanjut ke controller.

### [AI AGENT] Prompt Chunk 5.1 — PeriodService

```
Kamu adalah Laravel 13 expert.
Buat: app/Services/PeriodService.php

METHOD 1: generateFirstPeriod(Participant $participant): AttendancePeriod

Logic:
- period_start = $participant->supervision_start
- Jika quota_type = 'weekly': period_end = period_start + 6 hari (7 hari total)
- Jika quota_type = 'monthly': period_end = period_start + 29 hari (30 hari total)
- Jika period_end > supervision_end: period_end = supervision_end (potong di batas pengawasan)
- quota_target = $participant->quota_amount (snapshot)
- Simpan dan return AttendancePeriod baru

METHOD 2: generateNextPeriod(AttendancePeriod $currentPeriod): ?AttendancePeriod

Logic:
- Ambil participant dari currentPeriod
- next_start = currentPeriod->period_end + 1 hari
- Jika next_start > participant->supervision_end → return null (pengawasan selesai)
- Jika quota_type = 'weekly': next_end = next_start + 6 hari
- Jika quota_type = 'monthly': next_end = next_start + 29 hari
- Jika next_end > supervision_end: next_end = supervision_end
- Simpan dan return AttendancePeriod baru

METHOD 3: getCurrentPeriod(Participant $participant): ?AttendancePeriod

Logic:
return $participant->attendancePeriods()
    ->where('period_start', '<=', today())
    ->where('period_end', '>=', today())
    ->first();

METHOD 4: generatePeriodsForAllActive(): void

Logic:
- Ambil semua AttendancePeriod yang period_end = yesterday (kemarin)
- Untuk setiap periode, panggil generateNextPeriod()
- Log hasilnya (berapa periode yang di-generate)

Ini akan dipanggil oleh scheduler command setiap malam.

Setelah PeriodService selesai, UPDATE ParticipantSeeder:
- Uncomment bagian TODO di akhir seeder
- Panggil generateFirstPeriod() untuk semua peserta dummy
- Jalankan: php artisan db:seed --class=ParticipantSeeder --force
  (jika data sudah ada, truncate dulu atau gunakan fresh migration)
```

### [AI AGENT] Prompt Chunk 5.2 — AttendanceService

```
Kamu adalah Laravel 13 expert.
Buat: app/Services/AttendanceService.php

METHOD 1: haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float

Implementasi Haversine formula. Return jarak dalam METER.
Radius bumi = 6371000 meter.

Formula:
$dLat = deg2rad($lat2 - $lat1);
$dLng = deg2rad($lng2 - $lng1);
$a = sin($dLat/2) * sin($dLat/2) +
     cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
     sin($dLng/2) * sin($dLng/2);
$c = 2 * atan2(sqrt($a), sqrt(1-$a));
return 6371000 * $c;

METHOD 2: findNearestActiveLocation(float $lat, float $lng): array

Logic:
- Ambil semua Location where is_active = true
- Jika tidak ada lokasi aktif → return ['location' => null, 'distance' => null, 'within_radius' => false]
- Untuk setiap lokasi, hitung haversineDistance()
- Temukan lokasi dengan jarak terdekat
- Return:
  [
      'location' => $nearestLocation,
      'distance' => $minDistance,  // dalam meter
      'within_radius' => $minDistance <= $nearestLocation->radius_meters,
  ]

METHOD 3: validateAbsence(Participant $participant, ?float $lat, ?float $lng, ?float $accuracy): array

Return structure:
[
    'valid' => bool,
    'error_message' => ?string,  // null jika valid
    'error_code' => ?string,     // kode singkat untuk logging
    'location' => ?Location,
    'distance' => ?float,
]

Urutan pengecekan (HARUS berurutan, stop di cek pertama yang gagal):

CEK 1 — Masa pengawasan aktif:
if (!$participant->isActive()) {
    return invalid('Masa pengawasan Anda sudah berakhir.', 'SUPERVISION_ENDED');
}

CEK 2 — Belum absen hari ini:
if ($participant->hasAbsentToday()) {
    return invalid('Anda sudah melakukan absensi hari ini.', 'ALREADY_ABSENT');
}

CEK 3 — Kuota periode belum penuh:
$currentPeriod = (new PeriodService())->getCurrentPeriod($participant);
if (!$currentPeriod || $currentPeriod->isCompleted()) {
    return invalid('Target kehadiran periode ini sudah terpenuhi.', 'QUOTA_FULL');
}

CEK 4 — Batas percobaan harian:
$attemptsToday = AttendanceAttempt::where('participant_id', $participant->id)
    ->whereDate('attempted_at', today())
    ->count();
if ($attemptsToday >= 10) {
    return invalid('Terlalu banyak percobaan. Coba lagi besok.', 'MAX_ATTEMPTS');
}

CEK 5 — Koordinat GPS ada:
if (is_null($lat) || is_null($lng)) {
    return invalid('Gagal mendapatkan lokasi GPS. Aktifkan GPS dan izinkan akses lokasi.', 'NO_GPS');
}

CEK 6 — Akurasi GPS cukup:
if (!is_null($accuracy) && $accuracy > 500) {
    return invalid('Sinyal GPS terlalu lemah (akurasi ' . round($accuracy) . 'm). Pindah ke area terbuka.', 'GPS_ACCURACY');
}

CEK 7 — Dalam radius lokasi resmi:
$locationResult = $this->findNearestActiveLocation($lat, $lng);
if (!$locationResult['within_radius']) {
    $distance = $locationResult['distance'] ? round($locationResult['distance']) . 'm' : 'tidak diketahui';
    return [
        'valid' => false,
        'error_message' => 'Anda berada di luar area wajib lapor (' . $distance . ' dari lokasi terdekat).',
        'error_code' => 'OUT_OF_RANGE',
        'location' => $locationResult['location'],
        'distance' => $locationResult['distance'],
    ];
}

return [
    'valid' => true,
    'error_message' => null,
    'error_code' => null,
    'location' => $locationResult['location'],
    'distance' => $locationResult['distance'],
];

METHOD 4: recordAttempt(Participant $participant, ?float $lat, ?float $lng, ?float $accuracy, string $rejectionCode, ?Location $nearestLocation, ?float $distance): AttendanceAttempt

Simpan ke attendance_attempts. Return AtendanceAttempt baru.

METHOD 5: recordAttendance(Participant $participant, Location $location, AttendancePeriod $period, float $lat, float $lng, float $accuracy, string $photoPath): AttendanceLog

Simpan ke attendance_logs dengan attended_at = today(). Return AttendanceLog baru.

Buat private helper method invalid() untuk mempersingkat return invalid di atas:
private function invalid(string $message, string $code, ?Location $loc = null, ?float $dist = null): array {
    return ['valid' => false, 'error_message' => $message, 'error_code' => $code, 'location' => $loc, 'distance' => $dist];
}
```

### [AI AGENT] Prompt Chunk 5.3 — WarningService

```
Kamu adalah Laravel 13 expert.
Buat: app/Services/WarningService.php

METHOD 1: checkAndGenerateWarnings(): int

Logic:
- Ambil semua Participant yang supervision_end >= today (masih dalam pengawasan)
- Untuk setiap peserta, panggil checkParticipantWarning()
- Return total jumlah warning yang dibuat

METHOD 2: checkParticipantWarning(Participant $participant): void

Gunakan PeriodService untuk getCurrent dan getRecentPeriods.

--- Cek Level 1 ---
$currentPeriod = (new PeriodService())->getCurrentPeriod($participant);
if ($currentPeriod) {
    $remainingDays = $currentPeriod->getRemainingDays();
    $remainingCount = $currentPeriod->getRemainingCount();
    
    if ($remainingDays <= 3 && $remainingCount > 0) {
        // Cek apakah sudah ada warning level 1 aktif untuk periode ini
        $exists = Warning::where('participant_id', $participant->id)
            ->where('triggered_period_id', $currentPeriod->id)
            ->where('level', 1)
            ->where('is_resolved', false)
            ->exists();
        
        if (!$exists) {
            Warning::create([
                'participant_id' => $participant->id,
                'triggered_period_id' => $currentPeriod->id,
                'level' => 1,
                'message' => "Periode wajib lapor akan berakhir dalam {$remainingDays} hari. Masih ada {$remainingCount} kehadiran yang harus dipenuhi.",
                'is_resolved' => false,
            ]);
        }
    }
}

--- Cek Level 2 ---
// Ambil periode yang baru berakhir kemarin
$recentEndedPeriod = $participant->attendancePeriods()
    ->where('period_end', today()->subDay())
    ->first();

if ($recentEndedPeriod && !$recentEndedPeriod->isCompleted()) {
    $exists = Warning::where('participant_id', $participant->id)
        ->where('triggered_period_id', $recentEndedPeriod->id)
        ->where('level', 2)
        ->exists();
    
    if (!$exists) {
        $missing = $recentEndedPeriod->getRemainingCount();
        Warning::create([
            'participant_id' => $participant->id,
            'triggered_period_id' => $recentEndedPeriod->id,
            'level' => 2,
            'message' => "Periode wajib lapor telah berakhir dengan {$missing} kehadiran yang tidak terpenuhi.",
            'is_resolved' => false,
        ]);
        
        // Kirim email level 2
        $this->sendWarningEmail($participant, 2);
    }
}

--- Cek Level 3 ---
$level2UnresolvedCount = Warning::where('participant_id', $participant->id)
    ->where('level', 2)
    ->where('is_resolved', false)
    ->count();

if ($level2UnresolvedCount >= 2) {
    $exists = Warning::where('participant_id', $participant->id)
        ->where('level', 3)
        ->where('is_resolved', false)
        ->exists();
    
    if (!$exists) {
        Warning::create([
            'participant_id' => $participant->id,
            'triggered_period_id' => $participant->attendancePeriods()->latest()->first()->id,
            'level' => 3,
            'message' => "Peserta telah mangkir pada 2 periode berturut-turut. Peserta wajib hadir langsung ke Polres.",
            'is_resolved' => false,
        ]);
        
        $this->sendWarningEmail($participant, 3);
    }
}

METHOD 3: sendWarningEmail(Participant $participant, int $level): void

Jika level = 2:
- Ambil assigned admin ($participant->assignedAdmin)
- Kirim Mail::to($assignedAdmin->email)->send(new WarningNotificationMail($participant, $level))

Jika level = 3:
- Ambil semua admin: User::where('role', 'admin')->get()
- Kirim ke semua: Mail::to($admins)->send(new WarningNotificationMail($participant, $level))

Buat juga Mailable: app/Mail/WarningNotificationMail.php
- Constructor menerima: Participant $participant, int $level
- Subject: "Peringatan Level {$level} - {$participant->name} | Sistem Wajib Lapor"
- View: resources/views/emails/warning-notification.blade.php
- Data yang dikirim ke view: nama peserta, NIK, jenis pelanggaran, level peringatan, pesan warning, link ke admin panel

Buat juga view email: resources/views/emails/warning-notification.blade.php
- Template HTML email sederhana
- Tampilkan semua data yang relevan
- Sertakan tombol/link "Lihat Detail di Panel Admin" → mengarah ke /admin/participants/{id}
```

### **[CHECKPOINT 5]** — WAJIB konsultasi Claude. Tempel kode PeriodService dan AttendanceService untuk direview sebelum lanjut. Ini logika bisnis paling kritis.

---

## FASE 6 — Admin Controllers & Views

### [AI AGENT] Prompt Chunk 6.1 — Layout Admin & Dashboard

```
Kamu adalah Laravel 13 expert dengan Tailwind CSS dan Alpine.js.

BAGIAN 1: Layout Admin (resources/views/layouts/admin.blade.php)

Struktur layout:
- Sidebar kiri (fixed, lebar 256px di desktop)
  * Logo + nama sistem
  * Menu navigasi: Dashboard, Peserta, Lokasi, Laporan
  * Di mobile: sidebar tersembunyi, bisa toggle dengan Alpine.js
- Konten utama (margin-left mengakomodasi sidebar)
  * Header bar atas: nama halaman (breadcrumb) + nama admin yang login + tombol logout
  * @yield('content') untuk konten halaman
- Flash messages (success/error) muncul di atas konten, auto-dismiss 4 detik via Alpine.js

BAGIAN 2: DashboardController (app/Http/Controllers/Admin/DashboardController.php)

Method index():
Hitung dan pass ke view:
- $totalActive: Participant yang supervision_end >= today()
- $totalCompliant: peserta yang current period sudah completed (isCompleted() = true)
- $totalAtRisk: peserta yang current period sisa hari <= 3 DAN sisa kewajiban > 0
- $totalAbsent: peserta yang ada warning level 2 is_resolved=false
- $endingSoon: peserta yang supervision_end antara today dan today+7 hari
- $recentParticipants: 5 peserta terbaru dengan status kepatuhan

BAGIAN 3: View Dashboard (resources/views/admin/dashboard.blade.php)

@extends('layouts.admin')

Konten:
- 5 card statistik dalam grid 5 kolom (responsive)
  * Total Peserta Aktif (biru)
  * Peserta Patuh (hijau)
  * Peserta Berisiko (kuning)
  * Peserta Mangkir (merah)
  * Selesai dalam 7 Hari (abu-abu)
- Tabel "Peserta Terbaru" dengan kolom:
  Nama | NIK | Pelanggaran | Status Pengawasan | Kepatuhan Periode Ini | Aksi (Lihat Detail)

Tambahkan route di routes/web.php dalam group admin.
```

### [AI AGENT] Prompt Chunk 6.2 — Manajemen Peserta (CRUD)

```
Kamu adalah Laravel 13 expert.
Buat Admin\ParticipantController dengan full CRUD.

METHOD index():
- Ambil semua participant dengan eager load: user, assignedAdmin, currentPeriod (via scope atau accessor)
- Filter berdasarkan query string: status (aktif/selesai), violation_type, search (nama/NIK)
- Paginate 15 per halaman
- Return view admin.participants.index

METHOD create():
- Ambil semua admin untuk dropdown assigned_admin
- Ambil Location::active() untuk dropdown lokasi wajib lapor
- Return view admin.participants.create

METHOD store(Request $request):
Validasi:
- name: required|string|max:255
- nik: required|digits:16|unique:users,nik
- address: nullable|string
- phone: nullable|string|max:20
- violation_type: required|string
- case_notes: nullable|string
- supervision_start: required|date|after_or_equal:today
- supervision_end: required|date|after:supervision_start
- quota_type: required|in:weekly,monthly
- quota_amount: required|integer|min:1|max:30
- assigned_admin_id: nullable|exists:users,id

Logic:
1. Buat akun User:
   User::create([
       'name' => $request->name,
       'email' => null,
       'password' => null,
       'nik' => $request->nik,
       'role' => 'peserta',
       'is_active' => true,
   ]);

2. Buat Participant terhubung ke user tersebut

3. Generate periode pertama via PeriodService::generateFirstPeriod()

4. Sync lokasi wajib lapor:
   $participant->locations()->sync($request->location_ids)
   → Jumlah lokasi yang dipilih harus sesuai dengan quota_amount
   → Gunakan tabel pivot participant_location

5. Redirect ke show dengan pesan sukses

METHOD show(Participant $participant):
- Load relasi: user, assignedAdmin, attendanceLogs (dengan location, overriddenBy), 
  attendancePeriods, warnings, attendanceAttempts (10 terbaru)
- Hitung statistik kepatuhan
- Return view admin.participants.show

METHOD edit(Participant $participant):
- Jangan izinkan edit NIK (NIK adalah identifier tetap)
- Ambil Location::active() untuk dropdown lokasi
- Ambil assignedLocationIds = participant->locations->pluck('id')->toArray()
- Return view admin.participants.edit

METHOD update(Request $request, Participant $participant):
- Validasi semua kecuali NIK
- Update participant
- Sync lokasi wajib lapor: $participant->locations()->sync($request->location_ids)
- JANGAN regenerate periode (periode yang sudah berjalan tidak berubah)
- Redirect ke show

METHOD destroy(Participant $participant):
- Bukan hard delete — set user->is_active = false
- Redirect ke index dengan pesan

VIEWS:
admin/participants/index.blade.php:
- Tabel dengan: Nama, NIK, Pelanggaran, Masa Pengawasan, Progress Periode Ini, Status, Aksi
- Filter form di atas tabel
- Tombol "Tambah Peserta Baru"
- Pagination

admin/participants/create.blade.php:
- Form semua field
- Dropdown assigned_admin (nama admin)
- Date picker untuk supervision_start dan supervision_end
- Dynamic dropdown lokasi wajib lapor (Alpine.js):
  → Jumlah dropdown otomatis mengikuti quota_amount
  → Setiap dropdown berisi daftar lokasi aktif
  → Lokasi yang sudah dipilih di dropdown lain otomatis disabled
  → Validasi: location_ids required, array, setiap elemen exists:locations,id, distinct

admin/participants/show.blade.php:
- Card info peserta
- Progress bar kehadiran periode ini
- Tabel riwayat absensi (dengan foto thumbnail, badge "Input Manual" jika override)
- Tabel 10 percobaan ditolak terakhir
- Section "Override Manual Absensi" (form kecil: tanggal + alasan wajib)
- Daftar peringatan aktif dan riwayat peringatan
- Section "Lokasi Wajib Lapor" menampilkan daftar lokasi yang ditetapkan untuk peserta

admin/participants/edit.blade.php:
- Sama dengan create, NIK ditampilkan tapi readonly (tidak bisa diubah)

Tambahkan semua route yang dibutuhkan di route group admin.
Gunakan Route::resource('participants', Admin\ParticipantController::class);
```

### [AI AGENT] Prompt Chunk 6.3 — Override Absensi & Akses Foto

```
Kamu adalah Laravel 13 expert.
Buat Admin\AttendanceController untuk override manual dan akses foto selfie.

METHOD override(Request $request, Participant $participant):
Validasi:
- attended_at: required|date|before_or_equal:today
- override_reason: required|string|min:10

Logic:
1. Cek apakah sudah ada absensi di tanggal tersebut untuk peserta ini
   → Jika sudah ada, return back with error "Peserta sudah absen pada tanggal ini"

2. Cari period yang mencakup tanggal attended_at
   → Jika tidak ada period → return back with error

3. Cek kuota period tersebut belum penuh (getAttendanceCount < quota_target)
   → Jika penuh → return back with error

4. Simpan ke attendance_logs:
   AttendanceLog::create([
       'participant_id' => $participant->id,
       'location_id' => null,
       'period_id' => $period->id,
       'attended_at' => $request->attended_at,
       'latitude' => null,
       'longitude' => null,
       'accuracy_meters' => null,
       'photo_path' => null,
       'is_manual_override' => true,
       'override_reason' => $request->override_reason,
       'override_by' => Auth::id(),
   ]);

5. Redirect back with success

METHOD showPhoto(AttendanceLog $log):
- Cek bahwa log->photo_path ada
- Cek bahwa user yang request adalah admin
- Return response()->file(storage_path('app/private/' . $log->photo_path));
  dengan headers yang tepat untuk gambar

Tambahkan route:
- POST /admin/participants/{participant}/attendance/override → AttendanceController@override → name: admin.attendance.override
- GET /admin/attendance/{log}/photo → AttendanceController@showPhoto → name: admin.attendance.photo
```

### [AI AGENT] Prompt Chunk 6.4 — Manajemen Lokasi

```
Kamu adalah Laravel 13 expert dengan Leaflet.js.
Buat Admin\LocationController dengan CRUD + toggle aktif.

METHOD index():
- Ambil semua lokasi dengan count attendance_logs masing-masing
- Return view admin.locations.index

METHOD create():
- Return view admin.locations.create (dengan peta untuk pilih koordinat)

METHOD store(Request $request):
Validasi:
- name: required|string|max:255
- address: nullable|string
- latitude: required|numeric|between:-90,90
- longitude: required|numeric|between:-180,180
- radius_meters: required|integer|min:50|max:500

METHOD edit(Location $location):
- Return view dengan data lokasi existing

METHOD update(Request $request, Location $location):
- Validasi sama dengan store
- Update dan redirect

METHOD toggle(Location $location):
- Toggle is_active (true → false, false → true)
- Return redirect back with message

VIEWS:
admin/locations/index.blade.php:
- Tabel: Nama, Alamat, Koordinat, Radius, Status (badge), Jumlah Absensi, Aksi (Edit, Toggle)
- Tombol Tambah Lokasi

admin/locations/create.blade.php dan edit.blade.php:
- Form: nama, alamat, radius_meters (slider atau input angka)
- Peta Leaflet.js interaktif:
  * Tampilkan OpenStreetMap
  * Jika create: peta default ke Semarang (-6.9667, 110.4167, zoom 13)
  * Jika edit: peta langsung ke koordinat existing dengan marker terpasang
  * Saat user klik peta → letakkan marker di titik tersebut → update input hidden latitude & longitude
  * Tampilkan circle radius yang berubah real-time sesuai nilai radius_meters yang diinput
  * Tambahkan tombol "Gunakan Lokasi Saya" (browser GPS) untuk deteksi cepat
- Input hidden: latitude, longitude (terisi dari interaksi peta)
- Validasi client-side: tidak bisa submit jika lat/lng belum terisi

Sertakan Leaflet.js via CDN di view ini saja (tidak di semua halaman admin).
Gunakan Alpine.js untuk sinkronisasi nilai slider radius dengan circle di peta.

Tambahkan route:
- Route::resource('locations', Admin\LocationController::class)->except(['show', 'destroy']);
- Route::patch('locations/{location}/toggle', ...)->name('admin.locations.toggle');
```

### [AI AGENT] Prompt Chunk 6.5 — Laporan Kepatuhan

```
Kamu adalah Laravel 13 expert.
Buat Admin\ReportController.

METHOD index():
- Ambil semua peserta dengan statistik kepatuhan:
  * Total periode yang sudah selesai
  * Total kehadiran keseluruhan
  * Persentase kepatuhan = (total hadir / total target) * 100
- Filter: violation_type, status (aktif/selesai)
- Return view admin.reports.index

METHOD show(Participant $participant):
- Load semua attendance_periods dengan attendance_logs
- Hitung statistik per periode
- Return view admin.reports.show

VIEWS:
admin/reports/index.blade.php:
- Tabel: Nama, NIK, Pelanggaran, Status, Total Periode, Total Hadir, Target, Persentase Kepatuhan
- Tombol "Cetak Laporan" (window.print())
- Filter di atas tabel
- CSS @media print: sembunyikan sidebar, header, tombol — tampilkan hanya tabel

admin/reports/show.blade.php:
- Header laporan: nama instansi, nama peserta, NIK, pelanggaran, masa pengawasan
- Tabel per periode: Periode ke-X, Tanggal, Target, Hadir, Status (Patuh/Mangkir)
- Tabel riwayat absensi: Tanggal, Lokasi, Metode (Normal/Manual), Keterangan
- Footer: tempat tanda tangan petugas
- CSS @media print: layout dokumen resmi, sembunyikan elemen UI

Route:
- GET /admin/reports → ReportController@index → name: admin.reports.index
- GET /admin/reports/{participant} → ReportController@show → name: admin.reports.show
```

### **[CHECKPOINT 6]** — Test semua fitur admin di browser. Tambah peserta, lihat detail, coba override, tambah lokasi. Konsultasi Claude jika ada yang tidak sesuai harapan.

---

## FASE 7 — Peserta Controllers & Views

### [AI AGENT] Prompt Chunk 7.1 — Layout Peserta & Dashboard

```
Kamu adalah Laravel 13 expert dengan Tailwind CSS dan Alpine.js.

BAGIAN 1: Layout Peserta (resources/views/layouts/participant.blade.php)

Layout minimalis, mobile-first (peserta kemungkinan akses dari HP):
- Header simpel: nama sistem + nama peserta yang login + tombol logout
- Konten penuh
- Navigation bottom bar (mobile-style): Dashboard, Absensi, Riwayat
- Flash messages untuk feedback

BAGIAN 2: Participant\DashboardController (app/Http/Controllers/Participant/DashboardController.php)

Method index():
- Ambil participant dari auth user: $participant = Auth::user()->participant;
- Jika tidak ada participant → redirect ke /login dengan error (edge case)
- Ambil data:
  * $currentPeriod = PeriodService->getCurrentPeriod($participant)
  * $attendanceCount = $currentPeriod ? $currentPeriod->getAttendanceCount() : 0
  * $remainingCount = $currentPeriod ? $currentPeriod->getRemainingCount() : 0
  * $remainingDays = $participant->getRemainingDays()
  * $hasAbsentToday = $participant->hasAbsentToday()
  * $isActive = $participant->isActive()
  * $quotaFull = $currentPeriod ? $currentPeriod->isCompleted() : false
  * $activeLocations = $participant->locations()->where('is_active', true)->get()
  → PENTING: Hanya tampilkan lokasi yang DITETAPKAN untuk peserta ini, bukan semua lokasi aktif
  * $activeWarnings = $participant->warnings()->active()->latest()->get()
  * $recentLogs = $participant->attendanceLogs()->with('location')->latest()->limit(10)->get()
- Return view('participant.dashboard', compact semua variabel)

BAGIAN 3: View Dashboard Peserta (resources/views/participant/dashboard.blade.php)

@extends('layouts.participant')

Urutan konten dari atas ke bawah:

SECTION 1 — Peringatan (jika ada)
- Jika ada warning level 3: banner merah mencolok + teks "WAJIB HADIR LANGSUNG KE POLRES"
- Jika ada warning level 2: banner merah + pesan mangkir
- Jika ada warning level 1: banner kuning + pesan hampir habis

SECTION 2 — Card Status Peserta
- Nama lengkap (besar)
- Jenis pelanggaran (badge)
- Masa pengawasan: [tanggal mulai] s.d. [tanggal selesai]
- Status: AKTIF (hijau) atau SELESAI MASA PENGAWASAN (abu)
- Sisa hari pengawasan: "X hari lagi" (merah jika < 7 hari)

SECTION 3 — Progress Kehadiran Periode Ini
- Judul: "Periode [tanggal] s.d. [tanggal]"
- Progress bar: X dari Y kehadiran
- Teks: "Masih perlu hadir N kali lagi" atau "Target terpenuhi! ✓"

SECTION 4 — Tombol Absensi (besar, prominent)
Kondisi tombol:
- AKTIF → "Absensi Sekarang" (warna biru/hijau besar)
  Kondisi: isActive && !hasAbsentToday && !quotaFull
- DISABLED + pesan jika:
  * !isActive → "Masa pengawasan sudah selesai"
  * hasAbsentToday → "Sudah absen hari ini ✓"
  * quotaFull → "Target periode ini sudah terpenuhi ✓"

SECTION 5 — Peta Lokasi Wajib Lapor
- Leaflet.js, tinggi 300px
- Tampilkan marker + circle radius untuk setiap location aktif
- Popup info saat klik marker: nama lokasi, alamat, radius
- Map default center ke rata-rata koordinat semua lokasi

SECTION 6 — Riwayat Absensi (10 terakhir)
- Tabel: Tanggal, Lokasi, Keterangan (Normal / Input Manual)
- Badge berbeda warna untuk Input Manual
```

### [AI AGENT] Prompt Chunk 7.2 — Halaman Absensi

```
Kamu adalah Laravel 13 expert dengan Alpine.js dan Tailwind CSS.

BAGIAN 1: Participant\AbsenceController

Method show():
- Cek $participant = Auth::user()->participant
- Validasi pre-conditions (redirect dengan pesan jika gagal):
  * !$participant->isActive() → "Masa pengawasan sudah berakhir"
  * $participant->hasAbsentToday() → "Sudah absen hari ini"
  * getCurrentPeriod()->isCompleted() → "Target periode sudah terpenuhi"
- Ambil semua lokasi aktif untuk ditampilkan di peta
- Return view participant.absence

Method store(Request $request):

Validasi request:
- latitude: nullable|numeric
- longitude: nullable|numeric
- accuracy: nullable|numeric
- photo: required|file|mimes:jpeg,jpg,png|max:5120

Logic:
1. Ambil peserta dari Auth
2. Konversi request lat/lng/accuracy ke float atau null
3. Jalankan AttendanceService->validateAbsence($participant, $lat, $lng, $accuracy)

4. Jika tidak valid:
   a. Simpan attempt via AttendanceService->recordAttempt(...)
   b. Return back()->withErrors(['attendance' => $result['error_message']])

5. Jika valid:
   a. Simpan foto:
      $path = $request->file('photo')->store(
          'selfies/' . $participant->id . '/' . today()->format('Y/m'),
          'private'
      );
   
   b. Ambil current period
   
   c. Simpan absensi:
      AttendanceService->recordAttendance(
          $participant,
          $result['location'],
          $currentPeriod,
          $lat, $lng, $accuracy,
          $path
      );
   
   d. Redirect ke /peserta/dashboard dengan success message "Absensi berhasil dicatat!"

BAGIAN 2: View Absensi (resources/views/participant/absence.blade.php)

@extends('layouts.participant')

Gunakan Alpine.js dengan x-data untuk state management:
x-data="{
    gpsStatus: 'idle',       // idle | loading | success | error
    lat: null,
    lng: null,
    accuracy: null,
    photoTaken: false,
    photoPreview: null,
    cooldown: false,
    cooldownSeconds: 0,
    canSubmit() { return this.gpsStatus === 'success' && this.photoTaken && !this.cooldown; }
}"

SECTION 1 — Info Singkat
- Sisa kewajiban hari ini
- Lokasi resmi yang bisa dikunjungi (list nama + jarak jika GPS sudah aktif)

SECTION 2 — Deteksi Lokasi GPS
- Tombol "Deteksi Lokasi Saya" 
  * Saat klik: gpsStatus = 'loading', jalankan navigator.geolocation.getCurrentPosition()
  * Berhasil: gpsStatus = 'success', simpan lat/lng/accuracy ke Alpine state
  * Gagal: gpsStatus = 'error'
- Tampilkan status GPS:
  * idle: "Klik tombol untuk mendeteksi lokasi"
  * loading: spinner + "Mendeteksi lokasi..."
  * success: ✓ "Lokasi berhasil dideteksi. Akurasi: Xm"
  * error: ✗ "Gagal deteksi GPS. Pastikan GPS aktif dan izin diberikan."
- Input hidden: name="latitude", name="longitude", name="accuracy" (diisi via Alpine x-model atau @change)

SECTION 3 — Foto Selfie
- Label: "Ambil Foto Selfie"
- Input: type="file" name="photo" accept="image/*" capture="camera"
  * Sembunyikan input asli, gunakan tombol custom
  * Saat file dipilih: tampilkan preview gambar, set photoTaken = true
- Area preview foto (tersembunyi sampai foto diambil)
- Teks: "Foto harus diambil langsung dari kamera, bukan dari galeri"

SECTION 4 — Tombol Submit
- Button submit besar
- Disabled dan warna abu-abu jika canSubmit() = false
- Tampilkan pesan kenapa disabled (GPS belum aktif / foto belum diambil)

SECTION 5 — Error Messages
- Tampilkan @error('attendance') jika ada error dari server validasi

Logika cooldown (client-side):
- Setelah GPS error karena lokasi (bukan error GPS device), mulai countdown 120 detik
- Tombol submit disabled selama countdown
- Tampilkan "Coba lagi dalam Xs"
- Ini hanya UI — rate limiting sesungguhnya ada di server

PENTING: Form ini menggunakan enctype="multipart/form-data" karena ada upload file.
```

### [AI AGENT] Prompt Chunk 7.3 — Riwayat Absensi Peserta

```
Kamu adalah Laravel 13 expert.
Buat Participant\HistoryController.

Method index():
- Ambil participant dari auth user
- Ambil SEMUA attendance_logs dengan eager load location
- Group by period (join dengan attendance_periods)
- Return view participant.history

View resources/views/participant/history.blade.php:
- Tampilkan riwayat dikelompokkan per periode
- Header setiap grup: "Periode X: [tanggal] s.d. [tanggal] — X/Y hadir"
- Tabel dalam setiap grup: Tanggal, Hari, Lokasi, Status
- Badge "Input Manual" untuk override
- Warna baris berbeda untuk yang manual
- Scroll ke periode terbaru secara default

Route: GET /peserta/riwayat → HistoryController@index → name: peserta.history
```

### **[CHECKPOINT 7]** — Test end-to-end alur peserta: login NIK → dashboard → buka halaman absensi → coba submit tanpa GPS → coba submit dari luar radius → coba submit valid. Konsultasi Claude jika ada yang tidak sesuai.

---

## FASE 8 — Scheduler & Background Jobs

### [AI AGENT] Prompt Chunk 8.1 — Artisan Commands

```
Kamu adalah Laravel 13 expert.

COMMAND 1: app/Console/Commands/GenerateNextPeriods.php
Signature: periods:generate-next
Description: "Generate periode berikutnya untuk peserta yang periodenya baru selesai"

Handle:
- Panggil PeriodService->generatePeriodsForAllActive()
- Log hasilnya ke console: "Generated X new periods"

COMMAND 2: app/Console/Commands/CheckAttendanceWarnings.php
Signature: attendance:check-warnings
Description: "Cek kepatuhan peserta dan generate peringatan jika perlu"

Handle:
- Panggil WarningService->checkAndGenerateWarnings()
- Log hasilnya: "Generated X warnings"

SCHEDULE (routes/console.php — Laravel 13 style):

use Illuminate\Support\Facades\Schedule;

Schedule::command('periods:generate-next')->dailyAt('00:05');
Schedule::command('attendance:check-warnings')->dailyAt('08:00');

Untuk menjalankan scheduler di local development, tambahkan ke README:
php artisan schedule:work
atau jalankan manual:
php artisan periods:generate-next
php artisan attendance:check-warnings
```

### [KAMU] Test Manual Commands

```bash
php artisan periods:generate-next
# Output: "Generated X new periods"

php artisan attendance:check-warnings
# Output: "Generated X warnings"

# Verifikasi di database via HeidiSQL:
# Cek tabel attendance_periods — apakah ada periode baru
# Cek tabel warnings — apakah ada warning baru
```

### **[CHECKPOINT 8]** — Kedua command berjalan tanpa error → Lanjut ke Fase 9

---

## FASE 9 — Polish & UX Improvements

### [AI AGENT] Prompt Chunk 9.1 — Flash Messages & UX Polish

```
Kamu adalah Laravel 13 expert.
Lakukan UX improvement berikut. Jangan ubah business logic — hanya tampilan dan interaksi.

1. FLASH MESSAGE COMPONENT
   Buat partial: resources/views/components/flash-message.blade.php
   - Tampilkan session('success') → kotak hijau
   - Tampilkan session('error') → kotak merah
   - Tampilkan session('warning') → kotak kuning
   - Auto-dismiss setelah 4 detik via Alpine.js
   - X button untuk close manual
   Include di kedua layout (admin dan participant)

2. KONFIRMASI SEBELUM AKSI BERBAHAYA
   Tambahkan konfirmasi via Alpine.js modal sederhana sebelum:
   - Nonaktifkan peserta (bukan hapus)
   - Toggle nonaktifkan lokasi
   Jangan gunakan native browser confirm() — buat modal Tailwind sendiri

3. LOADING STATE
   - Tombol "Absensi Sekarang" di halaman absensi: tambahkan spinner dan disabled state saat form disubmit
   - Tombol "Deteksi Lokasi": tambahkan loading state selama GPS bekerja

4. HALAMAN ERROR CUSTOM
   Buat: resources/views/errors/403.blade.php dan 404.blade.php
   - Tampilan sederhana tapi sesuai tema sistem
   - Pesan yang jelas + tombol kembali

5. PAGINATION KONSISTEN
   Pastikan semua tabel di admin yang bisa memiliki banyak data menggunakan:
   ->paginate(15)
   Dan tampilkan links pagination di bawah tabel

6. RESPONSIVE CHECK
   - Sidebar admin: collapse otomatis di layar < 768px
   - Tabel: horizontal scroll di mobile
   - Halaman absensi peserta: optimal di layar mobile (min-width: 320px)

7. BREADCRUMB
   Tambahkan breadcrumb sederhana di halaman admin:
   Dashboard > Peserta > Budi Santoso > Detail
   Buat component breadcrumb yang bisa diisi via @section
```

### [AI AGENT] Prompt Chunk 9.2 — Rate Limiting & Keamanan Final

```
Kamu adalah Laravel 13 expert.
Tambahkan lapisan keamanan final:

1. RATE LIMITING LOGIN PESERTA
   Di PesertaAuthController, tambahkan rate limiting:
   - Gunakan RateLimiter facade
   - Max 5 percobaan per IP per 10 menit
   - Jika melebihi: return back() dengan error "Terlalu banyak percobaan. Coba lagi dalam X menit."
   - Sama persis dengan yang dilakukan Breeze untuk admin login

2. RATE LIMITING ABSENSI
   Di route peserta absensi:
   Route::post('/absensi', ...)->middleware('throttle:absensi');
   
   Daftarkan di AppServiceProvider atau RouteServiceProvider:
   RateLimiter::for('absensi', function (Request $request) {
       return Limit::perDay(10)->by($request->user()?->id ?: $request->ip());
   });

3. SESSION TIMEOUT
   Di config/session.php:
   'lifetime' => 30, // 30 menit
   'expire_on_close' => false,

4. VALIDASI OWNERSHIP
   Di semua controller peserta, pastikan peserta hanya bisa akses datanya sendiri:
   $participant = Auth::user()->participant;
   // Jangan gunakan route parameter untuk peserta — selalu ambil dari auth user

5. FOTO SELFIE — PASTIKAN PRIVATE
   Verifikasi config/filesystems.php disk 'private':
   'private' => [
       'driver' => 'local',
       'root' => storage_path('app/private'),
       'visibility' => 'private',
   ],
   
   Pastikan route showPhoto hanya bisa diakses admin (sudah dilindungi middleware role:admin).
```

### **[CHECKPOINT 9 — FINAL]** — Konsultasi Claude untuk evaluasi keseluruhan sebelum testing akhir.

---

## FASE 10 — Testing Akhir (Manual)

### [KAMU] Checklist Testing Lengkap

**Auth & Access Control:**
- [ ] Login admin via /admin/login (email+password) → redirect /admin/dashboard
- [ ] Login peserta via /login (NIK saja) → redirect /peserta/dashboard
- [ ] NIK salah → pesan error yang tepat
- [ ] Peserta coba akses /admin/* → redirect ke /peserta/dashboard
- [ ] Admin coba akses /peserta/* → redirect ke /admin/dashboard
- [ ] Logout admin → redirect /admin/login
- [ ] Logout peserta → redirect /login
- [ ] 5+ percobaan login gagal → rate limit aktif

**Alur Admin:**
- [ ] Tambah peserta baru dengan lokasi wajib lapor → akun + periode + lokasi terbuat
- [ ] Login dengan NIK peserta baru → berhasil
- [ ] Lihat detail peserta → semua data tampil benar + section "Lokasi Wajib Lapor" muncul
- [ ] Edit data peserta → tersimpan, periode existing tidak berubah, lokasi ter-update
- [ ] Ubah quota_amount → jumlah dropdown lokasi otomatis menyesuaikan
- [ ] Tambah lokasi dengan klik peta → koordinat terisi → circle radius tampil
- [ ] Toggle nonaktifkan lokasi → konfirmasi modal muncul → lokasi nonaktif
- [ ] Override manual absensi → muncul di riwayat dengan badge "Input Manual"
- [ ] Tanpa alasan override → validasi error muncul
- [ ] Cetak laporan → tampilan print-ready

**Alur Peserta:**
- [ ] Dashboard menampilkan info yang benar (nama, status, progress)
- [ ] Dashboard hanya menampilkan lokasi yang ditetapkan, BUKAN semua lokasi aktif
- [ ] Peta di dashboard hanya menunjukkan lokasi yang ditetapkan untuk peserta ini
- [ ] Tombol absensi disabled jika sudah absen hari ini
- [ ] Tombol absensi disabled jika kuota penuh
- [ ] Halaman absensi: tombol submit disabled sebelum GPS + foto
- [ ] GPS berhasil → koordinat tampil + status "berhasil"
- [ ] GPS di luar radius lokasi yang ditetapkan → error "di luar area lokasi wajib lapor yang ditetapkan" → percobaan tercatat di DB
- [ ] GPS dalam radius lokasi yang ditetapkan + foto valid → absensi berhasil → redirect dashboard dengan pesan sukses
- [ ] Foto dari galeri (bukan kamera langsung) → cek apakah bisa dicegah di mobile
- [ ] Riwayat absensi tampil benar

**Scheduler:**
- [ ] `php artisan periods:generate-next` → tidak error, output log
- [ ] `php artisan attendance:check-warnings` → tidak error, output log
- [ ] Modifikasi manual data DB untuk trigger warning → run command → warning terbuat

**Edge Cases:**
- [ ] Peserta coba absen setelah masa pengawasan selesai → ditolak dengan pesan tepat
- [ ] Peserta coba absen hari yang sama dua kali → ditolak
- [ ] Akurasi GPS 600m → ditolak karena terlalu lemah
- [ ] Upload file bukan gambar → validasi error

---

## Referensi: Kapan Harus Kembali ke Claude

| Situasi | Tindakan |
|---|---|
| Logic service tidak sesuai ekspektasi | Tempel kode ke Claude, minta review |
| AI Agent keluar jalur / hasil tidak terkontrol | Stop, reset chunk, konsultasi Claude |
| Mau tambah fitur baru | Diskusikan scope dulu ke Claude |
| Bug yang tidak kunjung solved | Tempel error + kode ke Claude |
| Selesai Checkpoint 5 (Service Layer) | WAJIB review ke Claude sebelum lanjut |
| Selesai Checkpoint 7 (Alur peserta) | Review ke Claude sebelum lanjut |
| Hendak masuk ke Phase 2 (scheduler) | Brief ke Claude tentang hasil Phase 1 |

---

## Ringkasan Urutan File yang Dibuat

```
Fase 0  → .env, config/app.php, routes/auth.php (modifikasi Breeze)
Fase 1  → 8 migration files (berurutan) + 1 pivot migration (participant_location)
Fase 2  → 8 model files
Fase 3  → PesertaAuthController, RoleMiddleware, LogActivityMiddleware,
           views/peserta-auth/login.blade.php, routes/web.php (struktur)
Fase 4  → AdminSeeder, LocationSeeder (harus sebelum Participant), ParticipantSeeder
           (termasuk sync lokasi), DatabaseSeeder
Fase 5  → PeriodService, AttendanceService, WarningService, WarningNotificationMail
Fase 6  → Admin controllers (Dashboard, Participant, Attendance, Location, Report)
           + semua views admin
Fase 7  → Participant controllers (Dashboard, Absence, History)
           + semua views peserta + layout participant
Fase 8  → 2 Artisan commands, routes/console.php
Fase 9  → Flash message component, UX polish, keamanan final
```

---

*Dokumen living — update jika ada keputusan desain yang berubah selama pengembangan.*
