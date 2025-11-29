<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{

    /**
     * Display income report
     */
    public function pendapatan(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        // Base query for pembayarans
        $pembayaranQuery = Pembayaran::query();

        // If user is penagih, filter by their penagih_id
        if ($user->role === 'penagih') {
            $penagih = \App\Models\Penagih::where('user_id', $user->id)->first();
            if ($penagih) {
                $pembayaranQuery->where('penagih_id', $penagih->id);
            }
        }

        // Filter by month and year (only if specified)
        if ($bulan) {
            $pembayaranQuery->where('bulan_tagihan', $bulan);
        }
        if ($tahun) {
            $pembayaranQuery->where('tahun_tagihan', $tahun);
        }

        // Get income data - use the same query builder as pagination
        $totalPendapatan = (clone $pembayaranQuery)->where('status', 'lunas')->sum('jumlah');
        $totalTagihan = (clone $pembayaranQuery)->sum('jumlah');
        $belumBayar = (clone $pembayaranQuery)->where('status', 'belum_bayar')->sum('jumlah');

        // Get detailed income by status
        $pendapatanByStatus = (clone $pembayaranQuery)
            ->select('status', DB::raw('SUM(jumlah) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Get income by penagih
        $pendapatanByPenagih = (clone $pembayaranQuery)
            ->where('status', 'lunas')
            ->with('pelanggan.penagih:id,nama')
            ->select('pelanggan_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('pelanggan_id')
            ->get();

        // Get daily income for the month
        $pendapatanHarian = (clone $pembayaranQuery)
            ->where('status', 'lunas')
            ->whereNotNull('tanggal_bayar')
            ->select(DB::raw('DATE(tanggal_bayar) as tanggal'), DB::raw('SUM(jumlah) as total'))
            ->groupBy(DB::raw('DATE(tanggal_bayar)'))
            ->orderBy('tanggal')
            ->get();

        // Get recent payments
        $pembayaranTerbaru = (clone $pembayaranQuery)
            ->where('status', 'lunas')
            ->with(['pelanggan:id,nama,pppoe', 'pelanggan.penagih:id,nama'])
            ->orderBy('tanggal_bayar', 'desc')
            ->limit(10)
            ->get();

        // Get monthly comparison (previous month) - only if month and year are specified
        $pendapatanBulanSebelumnya = 0;
        if ($bulan && $tahun) {
            $bulanSebelumnya = Carbon::create($tahun, $bulan, 1)->subMonth();
            $pendapatanBulanSebelumnya = Pembayaran::where('bulan_tagihan', $bulanSebelumnya->month)
                ->where('tahun_tagihan', $bulanSebelumnya->year)
                ->where('status', 'lunas')
                ->sum('jumlah');
        }

        $persentasePerubahan = $pendapatanBulanSebelumnya > 0
            ? (($totalPendapatan - $pendapatanBulanSebelumnya) / $pendapatanBulanSebelumnya) * 100
            : 0;

        // Get pembayarans for table
        $pembayarans = $pembayaranQuery->with(['pelanggan', 'pelanggan.penagih'])
            ->orderBy('tahun_tagihan', 'desc')
            ->orderBy('bulan_tagihan', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Prepare summary data - use the same query builder as pagination
        $summary = [
            'total_pendapatan' => $totalPendapatan ?? 0,
            'pembayaran_lunas' => (clone $pembayaranQuery)->where('status', 'lunas')->count(),
            'pembayaran_belum_lunas' => (clone $pembayaranQuery)->where('status', 'belum_bayar')->count(),
            'total_pelanggan' => (clone $pembayaranQuery)->select('pelanggan_id')->distinct()->count('pelanggan_id')
        ];

        // Prepare chart data - use base query with filters
        $chartData = [];
        $maxChartValue = 0;

        for ($i = 1; $i <= 12; $i++) {
            $monthQuery = Pembayaran::query();

            // Apply user filter if penagih
            if ($user->role === 'penagih') {
                $penagih = \App\Models\Penagih::where('user_id', $user->id)->first();
                if ($penagih) {
                    $monthQuery->where('penagih_id', $penagih->id);
                }
            }

            $monthQuery->where('bulan_tagihan', $i);

            // Apply year filter if specified
            if ($tahun) {
                $monthQuery->where('tahun_tagihan', $tahun);
            }

            $monthTotal = $monthQuery->where('status', 'lunas')->sum('jumlah');
            $maxChartValue = max($maxChartValue, $monthTotal);

            $chartData[] = [
                'month' => \Carbon\Carbon::create(null, $i, 1)->format('M'),
                'amount' => $monthTotal,
                'height' => 0 // Will be calculated after we know max value
            ];
        }

        // Calculate heights based on max value
        foreach ($chartData as &$data) {
            $data['height'] = $data['amount'] > 0 ? max(20, ($data['amount'] / max($maxChartValue, 1)) * 200) : 0;
        }
        unset($data);

        return view('laporan.pendapatan', compact(
            'summary',
            'chartData',
            'pembayarans',
            'bulan',
            'tahun'
        ));
    }

    /**
     * Display expense report
     */
    public function pengeluaran(Request $request)
    {
        // Get filter parameters
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        // Base query for pengeluarans
        $pengeluaranQuery = Pengeluaran::query();

        // Filter by month and year (only if specified)
        if ($bulan) {
            $pengeluaranQuery->whereMonth('tanggal_pengeluaran', $bulan);
        }
        if ($tahun) {
            $pengeluaranQuery->whereYear('tanggal_pengeluaran', $tahun);
        }

        // Get expense data - use the same query builder as pagination
        $totalPengeluaran = (clone $pengeluaranQuery)->sum('jumlah');

        // Get expenses by category - use base query with filters
        $pengeluaranByKategori = (clone $pengeluaranQuery)
            ->where('status', 'terkonfirmasi')
            ->select('kategori', DB::raw('SUM(jumlah) as total'))
            ->groupBy('kategori')
            ->get()
            ->pluck('total', 'kategori');

        // Get expenses by status - use base query with filters
        $pengeluaranByStatus = (clone $pengeluaranQuery)
            ->select('status', DB::raw('SUM(jumlah) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Get recent expenses - use base query with filters
        $pengeluaranTerbaru = (clone $pengeluaranQuery)
            ->with('user:id,name')
            ->orderBy('tanggal_pengeluaran', 'desc')
            ->limit(10)
            ->get();

        // Get monthly comparison - only if month and year are specified
        $pengeluaranBulanSebelumnya = 0;
        $persentasePerubahan = 0;
        if ($bulan && $tahun) {
            $bulanSebelumnya = Carbon::create($tahun, $bulan, 1)->subMonth();
            $pengeluaranBulanSebelumnya = Pengeluaran::whereMonth('tanggal_pengeluaran', $bulanSebelumnya->month)
                ->whereYear('tanggal_pengeluaran', $bulanSebelumnya->year)
                ->where('status', 'terkonfirmasi')
                ->sum('jumlah');

            $persentasePerubahan = $pengeluaranBulanSebelumnya > 0
                ? (($totalPengeluaran - $pengeluaranBulanSebelumnya) / $pengeluaranBulanSebelumnya) * 100
                : 0;
        }

        // Get pengeluarans for table
        $pengeluarans = $pengeluaranQuery->with('user')
            ->orderBy('tanggal_pengeluaran', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Prepare summary data - use the same query builder as pagination
        $summary = [
            'total_pengeluaran' => $totalPengeluaran ?? 0,
            'total_transaksi' => (clone $pengeluaranQuery)->count(),
            'rata_rata' => (clone $pengeluaranQuery)->avg('jumlah') ?? 0
        ];

        // Prepare chart data - use base query with filters
        $chartData = [];
        $maxChartValue = 0;

        for ($i = 1; $i <= 12; $i++) {
            $monthQuery = Pengeluaran::query();

            $monthQuery->whereMonth('tanggal_pengeluaran', $i);

            // Apply year filter if specified
            if ($tahun) {
                $monthQuery->whereYear('tanggal_pengeluaran', $tahun);
            }

            $monthTotal = $monthQuery->sum('jumlah');
            $maxChartValue = max($maxChartValue, $monthTotal);

            $chartData[] = [
                'month' => \Carbon\Carbon::create(null, $i, 1)->format('M'),
                'amount' => $monthTotal,
                'height' => 0 // Will be calculated after we know max value
            ];
        }

        // Calculate heights based on max value
        foreach ($chartData as &$data) {
            $data['height'] = $data['amount'] > 0 ? max(20, ($data['amount'] / max($maxChartValue, 1)) * 200) : 0;
        }
        unset($data);

        return view('laporan.pengeluaran', compact(
            'summary',
            'chartData',
            'pengeluarans',
            'bulan',
            'tahun'
        ));
    }

    /**
     * Display profit/loss report
     */
    public function labaRugi(Request $request)
    {
        // Get filter parameters
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        // Base queries
        $pembayaranQuery = Pembayaran::query();
        $pengeluaranQuery = Pengeluaran::query();

        // Filter by month and year (only if specified)
        if ($bulan) {
            $pembayaranQuery->where('bulan_tagihan', $bulan);
        }
        if ($tahun) {
            $pembayaranQuery->where('tahun_tagihan', $tahun);
        }

        if ($bulan) {
            $pengeluaranQuery->whereMonth('tanggal_pengeluaran', $bulan);
        }
        if ($tahun) {
            $pengeluaranQuery->whereYear('tanggal_pengeluaran', $tahun);
        }

        // Get income - use the same query builder
        $totalPendapatan = (clone $pembayaranQuery)->where('status', 'lunas')->sum('jumlah');

        // Get expenses - use the same query builder
        $totalPengeluaran = (clone $pengeluaranQuery)->sum('jumlah');

        // Calculate profit/loss
        $labaRugi = $totalPendapatan - $totalPengeluaran;
        $marginKeuntungan = $totalPendapatan > 0 ? ($labaRugi / $totalPendapatan) * 100 : 0;

        // Get monthly comparison - only if month and year are specified
        $pendapatanBulanSebelumnya = 0;
        $pengeluaranBulanSebelumnya = 0;
        $labaRugiBulanSebelumnya = 0;
        $persentasePerubahan = 0;

        if ($bulan && $tahun) {
            $bulanSebelumnya = Carbon::create($tahun, $bulan, 1)->subMonth();
            $pendapatanBulanSebelumnya = Pembayaran::where('bulan_tagihan', $bulanSebelumnya->month)
                ->where('tahun_tagihan', $bulanSebelumnya->year)
                ->where('status', 'lunas')
                ->sum('jumlah');
            $pengeluaranBulanSebelumnya = Pengeluaran::whereMonth('tanggal_pengeluaran', $bulanSebelumnya->month)
                ->whereYear('tanggal_pengeluaran', $bulanSebelumnya->year)
                ->sum('jumlah');
            $labaRugiBulanSebelumnya = $pendapatanBulanSebelumnya - $pengeluaranBulanSebelumnya;

            $persentasePerubahan = $labaRugiBulanSebelumnya != 0
                ? (($labaRugi - $labaRugiBulanSebelumnya) / abs($labaRugiBulanSebelumnya)) * 100
                : 0;
        }

        // Prepare summary data - use the same query builder as calculations
        $summary = [
            'total_pendapatan' => $totalPendapatan ?? 0,
            'total_pengeluaran' => $totalPengeluaran ?? 0,
            'laba_rugi' => $labaRugi ?? 0,
            'margin_percentage' => $marginKeuntungan ?? 0
        ];

        // Prepare chart data
        $chartData = [];
        $allPendapatan = [];
        $allPengeluaran = [];
        $allLabaRugi = [];

        // First pass: collect all data to find global maximum
        for ($i = 1; $i <= 12; $i++) {
            $monthPendapatanQuery = Pembayaran::where('bulan_tagihan', $i)->where('status', 'lunas');
            $monthPengeluaranQuery = Pengeluaran::whereMonth('tanggal_pengeluaran', $i);

            // Apply year filter if specified
            if ($tahun) {
                $monthPendapatanQuery->where('tahun_tagihan', $tahun);
                $monthPengeluaranQuery->whereYear('tanggal_pengeluaran', $tahun);
            }

            $monthPendapatan = $monthPendapatanQuery->sum('jumlah');
            $monthPengeluaran = $monthPengeluaranQuery->sum('jumlah');
            $monthLabaRugi = $monthPendapatan - $monthPengeluaran;

            $allPendapatan[] = $monthPendapatan;
            $allPengeluaran[] = $monthPengeluaran;
            $allLabaRugi[] = abs($monthLabaRugi);
        }

        // Find global maximum for proper scaling
        $maxPendapatan = !empty($allPendapatan) ? max($allPendapatan) : 0;
        $maxPengeluaran = !empty($allPengeluaran) ? max($allPengeluaran) : 0;
        $maxLabaRugi = !empty($allLabaRugi) ? max($allLabaRugi) : 0;
        $globalMax = max($maxPendapatan, $maxPengeluaran, $maxLabaRugi, 1);

        // Second pass: create chart data with proper scaling
        for ($i = 1; $i <= 12; $i++) {
            $monthPendapatanQuery = Pembayaran::where('bulan_tagihan', $i)->where('status', 'lunas');
            $monthPengeluaranQuery = Pengeluaran::whereMonth('tanggal_pengeluaran', $i);

            // Apply year filter if specified
            if ($tahun) {
                $monthPendapatanQuery->where('tahun_tagihan', $tahun);
                $monthPengeluaranQuery->whereYear('tanggal_pengeluaran', $tahun);
            }

            $monthPendapatan = $monthPendapatanQuery->sum('jumlah');
            $monthPengeluaran = $monthPengeluaranQuery->sum('jumlah');
            $monthLabaRugi = $monthPendapatan - $monthPengeluaran;

            $chartData[] = [
                'month' => \Carbon\Carbon::create(null, $i, 1)->format('M'),
                'pendapatan' => $monthPendapatan,
                'pengeluaran' => $monthPengeluaran,
                'laba_rugi' => $monthLabaRugi,
                'pendapatan_height' => $monthPendapatan > 0 ? max(20, ($monthPendapatan / max($globalMax, 1)) * 200) : 0,
                'pengeluaran_height' => $monthPengeluaran > 0 ? max(20, ($monthPengeluaran / max($globalMax, 1)) * 200) : 0,
                'laba_rugi_height' => abs($monthLabaRugi) > 0 ? max(20, (abs($monthLabaRugi) / max($globalMax, 1)) * 200) : 0
            ];
        }

        // Prepare monthly data
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthPendapatanQuery = Pembayaran::where('bulan_tagihan', $i)->where('status', 'lunas');
            $monthPengeluaranQuery = Pengeluaran::whereMonth('tanggal_pengeluaran', $i);

            // Apply year filter if specified
            if ($tahun) {
                $monthPendapatanQuery->where('tahun_tagihan', $tahun);
                $monthPengeluaranQuery->whereYear('tanggal_pengeluaran', $tahun);
            }

            $monthPendapatan = $monthPendapatanQuery->sum('jumlah');
            $monthPengeluaran = $monthPengeluaranQuery->sum('jumlah');

            $monthLabaRugi = $monthPendapatan - $monthPengeluaran;
            $monthMargin = $monthPendapatan > 0 ? ($monthLabaRugi / $monthPendapatan) * 100 : 0;

            $monthlyData[] = [
                'bulan' => $i,
                'tahun' => $tahun,
                'pendapatan' => $monthPendapatan,
                'pengeluaran' => $monthPengeluaran,
                'laba_rugi' => $monthLabaRugi,
                'margin_percentage' => $monthMargin
            ];
        }

        return view('laporan.laba-rugi', compact(
            'summary',
            'chartData',
            'monthlyData',
            'bulan',
            'tahun',
            'persentasePerubahan',
            'globalMax'
        ));
    }
}
