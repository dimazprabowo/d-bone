<?php

namespace Database\Seeders;

use App\Enums\PengadaanApprovalStatus;
use App\Enums\TipeBiaya;
use App\Enums\VendorStatus;
use App\Models\BisnisArea;
use App\Models\Cabang;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\KategoriItem;
use App\Models\Pengadaan;
use App\Models\PengadaanEvidence;
use App\Models\PengadaanItem;
use App\Models\Satuan;
use App\Models\User;
use App\Models\Vendor;
use App\Services\ApprovalService;
use App\Services\FileStorageService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengadaanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@app.com')->first();
        if (! $admin) {
            return;
        }

        $fileStorage = app(FileStorageService::class);
        $disk = file_disk();

        $vendors = Vendor::where('status', VendorStatus::Aktif->value)->get()->keyBy('code');

        $bisnisAreas = BisnisArea::pluck('id', 'name');
        $satuanMap = Satuan::pluck('id', 'code');
        $kategoriMap = KategoriItem::pluck('id', 'name');
        $jakarta = Cabang::where('code', 'JKT')->first();

        // 20 skenario pengadaan menyerupai data asli: kombinasi tipe biaya
        // (Fix Cost / RAB Project tiap Bisnis Area), approval (pending/approved/rejected),
        // invoice & payment (none/partial/multi/lunas), umur invoice (Aman/Proses/Lewat),
        // pengadaan pusat tanpa cabang, record dengan field opsional kosong,
        // serta data historis tahun sebelumnya.
        $scenarios = [
            // 1. Pending - menunggu approval, ada evidence completed
            [
                'no' => 'SPK-2025-001',
                'pemohon' => 'Budi Santoso',
                'vendor_code' => 'VDR-001',
                'bisnis_area' => 'TRL',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(2),
                'status' => PengadaanApprovalStatus::Pending,
                'items' => [
                    ['nama_item' => 'Multimeter Digital Fluke 87V', 'kategori' => 'Alat Ukur Listrik', 'qty' => 2, 'satuan' => 'unit', 'harga' => 8500000],
                    ['nama_item' => 'Kabel Test Set Premium', 'kategori' => 'Aksesoris', 'qty' => 5, 'satuan' => 'set', 'harga' => 1250000],
                ],
                'evidences' => [
                    ['name' => 'Surat Penawaran PT Sumber Alat Teknik.pdf'],
                    ['name' => 'Spesifikasi Teknis Multimeter.pdf'],
                ],
                'invoices' => [],
                'catatan' => 'Pengadaan multimeter untuk lab kalibrasi Priok.',
            ],
            // 2. Approved - belum ditagih (no invoice)
            [
                'no' => 'SPK-2025-002',
                'pemohon' => 'Dian Prasetyo',
                'vendor_code' => 'VDR-002',
                'bisnis_area' => 'ACY',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(15),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Caliper Digital Mitutoyo 500-196-30', 'kategori' => 'Alat Ukur Dimensi', 'qty' => 3, 'satuan' => 'unit', 'harga' => 4200000],
                ],
                'evidences' => [
                    ['name' => 'Quotation CV Kalibrasi Mandiri.pdf'],
                ],
                'invoices' => [],
                'catatan' => 'Pengadaan caliper untuk lab Surabaya.',
            ],
            // 3. Approved + invoice partial (ditagih sebagian) + payment approved (sebagian)
            [
                'no' => 'SPK-2025-003',
                'pemohon' => 'Andi Wijaya',
                'tipe_biaya' => TipeBiaya::RabProject,
                'nama_project' => 'Project Modernisasi Navigasi Kapal',
                'no_wbs' => 'WBS-PRJ-2025-003',
                'vendor_code' => 'VDR-003',
                'bisnis_area' => 'ENI',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(40),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Marine GPS Chartplotter Garmin', 'kategori' => 'Marine Electronics', 'qty' => 1, 'satuan' => 'unit', 'harga' => 25000000],
                    ['nama_item' => 'VHF Marine Radio Standard Horizon', 'kategori' => 'Marine Electronics', 'qty' => 2, 'satuan' => 'unit', 'harga' => 3500000],
                ],
                'evidences' => [
                    ['name' => 'Penawaran Marine Equipment.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-MEI-2025-001',
                        'tanggal' => now()->subDays(35),
                        'jumlah' => 15000000,
                        'catatan' => 'Invoice tahap 1 (DP 50%).',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(30),
                                'jumlah_bayar' => 10000000,
                                'metode' => 'Transfer Bank BCA',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan alat navigasi kapal.',
            ],
            // 4. Approved + invoice full + payment approved (LUNAS)
            [
                'no' => 'SPK-2025-004',
                'pemohon' => 'Sari Wulandari',
                'vendor_code' => 'VDR-004',
                'bisnis_area' => 'TKDN & Pemerintahan',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(60),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Thermohygrometer Testo 608-H1', 'kategori' => 'Alat Ukur Lingkungan', 'qty' => 4, 'satuan' => 'unit', 'harga' => 3200000],
                ],
                'evidences' => [
                    ['name' => 'PO Thermohygrometer.pdf'],
                    ['name' => 'Sertifikat Kalibrasi Testo.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-FLK-2025-004',
                        'tanggal' => now()->subDays(55),
                        'jumlah' => 12800000,
                        'catatan' => 'Invoice full.',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(30),
                                'jumlah_bayar' => 12800000,
                                'metode' => 'Transfer Bank Mandiri',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan thermohygrometer untuk 4 cabang.',
            ],
            // 5. Approved + invoice full + payment lunas (satu pembayaran penuh)
            [
                'no' => 'SPK-2025-005',
                'pemohon' => 'Nur Hidayati',
                'vendor_code' => 'VDR-005',
                'bisnis_area' => 'TKDN & Pemerintahan',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(20),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Bor Listrik Bosch GSB 13 RE', 'kategori' => 'Power Tools', 'qty' => 6, 'satuan' => 'unit', 'harga' => 1850000],
                    ['nama_item' => 'Gerinda Tangan Makita 9555H', 'kategori' => 'Power Tools', 'qty' => 4, 'satuan' => 'unit', 'harga' => 1400000],
                ],
                'evidences' => [
                    ['name' => 'Quotation Mitroteknik.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-MTR-2025-005',
                        'tanggal' => now()->subDays(15),
                        'jumlah' => 16700000,
                        'catatan' => 'Invoice full.',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(2),
                                'jumlah_bayar' => 16700000,
                                'metode' => 'Transfer Bank BRI',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan power tools workshop Makassar.',
            ],
            // 6. Approved + invoice full + payment partial (dibayar sebagian, sisanya belum)
            [
                'no' => 'SPK-2025-006',
                'pemohon' => 'Bagus Pramudya',
                'vendor_code' => 'VDR-006',
                'bisnis_area' => 'TRL',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(50),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Clamp Meter Fluke 376 FC', 'kategori' => 'Alat Ukur Listrik', 'qty' => 2, 'satuan' => 'unit', 'harga' => 9500000],
                ],
                'evidences' => [
                    ['name' => 'PO Clamp Meter.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-TST-2025-006',
                        'tanggal' => now()->subDays(45),
                        'jumlah' => 19000000,
                        'catatan' => 'Invoice full.',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(40),
                                'jumlah_bayar' => 9500000,
                                'metode' => 'Transfer Bank BCA',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan clamp meter, sisa 50% belum dibayar.',
            ],
            // 7. Rejected (dengan rejection_reason)
            [
                'no' => 'SPK-2025-007',
                'pemohon' => 'Fitri Anggraini',
                'vendor_code' => 'VDR-007',
                'bisnis_area' => 'ACY',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(10),
                'status' => PengadaanApprovalStatus::Rejected,
                'rejection_reason' => 'Harga satuan di atas estimasi pasar — lakukan negosiasi ulang dengan vendor atau cari alternatif merek lain.',
                'items' => [
                    ['nama_item' => 'Pressure Gauge WIKA 232.50 (Replacement)', 'kategori' => 'Alat Ukur Tekanan', 'qty' => 1, 'satuan' => 'unit', 'harga' => 1800000],
                ],
                'evidences' => [],
                'invoices' => [],
                'catatan' => 'Pengadaan pengganti pressure gauge rusak.',
            ],
            // 8. Approved + invoice overdue (jatuh tempo lewat, belum dibayar sama sekali)
            [
                'no' => 'SPK-2025-008',
                'pemohon' => 'Yusuf Maulana',
                'vendor_code' => 'VDR-008',
                'bisnis_area' => 'ENI',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(75),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Helm Safety SNI (Pack 10pcs)', 'kategori' => 'Safety Equipment', 'qty' => 10, 'satuan' => 'pack', 'harga' => 1500000],
                    ['nama_item' => 'Safety Shoes Steel Toe', 'kategori' => 'Safety Equipment', 'qty' => 20, 'satuan' => 'pasang', 'harga' => 650000],
                ],
                'evidences' => [
                    ['name' => 'Penawaran Safety Pro.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-SPI-2025-008',
                        'tanggal' => now()->subDays(70),
                        'jumlah' => 28000000,
                        'catatan' => 'Invoice full, sudah lewat jatuh tempo.',
                        'payments' => [],
                    ],
                ],
                'catatan' => 'Pengadaan APD untuk workshop Surabaya. INVOICE OVERDUE.',
            ],
            // 9. Approved + invoice + payment rejected (perlu re-submit bukti transfer)
            [
                'no' => 'SPK-2025-009',
                'pemohon' => 'Ratna Sari',
                'vendor_code' => 'VDR-009',
                'bisnis_area' => 'TKDN & Pemerintahan',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(25),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Trolley Hydraulic 2 Ton', 'kategori' => 'Material Handling', 'qty' => 2, 'satuan' => 'unit', 'harga' => 7500000],
                ],
                'evidences' => [
                    ['name' => 'PO Trolley Hydraulic.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-MLM-2025-009',
                        'tanggal' => now()->subDays(20),
                        'jumlah' => 15000000,
                        'catatan' => 'Invoice full.',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(5),
                                'jumlah_bayar' => 15000000,
                                'metode' => 'Transfer Bank BNI',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan trolley hydraulic untuk gudang Banjarmasin.',
            ],
            // 10. Approved + multi-item + multi-evidence (kompleks, lunas)
            [
                'no' => 'SPK-2025-010',
                'pemohon' => 'Siti Rahayu',
                'tipe_biaya' => TipeBiaya::RabProject,
                'nama_project' => 'Project Paket Lab Kalibrasi Priok',
                'no_wbs' => 'WBS-LAB-2025-010',
                'vendor_code' => 'VDR-001',
                'bisnis_area' => 'TRL',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(90),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Multimeter Digital Fluke 87V', 'kategori' => 'Alat Ukur Listrik', 'qty' => 1, 'satuan' => 'unit', 'harga' => 8500000],
                    ['nama_item' => 'Caliper Digital Mitutoyo 500-196-30', 'kategori' => 'Alat Ukur Dimensi', 'qty' => 1, 'satuan' => 'unit', 'harga' => 4200000],
                    ['nama_item' => 'Toolbox Besi 5 Laci', 'kategori' => 'Storage', 'qty' => 2, 'satuan' => 'unit', 'harga' => 2500000],
                    ['nama_item' => 'Multimeter Test Lead Set', 'kategori' => 'Aksesoris', 'qty' => 10, 'satuan' => 'set', 'harga' => 300000],
                ],
                'evidences' => [
                    ['name' => 'Surat Penawaran PT Sumber Alat Teknik.pdf'],
                    ['name' => 'Spesifikasi Teknis Lengkap.pdf'],
                    ['name' => 'BAST Pengadaan.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-SAT-2025-010-A',
                        'tanggal' => now()->subDays(85),
                        'jumlah' => 10000000,
                        'catatan' => 'Invoice tahap 1 (DP).',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(80),
                                'jumlah_bayar' => 10000000,
                                'metode' => 'Transfer Bank BCA',
                            ],
                        ],
                    ],
                    [
                        'no' => 'INV-SAT-2025-010-B',
                        'tanggal' => now()->subDays(50),
                        'jumlah' => 10700000,
                        'catatan' => 'Invoice tahap 2 (pelunasan).',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(45),
                                'jumlah_bayar' => 10700000,
                                'metode' => 'Transfer Bank BCA',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan paket lengkap lab kalibrasi Priok. Multi-invoice & multi-payment.',
            ],
            // 11. Approved + invoice + multi-payment (lunas, 2x cicil)
            [
                'no' => 'SPK-2025-011',
                'pemohon' => 'Eko Prasetyo',
                'vendor_code' => 'VDR-003',
                'bisnis_area' => 'ENI',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(45),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Oscilloscope Rigol DS1054Z', 'kategori' => 'Alat Ukur Elektronik', 'qty' => 1, 'satuan' => 'unit', 'harga' => 12000000],
                ],
                'evidences' => [
                    ['name' => 'PO Oscilloscope Rigol.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-MEI-2025-011',
                        'tanggal' => now()->subDays(40),
                        'jumlah' => 12000000,
                        'catatan' => 'Invoice full.',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(35),
                                'jumlah_bayar' => 4000000,
                                'metode' => 'Transfer Bank Mandiri',
                            ],
                            [
                                'tanggal_bayar' => now()->subDays(20),
                                'jumlah_bayar' => 4000000,
                                'metode' => 'Transfer Bank Mandiri',
                            ],
                            [
                                'tanggal_bayar' => now()->subDays(5),
                                'jumlah_bayar' => 4000000,
                                'metode' => 'Transfer Bank Mandiri',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan oscilloscope, dibayar 3x cicil.',
            ],
            // 12. Pending dengan evidence processing (file_status=processing)
            [
                'no' => 'SPK-2025-012',
                'pemohon' => 'Dimas Aryo',
                'vendor_code' => 'VDR-006',
                'bisnis_area' => 'TRL',
                'cabang' => $jakarta,
                'tanggal' => now()->subDay(),
                'status' => PengadaanApprovalStatus::Pending,
                'items' => [
                    ['nama_item' => 'Anemometer Testo 405i', 'kategori' => 'Alat Ukur Lingkungan', 'qty' => 3, 'satuan' => 'unit', 'harga' => 2800000],
                ],
                'evidences' => [
                    ['name' => 'Quotation Testo.pdf', 'processing' => true],
                ],
                'invoices' => [],
                'catatan' => 'Pengadaan anemometer, evidence masih diproses.',
            ],
            // 13. Approved + invoice muda belum dibayar (status jatuh tempo Aman)
            [
                'no' => 'SPK-2025-013',
                'pemohon' => 'Dewi Lestari',
                'vendor_code' => 'VDR-006',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(18),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Data Logger Suhu Testo 175 T1', 'kategori' => 'Alat Ukur Lingkungan', 'qty' => 4, 'satuan' => 'unit', 'harga' => 2100000],
                    ['nama_item' => 'Sound Level Meter Testo 816-1', 'kategori' => 'Alat Ukur Lingkungan', 'qty' => 1, 'satuan' => 'unit', 'harga' => 5400000],
                ],
                'evidences' => [
                    ['name' => 'Penawaran PT Testo Instruments.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-TST-2025-013',
                        'tanggal' => now()->subDays(12),
                        'jumlah' => 13800000,
                        'catatan' => 'Invoice full, menunggu jadwal pembayaran.',
                        'payments' => [],
                    ],
                ],
                'catatan' => 'Pengadaan data logger dan sound level meter untuk ruang uji lingkungan.',
            ],
            // 14. RAB ACY pending — step 1 (SM ACY) selesai, menunggu MK/SM Dukbis
            [
                'no' => 'SPK-2025-014',
                'pemohon' => 'Rina Kartika',
                'tipe_biaya' => TipeBiaya::RabProject,
                'nama_project' => 'Project Renovasi Ruang Kelas BKI Academy',
                'no_wbs' => 'WBS-ACY-2025-014',
                'vendor_code' => 'VDR-005',
                'bisnis_area' => 'ACY',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(8),
                'status' => PengadaanApprovalStatus::Pending,
                'items' => [
                    ['nama_item' => 'Mesin Bor Duduk Bosch GBD 350', 'kategori' => 'Power Tools', 'qty' => 1, 'satuan' => 'unit', 'harga' => 6750000],
                    ['nama_item' => 'Lemari Perkakas Besi 3 Pintu', 'kategori' => 'Storage', 'qty' => 2, 'satuan' => 'unit', 'harga' => 4800000],
                ],
                'evidences' => [
                    ['name' => 'Penawaran CV Mitroteknik.pdf'],
                    ['name' => 'RAB Renovasi Ruang Kelas.xlsx'],
                ],
                'invoices' => [],
                'catatan' => 'Pengadaan peralatan renovasi ruang kelas Academy lantai 2.',
            ],
            // 15. RAB TKDN & Pemerintahan approved + lunas
            [
                'no' => 'SPK-2025-015',
                'pemohon' => 'Ferry Kurniawan',
                'tipe_biaya' => TipeBiaya::RabProject,
                'nama_project' => 'Project Verifikasi TKDN Batch II',
                'no_wbs' => 'WBS-TKD-2025-015',
                'vendor_code' => 'VDR-009',
                'bisnis_area' => 'TKDN & Pemerintahan',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(95),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Hand Pallet Truck 3 Ton', 'kategori' => 'Material Handling', 'qty' => 2, 'satuan' => 'unit', 'harga' => 5800000],
                ],
                'evidences' => [
                    ['name' => 'PO Hand Pallet.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-MLM-2025-015',
                        'tanggal' => now()->subDays(90),
                        'jumlah' => 11600000,
                        'catatan' => 'Invoice full.',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(55),
                                'jumlah_bayar' => 11600000,
                                'metode' => 'Virtual Account BNI',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Pengadaan hand pallet untuk kegiatan verifikasi TKDN.',
            ],
            // 16. Pengadaan pusat tanpa vendor/nomor SPK — field opsional kosong (Pending)
            [
                'no' => null,
                'pemohon' => 'Agus Prasetyo',
                'vendor_code' => null,
                'cabang' => null,
                'tanggal' => null,
                'submitted_at' => now()->subDays(4),
                'status' => PengadaanApprovalStatus::Pending,
                'items' => [
                    ['nama_item' => 'Jasa Konsultasi Layout Laboratorium', 'kategori' => null, 'qty' => 1, 'satuan' => 'jasa', 'harga' => 18000000],
                ],
                'evidences' => [],
                'invoices' => [],
                'catatan' => 'Pengajuan awal — vendor dan nomor pesanan menyusul setelah approval.',
            ],
            // 17. Rejected — pengadaan pusat (tanpa cabang), alasan penolakan eksplisit
            [
                'no' => 'SPK-2025-017',
                'pemohon' => 'Hendra Gunawan',
                'vendor_code' => 'VDR-007',
                'cabang' => null,
                'tanggal' => now()->subDays(14),
                'status' => PengadaanApprovalStatus::Rejected,
                'rejection_reason' => 'Total biaya melebihi anggaran yang disetujui untuk periode ini. Ajukan kembali setelah revisi anggaran.',
                'items' => [
                    ['nama_item' => 'Pressure Transmitter WIKA S-20', 'kategori' => 'Alat Ukur Tekanan', 'qty' => 6, 'satuan' => 'unit', 'harga' => 12500000],
                ],
                'evidences' => [
                    ['name' => 'Penawaran PT WIKA Instrument.pdf'],
                ],
                'invoices' => [],
                'catatan' => 'Pengadaan pressure transmitter untuk upgrade stasiun uji.',
            ],
            // 18. Data historis 2024 — Fix Cost lunas
            [
                'no' => 'SPK-2024-101',
                'pemohon' => 'Maya Sari',
                'vendor_code' => 'VDR-004',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(400),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Jasa Kalibrasi Thermohygrometer (Per Titik)', 'kategori' => 'Alat Ukur Lingkungan', 'qty' => 12, 'satuan' => 'jasa', 'harga' => 750000],
                ],
                'evidences' => [
                    ['name' => 'PO Kalibrasi 2024.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-FLK-2024-101',
                        'tanggal' => now()->subDays(390),
                        'jumlah' => 9000000,
                        'catatan' => 'Invoice full.',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(370),
                                'jumlah_bayar' => 9000000,
                                'metode' => 'Transfer Bank Mandiri',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Kalibrasi berkala thermohygrometer tahun 2024.',
            ],
            // 19. Data historis 2024 — RAB ENI lunas (multi-invoice)
            [
                'no' => 'SPK-2024-102',
                'pemohon' => 'Rudi Hartono',
                'tipe_biaya' => TipeBiaya::RabProject,
                'nama_project' => 'Project Retrofit Panel Kontrol Kapal Survei',
                'no_wbs' => 'WBS-ENI-2024-102',
                'vendor_code' => 'VDR-003',
                'bisnis_area' => 'ENI',
                'cabang' => $jakarta,
                'tanggal' => now()->subDays(365),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Echosounder Garmin Striker Vivid', 'kategori' => 'Marine Electronics', 'qty' => 2, 'satuan' => 'unit', 'harga' => 9800000],
                    ['nama_item' => 'Signal Generator Rigol DG1022Z', 'kategori' => 'Alat Ukur Elektronik', 'qty' => 1, 'satuan' => 'unit', 'harga' => 7200000],
                ],
                'evidences' => [
                    ['name' => 'Penawaran PT Marine Equipment.pdf'],
                    ['name' => 'BAST Retrofit Panel.pdf'],
                ],
                'invoices' => [
                    [
                        'no' => 'INV-MEI-2024-102-A',
                        'tanggal' => now()->subDays(360),
                        'jumlah' => 13400000,
                        'catatan' => 'Invoice tahap 1 (DP 50%).',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(340),
                                'jumlah_bayar' => 13400000,
                                'metode' => 'Transfer Bank BCA',
                            ],
                        ],
                    ],
                    [
                        'no' => 'INV-MEI-2024-102-B',
                        'tanggal' => now()->subDays(320),
                        'jumlah' => 13400000,
                        'catatan' => 'Invoice tahap 2 (pelunasan).',
                        'payments' => [
                            [
                                'tanggal_bayar' => now()->subDays(300),
                                'jumlah_bayar' => 13400000,
                                'metode' => 'Transfer Bank BCA',
                            ],
                        ],
                    ],
                ],
                'catatan' => 'Retrofit panel kontrol kapal survei — selesai dan lunas.',
            ],
            // 20. Approved pengadaan pusat — belum ada invoice
            [
                'no' => 'SPK-2025-016',
                'pemohon' => 'Lina Marlina',
                'vendor_code' => 'VDR-009',
                'cabang' => null,
                'tanggal' => now()->subDays(28),
                'status' => PengadaanApprovalStatus::Approved,
                'items' => [
                    ['nama_item' => 'Trolley Barang Heavy Duty 500kg', 'kategori' => 'Material Handling', 'qty' => 3, 'satuan' => 'unit', 'harga' => 3900000],
                ],
                'evidences' => [
                    ['name' => 'Quotation PT Mitra Logistik.pdf'],
                ],
                'invoices' => [],
                'catatan' => 'Pengadaan trolley untuk kantor pusat.',
            ],
        ];

        DB::transaction(function () use ($scenarios, $vendors, $bisnisAreas, $satuanMap, $kategoriMap, $admin, $fileStorage, $disk) {
            foreach ($scenarios as $scenario) {
                $vendor = $vendors->get($scenario['vendor_code'] ?? '');
                if (($scenario['vendor_code'] ?? null) && ! $vendor) {
                    continue;
                }

                // Hitung total_biaya dari items
                $totalBiaya = 0;
                $itemsData = [];
                foreach ($scenario['items'] as $item) {
                    $subtotal = bcmul($item['harga'], (string) $item['qty'], 2);
                    $totalBiaya = bcadd($totalBiaya, $subtotal, 2);
                    $itemsData[] = [
                        'nama_item' => $item['nama_item'],
                        'kategori_item_id' => $kategoriMap[$item['kategori']] ?? null,
                        'qty' => $item['qty'],
                        'satuan_id' => $satuanMap[strtoupper($item['satuan'])] ?? $satuanMap['UNIT'],
                        'harga_satuan' => $item['harga'],
                        'subtotal' => $subtotal,
                    ];
                }

                $pengadaan = Pengadaan::firstOrCreate(
                    $scenario['no']
                        ? ['no_pesanan_spk' => $scenario['no']]
                        : ['no_pesanan_spk' => null, 'nama_pemohon' => $scenario['pemohon'] ?? $admin->name],
                    [
                        'nama_pemohon' => $scenario['pemohon'] ?? $admin->name,
                        'tipe_biaya' => ($scenario['tipe_biaya'] ?? TipeBiaya::FixCost)->value,
                        'nama_project' => $scenario['nama_project'] ?? null,
                        'no_wbs' => $scenario['no_wbs'] ?? null,
                        'vendor_id' => $vendor?->id,
                        'bisnis_area_id' => ($scenario['tipe_biaya'] ?? TipeBiaya::FixCost) === TipeBiaya::RabProject
                            ? $bisnisAreas->get($scenario['bisnis_area'] ?? 'TRL')
                            : null,
                        'cabang_id' => $scenario['cabang']?->id,
                        'tanggal_pesanan_spk' => $scenario['tanggal'] ?? null,
                        'total_biaya' => $totalBiaya,
                        'status_approval' => $scenario['status']->value,
                        'approved_by' => $scenario['status'] !== PengadaanApprovalStatus::Pending ? $admin->id : null,
                        'catatan' => $scenario['catatan'] ?? null,
                    ]
                );

                // Simulasikan approval instance via engine — hasilnya instances,
                // steps, pivot approvers, dan action history seperti diisi real user.
                if ($pengadaan->wasRecentlyCreated) {
                    $this->simulateApproval($pengadaan, $scenario, $admin);
                }

                // Items
                foreach ($itemsData as $itemData) {
                    $itemData['pengadaan_id'] = $pengadaan->id;
                    PengadaanItem::firstOrCreate(
                        [
                            'pengadaan_id' => $pengadaan->id,
                            'nama_item' => $itemData['nama_item'],
                            'qty' => $itemData['qty'],
                        ],
                        $itemData
                    );
                }

                // Evidences (seeder: tulis file dummy ke disk agar download bisa di-test)
                foreach ($scenario['evidences'] as $evidence) {
                    $isProcessing = $evidence['processing'] ?? false;

                    if ($isProcessing) {
                        // Evidence processing: tidak ada file fisik, status 'processing'
                        PengadaanEvidence::firstOrCreate(
                            [
                                'pengadaan_id' => $pengadaan->id,
                                'name' => $evidence['name'],
                            ],
                            [
                                'file_path' => null,
                                'file_name' => null,
                                'file_size' => null,
                                'file_status' => 'processing',
                                'file_processed_at' => null,
                            ]
                        );

                        continue;
                    }

                    // Evidence completed: bangun path konsisten via FileStorageService,
                    // lalu tulis file dummy ke disk agar download() bekerja.
                    $path = $fileStorage->buildPath(
                        'pengadaan-evidence',
                        [$pengadaan->spk_slug],
                        $evidence['name']
                    );
                    $dummyContent = "Seeded evidence file for {$pengadaan->spk_slug}: {$evidence['name']}";
                    Storage::disk($disk)->put($path, $dummyContent);
                    $size = strlen($dummyContent);

                    PengadaanEvidence::firstOrCreate(
                        [
                            'pengadaan_id' => $pengadaan->id,
                            'name' => $evidence['name'],
                        ],
                        [
                            'file_path' => $path,
                            'file_name' => $evidence['name'],
                            'file_size' => $size,
                            'file_status' => 'completed',
                            'file_processed_at' => now(),
                        ]
                    );
                }

                // Invoices + Payments (hanya untuk pengadaan approved)
                if ($scenario['status'] === PengadaanApprovalStatus::Approved) {
                    foreach ($scenario['invoices'] as $invoiceData) {
                        $invoice = Invoice::firstOrCreate(
                            ['pengadaan_id' => $pengadaan->id, 'no_invoice' => $invoiceData['no']],
                            [
                                'tanggal_invoice' => $invoiceData['tanggal'],
                                'jumlah' => $invoiceData['jumlah'],
                                'catatan' => $invoiceData['catatan'] ?? null,
                                'file_status' => null, // seeder tidak upload file fisik invoice
                            ]
                        );

                        foreach ($invoiceData['payments'] as $paymentData) {
                            InvoicePayment::firstOrCreate(
                                [
                                    'invoice_id' => $invoice->id,
                                    'tanggal_bayar' => $paymentData['tanggal_bayar'],
                                    'jumlah_bayar' => $paymentData['jumlah_bayar'],
                                ],
                                [
                                    'metode_bayar' => $paymentData['metode'] ?? null,
                                    'file_status' => null, // seeder tidak upload bukti transfer fisik
                                ]
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Simulasikan lifecycle approval via engine — menghasilkan instance,
     * snapshot steps, pivot approvers, dan action history yang realistis
     * (timestamp dimundurkan mengikuti tanggal skenario).
     */
    private function simulateApproval(Pengadaan $pengadaan, array $scenario, User $admin): void
    {
        $service = app(ApprovalService::class);
        $target = $scenario['status'];

        $submittedAt = Carbon::parse($scenario['submitted_at'] ?? $scenario['tanggal'] ?? now())->copy()->setTime(9, 0);
        $instance = $service->submit($pengadaan, $admin);

        if (! $instance) {
            return;
        }

        // Backdate submit
        $instance->update(['created_at' => $submittedAt, 'updated_at' => $submittedAt]);
        $instance->actions()->oldest('id')->first()?->update([
            'created_at' => $submittedAt,
            'updated_at' => $submittedAt,
        ]);

        $at = $submittedAt->copy();
        $lastPosition = $instance->steps->max('position');
        // Pending: sisakan step terakhir agar terlihat "sedang diproses" —
        // flow 1 tahap berarti tidak ada step yang dijalankan sama sekali.
        $maxStepsToComplete = $target === PengadaanApprovalStatus::Pending
            ? max(0, $lastPosition - 1)
            : PHP_INT_MAX;
        $completed = 0;

        while ($instance->refresh()->isInProgress() && $completed < $maxStepsToComplete) {
            $step = $instance->currentStep()->with('approvers')->first();

            if (! $step) {
                break;
            }

            $at->addDays(2)->setTime(10 + $step->position, 15);

            // Target rejected → approve step-step awal, reject di step terakhir
            if ($target === PengadaanApprovalStatus::Rejected && $step->position === $lastPosition) {
                $service->reject($instance->refresh(), $step->approvers->first(), $scenario['rejection_reason'] ?? 'Ditolak oleh approver.');
                break;
            }

            $needed = $step->mode->value === 'all' ? $step->approvers->count() : 1;

            foreach ($step->approvers->take($needed) as $approver) {
                $service->approve($instance->refresh(), $approver);
                $instance->actions()->latest('id')->first()?->update([
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);
            }

            $completed++;
        }

        // Samakan timestamp akhir dengan skenario (approved_at bisa berbeda hari)
        $finalAt = $scenario['approved_at'] ?? $at;
        $instance->update(['completed_at' => $instance->completed_at ? $finalAt : null]);
    }
}
