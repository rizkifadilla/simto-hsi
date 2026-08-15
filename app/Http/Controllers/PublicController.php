<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class PublicController extends Controller
{
    // LIST LOWONGAN
    public function index(Request $request)
    {
        $query = Job::query();

        // SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // FILTER TYPE (multi)
        if ($request->type) {
            $query->whereIn('type', $request->type);
        }

        // STATUS
        if ($request->status == 'active') {
            $query->where('is_active', true);
        }

        // SORT
        if ($request->sort == 'deadline') {
            $query->orderBy('deadline', 'asc');
        } elseif ($request->sort == 'salary') {
            $query->orderBy('salary_max', 'desc');
        } else {
            $query->latest();
        }

        $jobs = $query->paginate(6)->withQueryString();

        return view('pages.public.index', compact('jobs'));
    }

    // DETAIL LOWONGAN
    public function show($slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        return view('pages.public.show', compact('job'));
    }

    // APPLY CV
    // public function apply(Request $request)
    // {
    //     $request->validate([
    //         'job_id' => 'required',
    //         'name' => 'required',
    //         'email' => 'required|email',
    //         'cv' => 'required|mimes:pdf|max:2048'
    //     ]);

    //     // upload CV
    //     $cvPath = $request->file('cv')->store('cv', 'public');

    //     // create / find applicant
    //     $applicant = Applicant::firstOrCreate(
    //         ['email' => $request->email],
    //         [
    //             'name' => $request->name,
    //             'phone' => $request->phone,
    //             'address' => $request->address,
    //             'cv_file' => $cvPath
    //         ]
    //     );

    //     // cek duplicate apply
    //     $exists = Application::where('job_id', $request->job_id)
    //         ->where('applicant_id', $applicant->id)
    //         ->exists();

    //     if ($exists) {
    //         return back()->with('error', 'Kamu sudah melamar di posisi ini');
    //     }

    //     // simpan lamaran
    //     Application::create([
    //         'job_id' => $request->job_id,
    //         'applicant_id' => $applicant->id,
    //         'cover_letter' => $request->cover_letter
    //     ]);

    //     return back()->with('success', 'Lamaran berhasil dikirim!');
    // }
    public function apply(Request $request)
    {
        // ======================
        // VALIDASI
        // ======================
        $request->validate([
            'job_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'g-recaptcha-response' => 'required'
        ]);

        // ======================
        // VERIFY RECAPTCHA V3
        // ======================
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );
        // $response = Http::withoutVerifying()
        //     ->asForm()
        //     ->post(
        //         'https://www.google.com/recaptcha/api/siteverify',
        //         [
        //             'secret' => config('services.recaptcha.secret_key'),
        //             'response' => $request->input('g-recaptcha-response'),
        //             'remoteip' => $request->ip(),
        //         ]
        //     );

        $result = $response->json();

        // ======================
        // VALIDASI SCORE
        // ======================
        if (
            !$result['success'] ||
            $result['score'] < 0.5 ||
            $result['action'] !== 'submit'
        ) {
            return back()->with('error', 'Detected as a bot!');
        }

        // ======================
        // SIMPAN CV
        // ======================
        $cvPath = $request->file('cv')->store('cv', 'public');

        // ======================
        // SIMPAN APPLICANT
        // ======================
        $applicant = Applicant::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'cv_file' => $cvPath,
            ]
        );

        // ======================
        // CEK DUPLIKAT
        // ======================
        $exists = Application::where('job_id', $request->job_id)
            ->where('applicant_id', $applicant->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have applied for this vacancy');
        }

        // ======================
        // SIMPAN APPLICATION
        // ======================
        Application::create([
            'job_id' => $request->job_id,
            'applicant_id' => $applicant->id,
            'cover_letter' => $request->cover_letter,
            'status' => 'submitted',
        ]);

        return back()->with('success', 'Application sent successfully');
    }

    // =========================================================
    // TRACKING APPLICATION
    // HALAMAN AWAL
    // =========================================================

    public function tracking()
    {
        return view('pages.public.tracking');
    }


    // =========================================================
    // KIRIM OTP
    // =========================================================

    public function sendTrackingOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);


        $email = strtolower(trim($request->email));


        // =====================================================
        // CEK APAKAH EMAIL PERNAH APPLY
        // =====================================================

        $applicant = Applicant::whereRaw(
            'LOWER(email) = ?',
            [$email]
        )->first();


        if (!$applicant) {

            return back()
                ->withInput()
                ->with('error', 'No application was found for this email address.');
        }


        // =====================================================
        // GENERATE OTP
        // =====================================================

        $otp = random_int(100000, 999999);


        // =====================================================
        // SIMPAN OTP DI SESSION
        //
        // Tidak ada database tambahan
        // =====================================================

        session([
            'tracking_email' => $email,

            'tracking_otp' => Hash::make((string) $otp),

            'tracking_otp_expires_at' => now()
                ->addMinutes(5)
                ->timestamp,

            'tracking_otp_attempts' => 0,
        ]);


        // =====================================================
        // KIRIM EMAIL
        // =====================================================

        try {

            Mail::send(
                'emails.tracking-otp',
                [
                    'otp' => $otp,
                    'expires' => 5,
                ],
                function ($message) use ($email) {

                    $message
                        ->to($email)
                        ->subject('Application Tracking OTP');
                }
            );

        } catch (\Throwable $e) {

            // Hapus OTP kalau email gagal dikirim
            session()->forget([
                'tracking_email',
                'tracking_otp',
                'tracking_otp_expires_at',
                'tracking_otp_attempts',
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to send OTP. Please try again later.'
                );
        }


        // =====================================================
        // PINDAH KE HALAMAN OTP
        // =====================================================

        return redirect()
            ->route('public.tracking.otp')
            ->with(
                'success',
                'OTP has been sent to your email address.'
            );
    }


    // =========================================================
    // HALAMAN INPUT OTP
    // =========================================================

    public function trackingOtp()
    {
        if (!session('tracking_email')) {

            return redirect()
                ->route('public.tracking')
                ->with(
                    'error',
                    'Please enter your email address first.'
                );
        }


        return view('pages.public.tracking-otp');
    }


    // =========================================================
    // VERIFY OTP
    // =========================================================

    public function verifyTrackingOtp(Request $request)
    {
        $request->validate([
            'otp' => [
                'required',
                'digits:6'
            ]
        ]);


        // =====================================================
        // CEK SESSION
        // =====================================================

        $email = session('tracking_email');

        $hashedOtp = session('tracking_otp');

        $expiresAt = session('tracking_otp_expires_at');

        $attempts = session('tracking_otp_attempts', 0);


        if (!$email || !$hashedOtp || !$expiresAt) {

            return redirect()
                ->route('public.tracking')
                ->with(
                    'error',
                    'Your OTP session has expired. Please request a new OTP.'
                );
        }


        // =====================================================
        // CEK EXPIRED
        // =====================================================

        if (now()->timestamp > $expiresAt) {

            session()->forget([
                'tracking_email',
                'tracking_otp',
                'tracking_otp_expires_at',
                'tracking_otp_attempts',
            ]);

            return redirect()
                ->route('public.tracking')
                ->with(
                    'error',
                    'OTP has expired. Please request a new OTP.'
                );
        }


        // =====================================================
        // BATASI ATTEMPT
        // =====================================================

        if ($attempts >= 5) {

            session()->forget([
                'tracking_email',
                'tracking_otp',
                'tracking_otp_expires_at',
                'tracking_otp_attempts',
            ]);

            return redirect()
                ->route('public.tracking')
                ->with(
                    'error',
                    'Too many incorrect attempts. Please request a new OTP.'
                );
        }


        // =====================================================
        // VERIFY OTP
        // =====================================================

        if (!Hash::check((string) $request->otp, $hashedOtp)) {

            session([
                'tracking_otp_attempts' => $attempts + 1
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid OTP. Please check your email and try again.'
                );
        }


        // =====================================================
        // OTP VALID
        // =====================================================

        session()->forget([
            'tracking_otp',
            'tracking_otp_expires_at',
            'tracking_otp_attempts',
        ]);


        // Tandai session sudah terverifikasi
        session([
            'tracking_verified' => true,
        ]);


        // =====================================================
        // REDIRECT KE HASIL TRACKING
        // =====================================================

        return redirect()
            ->route('public.tracking.result');
    }


    // =========================================================
    // HASIL TRACKING
    // =========================================================

    public function trackingResult()
    {
        // =====================================================
        // HARUS SUDAH VERIFY OTP
        // =====================================================

        if (
            !session('tracking_verified') ||
            !session('tracking_email')
        ) {

            return redirect()
                ->route('public.tracking')
                ->with(
                    'error',
                    'Please verify your email first.'
                );
        }


        $email = session('tracking_email');


        // =====================================================
        // AMBIL APPLICANT
        // =====================================================

        $applicant = Applicant::whereRaw(
            'LOWER(email) = ?',
            [$email]
        )->first();


        if (!$applicant) {

            session()->forget([
                'tracking_email',
                'tracking_verified',
            ]);

            return redirect()
                ->route('public.tracking')
                ->with(
                    'error',
                    'Application data could not be found.'
                );
        }


        // =====================================================
        // AMBIL SEMUA APPLICATION
        // =====================================================

        $applications = Application::with('job')
            ->where('applicant_id', $applicant->id)
            ->latest()
            ->get();


        return view(
            'pages.public.tracking-result',
            compact(
                'applicant',
                'applications'
            )
        );
    }


    // =========================================================
    // LOGOUT / RESET TRACKING
    // =========================================================

    public function trackingLogout()
    {
        session()->forget([
            'tracking_email',
            'tracking_verified',
            'tracking_otp',
            'tracking_otp_expires_at',
            'tracking_otp_attempts',
        ]);


        return redirect()
            ->route('public.tracking')
            ->with(
                'success',
                'Tracking session has been closed.'
            );
    }
}