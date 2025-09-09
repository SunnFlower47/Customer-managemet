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

        // Get income data
        $totalPendapatan = $pembayaranQuery->clone()->where('status', 'lunas')->sum('jumlah');
        $totalTagihan = $pembayaranQuery->clone()->sum('jumlah');
        $belumBayar = $pembayaranQuery->clone()->where('status', 'belum_bayar')->sum('jumlah');

        // Get detailed income by status
        $pendapatanByStatus = $pembayaranQuery->clone()
            ->select('status', DB::raw('SUM(jumlah) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Get income by penagih
        $pendapatanByPenagih = $pembayaranQuery->clone()
            ->where('status', 'lunas')
            ->with('pelanggan.penagih:id,nama')
            ->select('pelanggan_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('pelanggan_id')
            ->get();

        // Get daily income for the month
        $pendapatanHarian = $pembayaranQuery->clone()
            ->where('status', 'lunas')
            ->whereNotNull('tanggal_bayar')
            ->select(DB::raw('DATE(tanggal_bayar) as tanggal'), DB::raw('SUM(jumlah) as total'))
            ->groupBy(DB::raw('DATE(tanggal_bayar)'))
            ->orderBy('tanggal')
            ->get();

        // Get recent payments
        $pembayaranTerbaru = $pembayaranQuery->clone()
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
        $pembayarans = $pembayaranQuery->with(['pelanggan', 'pelanggan.penagih'])->paginate(20);

        // Prepare summary data
        $summary = [
            'total_pendapatan' => $totalPendapatan,
            'pembayaran_lunas' => $pembayaranQuery->clone()->where('status', 'lunas')->count(),
            'pembayaran_belum_lunas' => $pembayaranQuery->clone()->where('status', 'belum_bayar')->count(),
            'total_pelanggan' => $pembayaranQuery->clone()->distinct('pelanggan_id')->count()
        ];

        // Prepare chart data
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthTotal = Pembayaran::where('bulan_tagihan', $i)
                ->where('tahun_tagihan', $tahun)
                ->where('status', 'lunas')
                ->sum('jumlah');

            $chartData[] = [
                'month' => \Carbon\Carbon::create(null, $i, 1)->format('M'),
                'amount' => $monthTotal,
                'height' => $monthTotal > 0 ? max(20, ($monthTotal / max($totalPendapatan, 1)) * 200) : 0
            ];
        }

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

        // Get expense data
        $totalPengeluaran = $pengeluaranQuery->clone()->sum('jumlah');

        // Get expenses by category
        $pengeluaranByKategori = Pengeluaran::whereMonth('tanggal_pengeluaran', $bulan)
            ->whereYear('tanggal_pengeluaran', $tahun)
            ->where('status', 'terkonfirmasi')
            ->select('kategori', DB::raw('SUM(jumlah) as total'))
            ->groupBy('kategori')
            ->get()
            ->pluck('total', 'kategori');

        // Get expenses by status
        $pengeluaranByStatus = Pengeluaran::whereMonth('tanggal_pengeluaran', $bulan)
            ->whereYear('tanggal_pengeluaran', $tahun)
            ->select('status', DB::raw('SUM(jumlah) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Get recent expenses
        $pengeluaranTerbaru = Pengeluaran::whereMonth('tanggal_pengeluaran', $bulan)
            ->whereYear('tanggal_pengeluaran', $tahun)
            ->with('user:id,name')
            ->orderBy('tanggal_pengeluaran', 'desc')
            ->limit(10)
            ->get();

        // Get monthly comparison
        $bulanSebelumnya = Carbon::create($tahun, $bulan, 1)->subMonth();
        $pengeluaranBulanSebelumnya = Pengeluaran::whereMonth('tanggal_pengeluaran', $bulanSebelumnya->month)
            ->whereYear('tanggal_pengeluaran', $bulanSebelumnya->year)
            ->where('status', 'terkonfirmasi')
            ->sum('jumlah');

        $persentasePerubahan = $pengeluaranBulanSebelumnya > 0
            ? (($totalPengeluaran - $pengeluaranBulanSebelumnya) / $pengeluaranBulanSebelumnya) * 100
            : 0;

        // Get pengeluarans for table
        $pengeluarans = $pengeluaranQuery->clone()->with('user')->paginate(20);

        // Prepare summary data
        $summary = [
            'total_pengeluaran' => $totalPengeluaran,
            'total_transaksi' => $pengeluaranQuery->clone()->count(),
            'rata_rata' => $pengeluaranQuery->clone()->avg('jumlah') ?? 0
        ];

        // Prepare chart data
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthTotal = Pengeluaran::whereMonth('tanggal_pengeluaran', $i)
                ->whereYear('tanggal_pengeluaran', $tahun)
                ->sum('jumlah');

            $chartData[] = [
                'month' => \Carbon\Carbon::create(null, $i, 1)->format('M'),
                'amount' => $monthTotal,
                'height' => $monthTotal > 0 ? max(20, ($monthTotal / max($totalPengeluaran, 1)) * 200) : 0
            ];
        }

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

        // Get income
        $totalPendapatan = $pembayaranQuery->clone()->where('status', 'lunas')->sum('jumlah');

        // Get expenses
        $totalPengeluaran = $pengeluaranQuery->clone()->sum('jumlah');

        // Calculate profit/loss
        $labaRugi = $totalPendapatan - $totalPengeluaran;
        $marginKeuntungan = $totalPendapatan > 0 ? ($labaRugi / $totalPendapatan) * 100 : 0;

        // Get monthly comparison
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

        // Prepare summary data
        $summary = [
            'total_pendapatan' => $totalPendapatan,
            'total_pengeluaran' => $totalPengeluaran,
            'laba_rugi' => $labaRugi,
            'margin_percentage' => $marginKeuntungan
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
        $maxPendapatan = max($allPendapatan);
        $maxPengeluaran = max($allPengeluaran);
        $maxLabaRugi = max($allLabaRugi);
        $globalMax = max($maxPendapatan, $maxPengeluaran, $maxLabaRugi);

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
            'tahun'
        ));
    }
}
