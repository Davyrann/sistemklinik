<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etiket Obat — RSP-{{ str_pad($resep->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #FFFFFF;
            color: #0F172A;
            padding: 16px;
            font-size: 12px;
            line-height: 1.4;
        }
        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "JetBrains Mono", monospace;
        }
        .no-print {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px;
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            color: #0F172A;
            text-decoration: none;
        }
        .btn-primary {
            background: #0F766E;
            color: #FFFFFF;
            border-color: #0F766E;
        }
        .etiket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        .etiket-card {
            border: 2px solid #0F172A;
            border-radius: 6px;
            padding: 12px;
            background: #FFFFFF;
            page-break-inside: avoid;
        }
        .etiket-header {
            text-align: center;
            border-bottom: 1px dashed #0F172A;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .clinic-name {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .clinic-sub {
            font-size: 10px;
            color: #475569;
        }
        .patient-info {
            margin-bottom: 8px;
            font-size: 11px;
        }
        .medicine-box {
            border-top: 1px dashed #0F172A;
            border-bottom: 1px dashed #0F172A;
            padding: 8px 0;
            margin: 8px 0;
            text-align: center;
        }
        .medicine-name {
            font-size: 14px;
            font-weight: 700;
        }
        .medicine-dosage {
            font-size: 13px;
            font-weight: 700;
            margin-top: 4px;
        }
        .instructions {
            font-size: 11px;
            text-align: center;
            margin-top: 6px;
        }
        .footer-note {
            margin-top: 8px;
            font-size: 9px;
            text-align: center;
            color: #475569;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .etiket-card {
                border-color: #0F172A;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            Cetak Etiket Obat
        </button>
        <button type="button" onclick="window.close()" class="btn">
            Tutup
        </button>
    </div>

    <div class="etiket-grid">
        @foreach ($resep->details as $item)
            <div class="etiket-card">
                <div class="etiket-header">
                    <div class="clinic-name">Klinik Pratama Rawat Jalan</div>
                    <div class="clinic-sub">Instalasi Farmasi & Apotek</div>
                    <div class="clinic-sub font-mono">Tgl: {{ now()->format('d/m/Y') }} | No: RSP-{{ str_pad($resep->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>

                <div class="patient-info font-mono">
                    <div>Pasien : <strong>{{ $resep->kunjungan->pasien->nama }}</strong></div>
                    <div>No. RM : {{ $resep->kunjungan->pasien->no_rm }}</div>
                    <div>Dokter : {{ $resep->dokter->nama ?? '-' }}</div>
                </div>

                <div class="medicine-box">
                    <div class="medicine-name">{{ $item->obat->nama_generik }}</div>
                    @if ($item->obat->nama_dagang)
                        <div style="font-size: 11px; color: #475569;">({{ $item->obat->nama_dagang }})</div>
                    @endif
                    <div class="medicine-dosage font-mono">
                        {{ $item->dosis ?: '1 Tablet' }} — {{ $item->frekuensi ?: '3x Sehari' }}
                    </div>
                    <div class="instructions">
                        {{ $item->aturan_pakai ?: 'Sesudah makan' }}
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 10px;" class="font-mono">
                    <span>Jumlah: {{ $item->jumlah }} {{ $item->obat->satuan ?? 'satuan' }}</span>
                    <span>Paraf Farmasis: _______</span>
                </div>

                <div class="footer-note">
                    Semoga Lekas Sembuh. Simpan di tempat sejuk & kering.
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
