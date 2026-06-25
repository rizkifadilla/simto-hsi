<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{   
    public function index()
    {
        $clients = Client::all();

        return view('pages.master.client.index', [
            'clients' => $clients,
            'type_menu' => 'master'
        ]);
    }

    public function create()
    {
        return view('pages.master.client.create', [
            'type_menu' => 'master'
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'check_in_time' => 'required',
            'check_out_time' => 'required',

            // optional
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'attendance_radius' => 'nullable|numeric|min:0',
        ]);
        try {

            Client::create([
                'name' => $request->name,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'attendance_radius' => $request->attendance_radius,
            ]);

            return redirect()
                ->route('clients.index')
                ->with('success', 'Client added successfully');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return view('pages.master.client.edit', [
            'client' => $client,
            'type_menu' => 'master'
        ]);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'contact_person' => 'required',
            'phone' => 'required',
            'check_in_time' => 'required',
            'check_out_time' => 'required',
        ]);
        
        try {

            $client = Client::findOrFail($id);

            $client->update([
                'name' => $request->name,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'attendance_radius' => $request->attendance_radius,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return redirect()
                ->route('clients.index')
                ->with('success', 'Client updated successfully');

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Client::destroy($id);

        return back()->with('success', 'The client was successfully deleted');
    }
}
