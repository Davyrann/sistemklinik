<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuitansi #{{ str_pad($tagihan->id, 5, '0', STR_PAD_LEFT) }} — {{ $tagihan->kunjungan->pasien->nama }}</title>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #0F172A;
            background-color: #F8FAFC;
            padding: 24px 16px;
            font-size: 13px;
            line-height: 1.4;
            -webkit-font-smoothing: antialiased;
        }

        .mono {
            font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-variant-numeric: tabular-nums;
        }

        .receipt-container {
            max-width: 680px;
            margin: 0 auto;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 24px 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0F172A;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }

        .clinic-brand h1 {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0F172A;
        }

        .clinic-brand p {
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }

        .receipt-title {
            text-align: right;
        }

        .receipt-title h2 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .receipt-title p {
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px dashed #94A3B8;
            font-size: 12px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
        }

        .meta-label {
            color: #475569;
        }

        .meta-value {
            font-weight: 600;
            color: #0F172A;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 16px;
        }

        th {
            background-color: #F1F5F9;
            color: #0F172A;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            padding: 8px 10px;
            border-top: 1px solid #0F172A;
            border-bottom: 1px solid #0F172A;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #E2E8F0;
            color: #0F172A;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            border-top: 2px solid #0F172A;
            padding-top: 8px;
            margin-top: 8px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 10px;
            font-size: 13px;
        }

        .total-row.grand-total {
            font-size: 15px;
            font-weight: 700;
            border-top: 1px solid #CBD5E1;
            margin-top: 4px;
            padding-top: 8px;
        }

        .payment-history {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px dashed #94A3B8;
        }

        .payment-history h3 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 6px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 2px 0;
            color: #0F172A;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-top: 32px;
            padding-top: 16px;
            text-align: center;
            font-size: 12px;
        }

        .signature-box p:first-child {
            color: #475569;
            margin-bottom: 50px;
        }

        .signature-box p:last-child {
            font-weight: 600;
            border-top: 1px solid #0F172A;
            display: inline-block;
            min-width: 140px;
            padding-top: 4px;
        }

        .actions-bar {
            max-width: 680px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            background-color: #0F766E;
            color: #FFFFFF;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-print:hover {
            background-color: #115E59;
        }

        .btn-back {
            color: #0F766E;
            text-decoration: underline;
            font-size: 13px;
        }

        @media print {
            body {
                background: #FFFFFF;
                color: #000000;
                padding: 0;
                margin: 0;
            }

            .actions-bar {
                display: none !important;
            }

            .receipt-container {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }

            th {
                background-color: transparent !important;
                border-top: 1px solid #000000;
                border-bottom: 1px solid #000000;
            }

            td {
                border-bottom: 1px solid #CCCCCC;
            }

            .header, .totals-section {
                border-color: #000000 !important;
            }

            .meta-grid, .payment-history {
                border-color: #999999 !important;
            }

            .signature-box p:last-child {
                border-color: #000000 !important;
            }
        }
    </style>
</head>
<body>
    <div class="actions-bar">
        <a href="javascript:window.close()" class="btn-back">Tutup Halaman</a>
        <button onclick="window.print()" class="btn-print">Cetak Kuitansi</button>
    </div>

    <div class="receipt-container">
        {{-- Clinic Identity & Document Header --}}
        <header class="header">
            <div class="clinic-brand">
                <h1>Klinik Pratama Rawat Jalan</h1>
                <p>Jl. Kesehatan No. 12, Telp: (021) 555-0143</p>
            </div>
            <div class="receipt-title">
                <h2>KUITANSI PEMBAYARAN</h2>
                <p class="mono">No: #{{ str_pad($tagihan->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </header>

        {{-- Meta Information Grid (2 Identifiers, Casheer, Dates) --}}
        <div class="meta-grid">
            <div class="meta-col">
                <div class="meta-row">
                    <span class="meta-label">Nama Pasien:</span>
                    <span class="meta-value">{{ $tagihan->kunjungan->pasien->nama }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">No. Rekam Medis:</span>
                    <span class="meta-value mono">{{ $tagihan->kunjungan->pasien->no_rm }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">No. Antrean:</span>
                    <span class="meta-value mono">{{ $tagihan->kunjungan->no_antrean }}</span>
                </div>
            </div>
            <div class="meta-col">
                <div class="meta-row">
                    <span class="meta-label">Tanggal Pelayanan:</span>
                    <span class="meta-value mono">{{ $tagihan->kunjungan->tgl_kunjungan->format('d/m/Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Tanggal Cetak:</span>
                    <span class="meta-value mono">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Status Tagihan:</span>
                    <span class="meta-value uppercase">{{ strtoupper($tagihan->status) }}</span>
                </div>
            </div>
        </div>

        {{-- Itemized Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Deskripsi Layanan / Obat</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 20%;">Harga Satuan</th>
                    <th class="text-right" style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tagihan->details as $d)
                    <tr>
                        <td>{{ $d->deskripsi }}</td>
                        <td class="text-center mono">{{ $d->qty }}</td>
                        <td class="text-right mono">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right mono">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            @if ((float) $tagihan->diskon > 0)
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span class="mono">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Diskon:</span>
                    <span class="mono">- Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL AKHIR:</span>
                <span class="mono">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment History & Cashier Info --}}
        @php
            $latestPembayaran = $tagihan->pembayarans->sortByDesc('id')->first();
            $kasirName = $latestPembayaran?->kasir?->name ?? 'Kasir Klinik';
        @endphp
        @if ($tagihan->pembayarans->isNotEmpty())
            <div class="payment-history">
                <h3>Rincian Pembayaran Masuk</h3>
                @foreach ($tagihan->pembayarans as $by)
                    <div class="payment-row">
                        <span>
                            <strong class="mono">{{ strtoupper($by->metode) }}</strong> · 
                            <span class="mono">{{ $by->tgl_bayar->format('d/m/Y H:i') }}</span>
                            @if ($by->keterangan)
                                · ({{ $by->keterangan }})
                            @endif
                        </span>
                        <span class="mono font-semibold">Rp {{ number_format($by->jumlah, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="signature-box">
                <p>Pasien / Keluarga</p>
                <p>{{ $tagihan->kunjungan->pasien->nama }}</p>
            </div>
            <div class="signature-box">
                <p>Petugas Kasir</p>
                <p>{{ $kasirName }}</p>
            </div>
        </div>
    </div>
</body>
</html>
