<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
        ]);

        return back()->with('success', 'Application sent successfully');
    }
}