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
        Client::create($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'Client added successfully');
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
        $client = Client::findOrFail($id);
        $client->update($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'The client was successfully updated');
    }

    public function destroy($id)
    {
        Client::destroy($id);

        return back()->with('success', 'The client was successfully deleted');
    }
}
