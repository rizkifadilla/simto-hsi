<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    // LIST LOWONGAN
    public function index()
    {
        $jobs = Job::where('is_active', 1)->latest()->get();

        return view('pages.public.index', compact('jobs'));
    }

    // DETAIL LOWONGAN
    public function show($slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        return view('pages.public.show', compact('job'));
    }

    // APPLY CV
    public function apply(Request $request)
    {
        $request->validate([
            'job_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'cv' => 'required|mimes:pdf|max:2048'
        ]);

        // upload CV
        $cvPath = $request->file('cv')->store('cv', 'public');

        // create / find applicant
        $applicant = Applicant::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'cv_file' => $cvPath
            ]
        );

        // cek duplicate apply
        $exists = Application::where('job_id', $request->job_id)
            ->where('applicant_id', $applicant->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah melamar di posisi ini');
        }

        // simpan lamaran
        Application::create([
            'job_id' => $request->job_id,
            'applicant_id' => $applicant->id,
            'cover_letter' => $request->cover_letter
        ]);

        return back()->with('success', 'Lamaran berhasil dikirim!');
    }
}