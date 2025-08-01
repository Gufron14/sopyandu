<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Weighing;
use App\Models\PregnancyCheck;
use App\Models\Immunization;

class LaporanController extends Controller
{
    public function index()
    {
        return view('dashboard.laporan');
    }

    // Cetak Penimbangan Anak
    public function cetakPenimbangan(Request $request)
    {
        $query = Weighing::with('familyChildren');

        // Default periode jika tidak diisi
        $early_period = $request->from ?? now()->startOfMonth()->toDateString();
        $final_period = $request->to ?? now()->toDateString();

        if ($request->from && $request->to) {
            $query->whereBetween('weighing_date', [$request->from, $request->to]);
        }

        $weighings = $query->get();

        // Filter status gizi jika dipilih
        $nutrition_status = $request->status_gizi ?? 'Semua';
        if ($request->status_gizi) {
            $weighings = $weighings->filter(function ($item) use ($request) {
                $status = $item->calculateWeightForAge()['status'] ?? null;
                return $status === $request->status_gizi;
            });
        }

        // Set nutrition_status ke 'Semua' jika kosong
        if (!$nutrition_status) {
            $nutrition_status = 'Semua';
        }

        return view('dashboard.service.weighing.report', [
            'weighings' => $weighings,
            'early_period' => $early_period,
            'final_period' => $final_period,
            'nutrition_status' => $nutrition_status,
        ]);
    }

    // Cetak Pemeriksaan Kehamilan
    public function cetakKehamilan(Request $request)
    {
        // Default periode jika tidak diisi
        $early_period = $request->from ?? now()->startOfMonth()->toDateString();
        $final_period = $request->to ?? now()->toDateString();

        $query = PregnancyCheck::with('familyParents');

        if ($request->from && $request->to) {
            $query->whereBetween('check_date', [$request->from, $request->to]);
        }

        // if ($request->status_vaksin) {
        //     $query->where('status_vaksin', $request->status_vaksin);
        // }

        $pregnancy_checks = $query->get();

        // Filter by BMI status if provided
        if ($request->status_bmi) {
            $pregnancy_checks = $pregnancy_checks->filter(function ($item) use ($request) {
                return $item->getBMIStatus() === $request->status_bmi;
            });
        }

        return view('dashboard.service.pregnancy-check.report', [
            'pregnancy_checks' => $pregnancy_checks,
            'early_period' => $early_period,
            'final_period' => $final_period,
            // 'vaccine_status' => $request->status_vaksin ?? 'Semua',
            'bmi_status' => $request->status_bmi ?? 'Semua',
        ]);
    }

    // Cetak Imunisasi Anak

    public function cetakImunisasi(Request $request)
    {
        // Default periode jika tidak diisi
        $early_period = $request->from ?? now()->startOfMonth()->toDateString();
        $final_period = $request->to ?? now()->toDateString();

        $query = Immunization::with('familyChildren');

        if ($request->from && $request->to) {
            $query->whereBetween('immunization_date', [$request->from, $request->to]);
        }

        if ($request->status_vaksin) {
            $query->where('status_vaksin', $request->status_vaksin);
        }

        $immunizations = $query->get();

return view('dashboard.service.immunization.report', [
    'immunizations' => $immunizations,
    'early_period' => $early_period,
    'final_period' => $final_period,
    'vaccine_status' => $request->status_vaksin ?? 'Semua', // ubah dari 'status_vaksin' ke 'vaccine_status'
]);
    }
}
