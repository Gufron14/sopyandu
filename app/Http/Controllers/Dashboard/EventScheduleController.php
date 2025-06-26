<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EventSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EventScheduleController extends Controller
{
    private function checkOfficerPosition()
    {
        $user = Auth::user();

        $officer = $user->officers;
        $parent  = $user->role === 'family_parent';

        // Periksa apakah posisi pejabat adalah 'Lurah' atau 'Kepala Lingkungan' atau jika role 'family_parent'
        if (($officer && ($officer->position === 'Lurah' || $officer->position === 'Kepala Lingkungan')) || $parent) {
            abort(403, 'Unauthorized');
        }
    }

    // Fungsi untuk menghapus cache yang berkaitan dengan event schedule
    protected function clearEventScheduleCache()
    {
        $now = now();
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();
        $tomorrow = $now->copy()->addDay()->toDateString();
        $currentYear = $now->year;
        $currentWeek = $now->format('o_W');
        $currentMonth = $now->format('Y_m');

        $keys = [
            "schedules_today_{$today}",
            "schedules_yesterday_{$yesterday}",
            "schedules_tomorrow_{$tomorrow}",
            "schedules_all_{$currentYear}",
            "schedules_weekly_{$currentWeek}",
            "schedules_monthly_{$currentMonth}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all'); // default: all
        $now = now();

        // Generate cache key sesuai filter
        $cacheKey = match ($filter) {
            'weekly' => 'schedules_weekly_' . $now->format('o_W'),
            'monthly' => 'schedules_monthly_' . $now->format('Y_m'),
            default => 'schedules_all_' . $now->year,
        };

        $schedules = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filter, $now) {
            $query = EventSchedule::with(['officers']);

            switch ($filter) {
                case 'weekly':
                    // Gunakan awal minggu Senin dan akhir minggu Minggu
                    $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
                    $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);

                    $query->whereBetween('event_date', [$startOfWeek, $endOfWeek]);
                    break;

                case 'monthly':
                    $query->whereYear('event_date', $now->year)
                        ->whereMonth('event_date', $now->month);
                    break;

                default:
                    $query->whereYear('event_date', $now->year);
            }

            return $query->orderBy('event_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->orderBy('end_time', 'asc')
                ->get();
        });

        return view('dashboard.schedule.index', compact('schedules', 'filter'));
    }

    public function getScheduleByDay($day)
    {
        $date = match ($day) {
            'today' => Carbon::now()->startOfDay(),
            'yesterday' => Carbon::now()->subDay()->startOfDay(),
            'tomorrow' => Carbon::now()->addDay()->startOfDay(),
            default => Carbon::now()->startOfDay()
        };

        $schedules = Cache::remember("schedules_{$day}_" . $date->toDateString(), now()->addMinutes(10), function () use ($date) {
            return EventSchedule::whereDate('event_date', $date)->orderBy('start_time', 'asc')->get();
        });

        $html = view('components.schedule-list', compact('schedules'))->render();

        return response()->json([
            'html' => $html
        ]);
    }

    public function show($id)
    {
        $schedule = EventSchedule::with(['officers'])->findOrFail($id);

        return view('dashboard.schedule.show', compact('schedule'));
    }

    public function create()
    {
        $this->checkOfficerPosition();

        return view('dashboard.schedule.create');
    }

    public function store(Request $request)
    {
        $this->checkOfficerPosition();

        $rules = [
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'event_location' => 'required|string|max:255',
            'description' => 'nullable',
            'officer_id' => 'required',
        ];

        $messages = [
            'title.required' => 'Nama kegiatan wajib diisi.',
            'title.max' => 'Nama kegiatan maksimal 255 karakter.',
            'event_date.required' => 'Tanggal kegiatan wajib diisi.',
            'event_date.date' => 'Tanggal kegiatan harus berupa tanggal yang valid.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required' => 'Waktu selesai wajib diisi.',
            'end_time.after' => 'Waktu selesai harus lebih setelah waktu mulai.',
            'event_location.required' => 'Lokasi wajib diisi.',
            'event_location.string' => 'Lokasi harus berupa teks.',
            'event_location.max' => 'Lokasi maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        $data = $request->validate($rules, $messages);

        try {
            EventSchedule::create([
                'title' => $data['title'],
                'event_date' => $data['event_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'event_location' => $data['event_location'],
                'description' => $data['description'] ?? null,
                'officer_id' => $data['officer_id'],
            ]);

            // Hapus chace
            $this->clearEventScheduleCache();

            return redirect(url('/schedule'))->with('success', "Jadwal berhasil ditambahkan.");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Jadwal gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $this->checkOfficerPosition();

        $schedule = EventSchedule::findOrFail($id);

        return view('dashboard.schedule.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $schedule = EventSchedule::findOrFail($id);

        $rules = [
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'event_location' => 'required|string|max:255',
            'description' => 'nullable',
            'officer_id' => 'required',
        ];

        $messages = [
            'title.required' => 'Nama kegiatan wajib diisi.',
            'title.max' => 'Nama kegiatan maksimal 255 karakter.',
            'event_date.required' => 'Tanggal kegiatan wajib diisi.',
            'event_date.date' => 'Tanggal kegiatan harus berupa tanggal yang valid.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required' => 'Waktu selesai wajib diisi.',
            'end_time.after' => 'Waktu selesai harus lebih setelah waktu mulai.',
            'event_location.required' => 'Lokasi wajib diisi.',
            'event_location.string' => 'Lokasi harus berupa teks.',
            'event_location.max' => 'Lokasi maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        $data = $request->validate($rules, $messages);

        try {
            $schedule->update([
                'title' => $data['title'],
                'event_date' => $data['event_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'event_location' => $data['event_location'],
                'description' => $data['description'] ?? null,
                'officer_id' => $data['officer_id'],
            ]);

            // Hapus chace
            $this->clearEventScheduleCache();

            return redirect(url('/schedule'))->with('success', "Jadwal berhasil diperbarui.");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Jadwal gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $this->checkOfficerPosition();

        $schedule = EventSchedule::findOrFail($id);

        $schedule->delete();

        // Hapus chace
        $this->clearEventScheduleCache();

        return redirect(url('/schedule'))->with('success', 'Jadwal berhasil dihapus.');
    }
}
