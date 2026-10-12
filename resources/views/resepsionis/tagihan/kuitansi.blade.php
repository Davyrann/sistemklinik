<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kuitansi — {{ $tagihan->kunjungan->pasien->nama }}</title>
    <style>
        body { font-family: system-ui, sans-serif; color: #0F172A; max-width: 640px; margin: 2rem auto; padding: 1rem; }
        h1 { font-size: 1.25rem; margin: 0; }
        .muted { color: #64748B; font-size: .875rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: .875rem; }
        th, td { text-align: left; padding: .5rem; border-bottom: 1px solid #E2E8F0; }
        .mono { font-family: ui-monospace, monospace; }
        .total { font-weight: 700; font-size: 1rem; }
        @media print { body { margin: 0; } button { display: none; } }
    </style>
</head>
<body>
    <h1>KlinikKu — Kuitansi</h1>
    <p class="muted">No. Kunjungan {{ $tagihan->kunjungan->no_antrean }} · {{ $tagihan->kunjungan->tgl_kunjungan->format('d M Y') }}</p>
    <p><strong>{{ $tagihan->kunjungan->pasien->nama }}</strong> — {{ $tagihan->kunjungan->pasien->no_rm }}<br><span class="muted">{{ $tagihan->kunjungan->pasien->alamat ?? '-' }}</span></p>
    <table>
        <thead><tr><th>Deskripsi</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
        <tbody>
            @foreach ($tagihan->details as $d)
                <tr><td>{{ $d->deskripsi }}</td><td class="mono">{{ $d->qty }}</td><td class="mono">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td><td class="mono">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <p class="total mono">Total Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }} — {{ strtoupper($tagihan->status) }}</p>
    @if ($tagihan->pembayarans->isNotEmpty())
        <p class="muted">Pembayaran:</p>
        <ul class="muted">
            @foreach ($tagihan->pembayarans as $by)
                <li>{{ strtoupper($by->metode) }} Rp {{ number_format($by->jumlah, 0, ',', '.') }} — {{ $by->tgl_bayar->format('d M Y H:i') }}</li>
            @endforeach
        </ul>
    @endif
    <p class="muted" style="margin-top:2rem">Terima kasih atas kunjungan Anda.</p>
    <button onclick="window.print()" style="margin-top:1rem; padding:.5rem 1rem; background:#0F766E; color:#fff; border:none; border-radius:6px; cursor:pointer">Cetak</button>
</body>
</html>
