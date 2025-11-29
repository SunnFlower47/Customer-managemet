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

        /* Add top margin to prevent button overlap on mobile */
        @media (max-width: 768px) {
            body {
                padding-top: 80px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 70px;
            }
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
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            transition: all 0.3s ease;
        }

        /* Mobile responsive buttons */
        @media (max-width: 768px) {
            .action-buttons {
                position: absolute;
                top: 10px;
                left: 10px;
                right: 10px;
                flex-direction: row;
                gap: 8px;
                border-radius: 8px;
                z-index: 9999;
                padding: 10px;
                justify-content: center;
            }

            .print-button, .download-button {
                flex: 1;
                justify-content: center;
                padding: 12px 15px;
                font-size: 14px;
                min-height: 44px;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            .action-buttons {
                position: absolute;
                top: 5px;
                left: 5px;
                right: 5px;
                padding: 8px;
                gap: 6px;
                z-index: 9999;
            }

            .print-button, .download-button {
                padding: 10px 12px;
                font-size: 13px;
                min-height: 40px;
            }
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
            transition: all 0.3s ease;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }

        .print-button:focus, .download-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }

        .print-button:active, .download-button:active {
            transform: translateY(1px) scale(0.98);
        }

        .print-button:hover, .download-button:hover {
            transform: translateY(-1px);
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
        <a href="{{ route('pembayarans.invoice.pdf', $pembayaran) }}" class="download-button">
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
                    $companyName = $companyProfile->official_name ?? 'PCM.net';
                    $companyInitials = $companyProfile->initials ?? 'PCM';
                @endphp
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-details">
                    {{ $companyInitials }} WiFi Billing Management System<br>
                    {{ $companyProfile->alamat ?? 'Jl. Raya Teknologi No. 123, Jakarta' }}<br>
                    Telp: {{ $companyProfile->nomor_kontak ?? '(021) 1234-5678' }} | Email: {{ $companyProfile->email_support ?? 'info@pcm.net' }}
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
            <p>Terima kasih telah menggunakan layanan {{ $companyProfile->official_name ?? 'PCM.net' }}</p>
            <p>Faktur ini dicetak pada {{ now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
