<?php

namespace Database\Seeders;

use App\Models\Diagnosis;
use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\Poli;
use App\Models\RekamMedis;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\TandaVital;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KlinikSeeder extends Seeder
{
    public function run(): void
    {
        $resepsionis = User::firstOrCreate(
            ['email' => 'resepsionis@klinik.com'],
            ['name' => 'Resepsionis', 'password' => Hash::make('password'), 'role' => 'resepsionis', 'email_verified_at' => now()],
        );
        $resepsionis->update(['role' => 'resepsionis']);

        $dokterUser = User::firstOrCreate(
            ['email' => 'dokter@klinik.com'],
            ['name' => 'Dr. Andi', 'password' => Hash::make('password'), 'role' => 'dokter', 'email_verified_at' => now()],
        );
        $dokterUser->update(['role' => 'dokter']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@klinik.com'],
            ['name' => 'Admin Klinik', 'password' => Hash::make('password'), 'role' => 'admin', 'email_verified_at' => now()],
        );
        $admin->update(['role' => 'admin']);

        $poliUmum = Poli::firstOrCreate(['kode' => 'UMUM'], ['nama' => 'Poli Umum', 'deskripsi' => 'Layanan umum', 'is_aktif' => true]);
        $poliGigi = Poli::firstOrCreate(['kode' => 'GIGI'], ['nama' => 'Poli Gigi', 'deskripsi' => 'Kesehatan gigi dan mulut', 'is_aktif' => true]);
        $poliKia = Poli::firstOrCreate(['kode' => 'KIA'], ['nama' => 'Poli KIA', 'deskripsi' => 'Kesehatan ibu dan anak', 'is_aktif' => true]);

        $dokters = collect([
            ['nama' => 'Dr. Andi Wijaya', 'user_id' => $dokterUser->id, 'poli_id' => $poliUmum->id, 'spesialisasi' => 'Dokter Umum'],
            ['nama' => 'Dr. Siti Rahma', 'user_id' => null, 'poli_id' => $poliUmum->id, 'spesialisasi' => 'Dokter Umum'],
            ['nama' => 'Drg. Budi Hartono', 'user_id' => null, 'poli_id' => $poliGigi->id, 'spesialisasi' => 'Dokter Gigi'],
            ['nama' => 'Dr. Maya Kusuma', 'user_id' => null, 'poli_id' => $poliKia->id, 'spesialisasi' => 'Bidan / KIA'],
        ])->map(fn (array $d) => Dokter::firstOrCreate(['nama' => $d['nama']], $d));

        $obats = collect([
            ['kode' => 'OBT001', 'nama_generik' => 'Paracetamol', 'nama_dagang' => 'Sanmol', 'satuan' => 'tablet', 'stok' => 500, 'harga' => 500],
            ['kode' => 'OBT002', 'nama_generik' => 'Amoxicillin', 'nama_dagang' => 'Amoxsan', 'satuan' => 'kapsul', 'stok' => 300, 'harga' => 1200],
            ['kode' => 'OBT003', 'nama_generik' => 'Cetirizine', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 200, 'harga' => 800],
            ['kode' => 'OBT004', 'nama_generik' => 'Omeprazole', 'nama_dagang' => null, 'satuan' => 'kapsul', 'stok' => 150, 'harga' => 2500],
            ['kode' => 'OBT005', 'nama_generik' => 'Ibuprofen', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 250, 'harga' => 700],
            ['kode' => 'OBT006', 'nama_generik' => 'Metformin', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 180, 'harga' => 900],
            ['kode' => 'OBT007', 'nama_generik' => 'Amlodipine', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 220, 'harga' => 1100],
            ['kode' => 'OBT008', 'nama_generik' => 'Salbutamol', 'nama_dagang' => 'Ventolin', 'satuan' => 'tablet', 'stok' => 120, 'harga' => 1500],
            ['kode' => 'OBT009', 'nama_generik' => 'Cotrimoxazole', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 160, 'harga' => 600],
            ['kode' => 'OBT010', 'nama_generik' => 'Vitamin C', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 400, 'harga' => 400],
            ['kode' => 'OBT011', 'nama_generik' => 'Antasida DOEN', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 300, 'harga' => 350],
            ['kode' => 'OBT012', 'nama_generik' => 'Chlorpheniramine', 'nama_dagang' => 'CTM', 'satuan' => 'tablet', 'stok' => 280, 'harga' => 300],
            ['kode' => 'OBT013', 'nama_generik' => 'Dexamethasone', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 100, 'harga' => 800],
            ['kode' => 'OBT014', 'nama_generik' => 'Captopril', 'nama_dagang' => null, 'satuan' => 'tablet', 'stok' => 140, 'harga' => 950],
            ['kode' => 'OBT015', 'nama_generik' => 'Asam Mefenamat', 'nama_dagang' => null, 'satuan' => 'kapsul', 'stok' => 200, 'harga' => 650],
        ])->map(fn (array $o) => Obat::firstOrCreate(['kode' => $o['kode']], $o + ['stok_minimum' => 20, 'is_aktif' => true]));

        if (Pasien::count() >= 20) {
            return;
        }

        $namaPasien = ['Budi Santoso', 'Siti Aminah', 'Agus Prasetyo', 'Dewi Lestari', 'Rudi Hartono', 'Ani Wijaya', 'Joko Susilo', 'Rina Marlina', 'Eko Saputra', 'Lina Permata', 'Hendra Gunawan', 'Maya Sari', 'Fajar Nugroho', 'Putri Ayu', 'Dedi Hermawan', 'Nurul Huda', 'Yusuf Maulana', 'Sri Wahyuni', 'Bambang Irawan', 'Fitri Handayani'];
        $alergis = [null, 'Amoxicillin', 'Udang', null, null, 'Paracetamol', null, null, 'Debu', null, null, null, 'Kacang', null, null, null, null, 'Ikan laut', null, null];

        $pasiens = collect(range(0, 19))->map(function (int $i) use ($namaPasien, $alergis): Pasien {
            $jk = $i % 2 === 0 ? 'L' : 'P';

            return Pasien::create([
                'no_rm' => sprintf('RM-%s-%04d', now()->format('Y'), $i + 1),
                'nik' => sprintf('3201%012d', 100000000001 + $i),
                'nama' => $namaPasien[$i],
                'tgl_lahir' => fake()->dateTimeBetween('-60 years', '-5 years')->format('Y-m-d'),
                'jk' => $jk,
                'alamat' => fake()->address(),
                'telp' => '08'.fake()->numerify('##########'),
                'gol_darah' => fake()->randomElement(['A', 'B', 'AB', 'O', null]),
                'alergi' => $alergis[$i],
                'penjamin' => fake()->randomElement(['umum', 'umum', 'bpjs', 'asuransi']),
            ]);
        });

        $statuses = ['menunggu', 'dipanggil', 'diperiksa', 'selesai', 'selesai', 'selesai', 'batal'];
        $keluhans = ['Demam dan batuk', 'Sakit gigi', 'Kontrol kehamilan', 'Pusing dan mual', 'Nyeri perut', 'Sesak napas', 'Gatal kulit', 'Diare', 'Hipertensi kontrol', 'Diabetes kontrol', 'ISPA', 'Maag kambuh', 'Alergi kulit', 'Kontrol KB', 'Imunisasi anak'];

        $counter = [];
        foreach (range(0, 29) as $i) {
            $pasien = $pasiens->random();
            $dokter = $dokters->random();
            $tgl = fake()->dateTimeBetween('-14 days', 'now')->format('Y-m-d');
            $key = $dokter->poli->kode.'|'.$tgl;
            $counter[$key] = ($counter[$key] ?? 0) + 1;
            $no = sprintf('%s-%03d', $dokter->poli->kode, $counter[$key]);

            $kunjungan = Kunjungan::create([
                'pasien_id' => $pasien->id,
                'dokter_id' => $dokter->id,
                'poli_id' => $dokter->poli_id,
                'user_id' => $resepsionis->id,
                'tgl_kunjungan' => $tgl,
                'no_antrean' => $no,
                'status' => $statuses[array_rand($statuses)],
                'keluhan_utama' => $keluhans[array_rand($keluhans)],
                'penjamin' => $pasien->penjamin,
            ]);

            TandaVital::create([
                'kunjungan_id' => $kunjungan->id,
                'td_sistolik' => fake()->numberBetween(100, 140),
                'td_diastolik' => fake()->numberBetween(70, 90),
                'nadi' => fake()->numberBetween(60, 100),
                'suhu' => fake()->randomFloat(1, 36, 38),
                'rr' => fake()->numberBetween(16, 24),
                'spo2' => fake()->numberBetween(95, 100),
                'bb' => fake()->randomFloat(1, 45, 85),
                'tb' => fake()->randomFloat(1, 150, 175),
                'dicatat_oleh' => $resepsionis->id,
            ]);

            if (in_array($kunjungan->status, ['diperiksa', 'selesai'], true)) {
                $rm = RekamMedis::create([
                    'kunjungan_id' => $kunjungan->id,
                    'dokter_id' => $dokter->id,
                    'subjective' => $kunjungan->keluhan_utama.'. Durasi '.fake()->numberBetween(1, 7).' hari.',
                    'objective' => 'Keadaan umum baik, kesadaran compos mentis.',
                    'assessment' => 'Diagnosis kerja: '.fake()->randomElement(['ISPA', 'Hipertensi', 'Dyspepsia', 'Dermatitis', 'Faringitis']),
                    'plan' => 'Terapi obat, edukasi, kontrol 3 hari bila tidak membaik.',
                ]);

                Diagnosis::create([
                    'rekam_medis_id' => $rm->id,
                    'kode_icd10' => fake()->randomElement(['J06.9', 'I10', 'K30', 'L30.9', 'J02.9']),
                    'nama_diagnosis' => $rm->assessment,
                    'is_primary' => true,
                ]);

                $resep = Resep::create([
                    'kunjungan_id' => $kunjungan->id,
                    'dokter_id' => $dokter->id,
                    'status' => $kunjungan->status === 'selesai' ? 'diserahkan' : 'draft',
                ]);

                foreach ($obats->random(rand(1, 3)) as $obat) {
                    ResepDetail::create([
                        'resep_id' => $resep->id,
                        'obat_id' => $obat->id,
                        'dosis' => fake()->randomElement(['500 mg', '250 mg', '10 mg']),
                        'frekuensi' => fake()->randomElement(['3x sehari', '2x sehari', '1x sehari']),
                        'jumlah' => fake()->numberBetween(5, 20),
                        'aturan_pakai' => 'Sesudah makan',
                    ]);
                }

                $tagihan = Tagihan::create([
                    'kunjungan_id' => $kunjungan->id,
                    'total' => 0,
                    'diskon' => 0,
                    'total_bayar' => 0,
                    'status' => $kunjungan->status === 'selesai' ? 'lunas' : 'belum',
                ]);

                $details = [
                    ['deskripsi' => 'Jasa konsultasi dokter', 'qty' => 1, 'harga_satuan' => 50000, 'jenis' => 'jasa'],
                    ['deskripsi' => 'Biaya administrasi', 'qty' => 1, 'harga_satuan' => 10000, 'jenis' => 'admin'],
                ];
                foreach ($resep->details as $d) {
                    $details[] = ['deskripsi' => $d->obat->nama_generik, 'qty' => $d->jumlah, 'harga_satuan' => (float) $d->obat->harga, 'jenis' => 'obat'];
                }
                $total = 0;
                foreach ($details as $det) {
                    $subtotal = $det['qty'] * $det['harga_satuan'];
                    $total += $subtotal;
                    TagihanDetail::create(['tagihan_id' => $tagihan->id, 'deskripsi' => $det['deskripsi'], 'qty' => $det['qty'], 'harga_satuan' => $det['harga_satuan'], 'subtotal' => $subtotal, 'jenis' => $det['jenis']]);
                }
                $tagihan->update(['total' => $total, 'total_bayar' => $total]);

                if ($tagihan->status === 'lunas') {
                    Pembayaran::create([
                        'tagihan_id' => $tagihan->id,
                        'metode' => fake()->randomElement(['tunai', 'transfer', 'qris']),
                        'jumlah' => $total,
                        'kasir_id' => $resepsionis->id,
                    ]);
                }
            } elseif ($kunjungan->status !== 'batal') {
                $tagihan = Tagihan::create([
                    'kunjungan_id' => $kunjungan->id,
                    'total' => 60000,
                    'total_bayar' => 60000,
                    'status' => 'belum',
                ]);
                TagihanDetail::create(['tagihan_id' => $tagihan->id, 'deskripsi' => 'Jasa konsultasi dokter', 'qty' => 1, 'harga_satuan' => 50000, 'subtotal' => 50000, 'jenis' => 'jasa']);
                TagihanDetail::create(['tagihan_id' => $tagihan->id, 'deskripsi' => 'Biaya administrasi', 'qty' => 1, 'harga_satuan' => 10000, 'subtotal' => 10000, 'jenis' => 'admin']);
            }
        }
    }
}
