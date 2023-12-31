<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $tb_m_projects = Project::all();
        $tb_m_clients = Client::all();
        $keyword = $request->input('keyword');
        $selectedClient = $request->input('pilclient');
        $selectedStatus = $request->input('pilstatus');

        $filteredProjects = Project::when($keyword, function ($query) use ($keyword) {
            $query->where('project_name', 'like', "%$keyword%")
                ->orWhere('project_status', 'like', "%$keyword%")
                ->orWhereHas('client', function ($subQuery) use ($keyword) {
                    $subQuery->where('client_name', 'like', "%$keyword%");
                });
        })->get();

        if ($selectedClient) {
            $tb_m_projects->where('client_id', $selectedClient);
        }

        if ($selectedStatus) {
            $tb_m_projects->where('project_status', $selectedStatus);
        }

        
        $tb_m_projects->transform(function ($project) {
            $project->project_start = Carbon::parse($project->project_start)->format('d M Y');
            $project->project_end = Carbon::parse($project->project_end)->format('d M Y');
            return $project;
        });



        return view('project.index', compact('tb_m_projects', 'tb_m_clients'));
    }
    public function search(Request $request)
{
    $keyword = $request->input('keyword');
    $client = $request->input('pilclient');
    $projectStatus = $request->input('project_status');

    // Lakukan pencarian berdasarkan kriteria yang diinputkan
    // Gunakan kriteria tersebut untuk menyaring data proyek
    // Contoh:
    $projects = Project::when($keyword, function ($query) use ($keyword) {
            return $query->where('project_name', 'like', '%' . $keyword . '%');
        })
        ->when($client, function ($query) use ($client) {
            return $query->where('client_id', $client);
        })
        ->when($projectStatus, function ($query) use ($projectStatus) {
            return $query->where('project_status', $projectStatus);
        })
        ->get();

    // Ambil data client untuk dropdown
    $tb_m_clients = Client::all();

    // Tampilkan hasil pencarian ke tampilan
    return view('nama.tampilan', compact('projects', 'tb_m_clients'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tb_m_clients = Client::all();
        return view('project.create', compact('tb_m_clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required',
            'client_id' => 'required|integer',
            'project_start' => 'required|date',
            'project_end' => 'required|date',
            'project_status' => 'required',
        ]);

        $tb_m_projects = new Project;

        $tb_m_projects->project_name = $request->input('project_name');
        $tb_m_projects->client_id = $request->input('client_id');
        $tb_m_projects->project_start = $request->input('project_start');
        $tb_m_projects->project_end = $request->input('project_end');
        $tb_m_projects->project_status = $request->input('project_status');

        $tb_m_projects->save();

        // Redirect atau melakukan tindakan lainnya setelah penyimpanan
        return redirect('/project')->with('success', 'Project berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tb_m_projects = Project::find($id);
        $tb_m_clients = Client::all();

        return view('project.edit', compact('tb_m_projects', 'tb_m_clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'project_name' => 'required',
            'client_id' => 'required|integer',
            'project_start' => 'required|date',
            'project_end' => 'required|date',
            'project_status' => 'required',
        ]);

        $tb_m_project = Project::find($id);
        $tb_m_project->project_name = $request->input('project_name');
        $tb_m_project->client_id = $request->input('client_id');
        $tb_m_project->project_start = $request->input('project_start');
        $tb_m_project->project_end = $request->input('project_end');
        $tb_m_project->project_status = $request->input('project_status');
        $tb_m_project->save();

        return redirect('/project')->with('success', 'Project berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteSelected(Request $request)
    {
        $selectedProjects = $request->input('selected_projects');

        if ($selectedProjects) {
            Project::whereIn('id', $selectedProjects)->delete();

            return redirect()->back()->with('success', 'Selected projects deleted successfully.');
        } else {
            return redirect()->back()->withErrors('No projects selected for deletion.');
        }
    }

    public function destroy(Project $project,$id)
    {
        Project::destroy($id);
        return redirect('/project')->with('success','Data Berhasil Di Hapus');
    }
}
