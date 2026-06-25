<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::latest()->get();

        return view('pages.job.index', [
            'jobs' => $jobs,
            'type_menu' => 'career'
        ]);
    }

    public function create()
    {
        return view('pages.job.create', [
            'type_menu' => 'career'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'requirement' => 'required',
            'benefit' => 'required',
            'location' => 'required',
            'type' => 'required',
            'salary_min' => 'required|numeric',
            'salary_max' => 'required|numeric|gte:salary_min',
            'deadline' => 'required|date',
        ]);
        try {

            Job::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . time(),
                'description' => $request->description,
                'requirement' => $request->requirement,
                'benefit' => $request->benefit,
                'location' => $request->location,
                'type' => $request->type,
                'salary_min' => $request->salary_min,
                'salary_max' => $request->salary_max,
                'deadline' => $request->deadline,
                'is_active' => $request->is_active ? 1 : 0,
            ]);

            return redirect()->route('career.index')->with('success', 'Job added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return back()
                ->withInput()
                ->with('error', implode('<br>', $e->validator->errors()->all()));

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $job = Job::findOrFail($id);

        return view('pages.job.edit', [
            'job' => $job,
            'type_menu' => 'career'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'description' => trim(strip_tags($request->description)),
            'requirement' => trim(strip_tags($request->requirement)),
            'benefit' => trim(strip_tags($request->benefit)),
        ]);
        $request->validate([
            'title' => 'required',
            'location' => 'required',
            'type' => 'required',
            'description' => 'required',
            'requirement' => 'required',
            'benefit' => 'required',
            'salary_min' => 'required|numeric',
            'salary_max' => 'required|numeric|gte:salary_min',
            'deadline' => 'required|date',
            'is_active' => 'required',
        ]);
        try {
            $job = Job::findOrFail($id);

            $job->update([
                'title' => $request->title,
                'description' => $request->description,
                'requirement' => $request->requirement,
                'benefit' => $request->benefit,
                'location' => $request->location,
                'type' => $request->type,
                'salary_min' => $request->salary_min,
                'salary_max' => $request->salary_max,
                'deadline' => $request->deadline,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()
                ->route('career.index')
                ->with('success', 'Job updated successfully');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Job::destroy($id);
        return back()->with('success', 'Job successfully deleted');
    }

    public function applicants($id)
    {
        $job = Job::findOrFail($id);

        $applications = Application::with('applicant')
            ->where('job_id', $id)
            ->orderBy('created_at', 'desc') // terbaru
            ->get();

        return view('pages.job.applicants', [
            'job' => $job,
            'applications' => $applications,
            'type_menu' => 'career'
        ]);
    }

    public function updateApplication(Request $request, $id)
    {
        $app = Application::findOrFail($id);

        $app->notes = $request->notes;

        // follow up toggle
        if ($request->has('follow_up')) {
            $app->followed_up_at = now();
        } else {
            $app->followed_up_at = null;
        }

        $app->save();

        return back()->with('success', 'Data updated!');
    }
}