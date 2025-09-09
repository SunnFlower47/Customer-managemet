<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Pembayaran - {{ $pembayaran->kode_pembayaran }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 15px;
            background-color: #f8f9fa;
            font-size: 12px;
        }
        
        /* PDF-specific body styles */
        @media print {
            body {
                margin: 0;
                padding: 10px;
                background-color: white;
            }
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* PDF-specific container styles */
        @media print {
            .invoice-container {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }
        }
        .header {
            background: #ffffff;
            color: #333;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #667eea;
        }
        
        /* PDF-specific header styles */
        @media print {
            .header {
                padding: 15px;
                margin-bottom: 0;
            }
        }
        .company-info {
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .company-details {
            font-size: 11px;
            opacity: 0.9;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }
        .content {
            padding: 20px;
        }
        
        /* PDF-specific content styles */
        @media print {
            .content {
                padding: 15px;
            }
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .invoice-info, .customer-info {
            flex: 1;
            min-width: 250px;
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 3px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #666;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .payment-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .amount-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            color: #28a745;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .status-lunas {
            background: #d4edda;
            color: #155724;
        }
        .footer {
            background: #f8f9fa;
            padding: 15px 20px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        /* Hide buttons in PDF generation */
        @media print, @page {
            .action-buttons {
                display: none !important;
                visibility: hidden !important;
            }
        }
        
        /* PDF-specific styles */
        @page {
            margin: 0.25in;
            size: A4;
        }
        .print-button, .download-button {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .print-button:hover, .download-button:hover {
            background: #5a6fd8;
        }
        .download-button {
            background: #28a745;
        }
        .download-button:hover {
            background: #218838;
        }
        @media print {
            .action-buttons {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    @if(!isset($isPdf) || !$isPdf)
    <div class="action-buttons">
        <a href="#" class="print-button" onclick="window.print(); return false;">
            <i class="fas fa-print"></i> Cetak
        </a>
        <a href="{{ route('pembayarans.invoice.pdf', $pembayaran) }}" class="download-button" target="_blank">
            <i class="fas fa-download"></i> Download PDF
        </a>
    </div>
    @endif

    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                @php
                    $companyProfile = \App\Models\CompanyProfile::first();
                    $companyName = $companyProfile->official_name ?? 'BCM';
                    $companyInitials = $companyProfile->initials ?? 'BCM';
                @endphp
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-details">
                    {{ $companyInitials }} WiFi Billing Management System<br>
                    {{ $companyProfile->alamat ?? 'Jl. Raya Teknologi No. 123, Jakarta' }}<br>
                    Telp: {{ $companyProfile->nomor_kontak ?? '(021) 1234-5678' }} | Email: {{ $companyProfile->email_support ?? 'info@bcmnet.com' }}
                </div>
            </div>
            <div class="invoice-title">FAKTUR PEMBAYARAN</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Invoice Details -->
            <div class="invoice-details">
                <div class="invoice-info">
                    <div class="section-title">Detail Faktur</div>
                    <div class="info-row">
                        <div class="info-label">Kode Faktur:</div>
                        <div class="info-value">{{ $pembayaran->kode_pembayaran }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Faktur:</div>
                        <div class="info-value">{{ $pembayaran->created_at->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Periode Tagihan:</div>
                        <div class="info-value">{{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->translatedFormat('F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            <span class="status-badge status-lunas">LUNAS</span>
                        </div>
                    </div>
                    @if($pembayaran->tanggal_bayar)
                    <div class="info-row">
                        <div class="info-label">Tanggal Bayar:</div>
                        <div class="info-value">{{ $pembayaran->tanggal_bayar->format('d F Y H:i') }}</div>
                    </div>
                    @endif
                </div>

                <div class="customer-info">
                    <div class="section-title">Data Pelanggan</div>
                    <div class="info-row">
                        <div class="info-label">Nama:</div>
                        <div class="info-value">{{ $pembayaran->pelanggan->nama }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">PPPoE:</div>
                        <div class="info-value">{{ $pembayaran->pelanggan->pppoe }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. HP:</div>
                        <div class="info-value">{{ $pembayaran->pelanggan->no_hp }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Alamat:</div>
                        <div class="info-value">{{ $pembayaran->pelanggan->alamat }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Penagih:</div>
                        <div class="info-value">
                            @if($pembayaran->penagih)
                                {{ $pembayaran->penagih->nama }}
                            @else
                                <span class="text-gray-400 italic">Belum ada penagih</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="payment-details">
                <div class="section-title">Detail Pembayaran</div>
                <div class="amount-row">
                    <span>Tagihan Internet {{ \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->translatedFormat('F Y') }}</span>
                    <span>Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</span>
                </div>
                <div class="amount-row">
                    <span><strong>TOTAL PEMBAYARAN</strong></span>
                    <span><strong>Rp {{ number_format((float)$pembayaran->jumlah, 0, ',', '.') }}</strong></span>
                </div>
            </div>

            @if($pembayaran->keterangan)
            <div style="margin-top: 20px;">
                <div class="section-title">Keterangan</div>
                <p style="color: #666; font-style: italic;">{{ $pembayaran->keterangan }}</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah menggunakan layanan {{ $companyProfile->official_name ?? 'BCM' }}</p>
            <p>Faktur ini dicetak pada {{ now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
