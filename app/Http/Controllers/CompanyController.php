<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Employee;
use App\Mail\NewCompanyMail;
use App\DataTables\CompaniesDataTable;
use Yajra\DataTables\DataTables;
use App\Rules\FQDM;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CompaniesDataTable $dataTable, Request $request)
    {
        if($request->ajax())
        {
            $companies = Company::orderBy('name','desc')->get();
            return DataTables::of($companies)->make(true);
        }
        return view('company.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:companies,name',
            'email' => 'required|email',
            'website' => [
                            'required',
                            'unique:companies,website',
                            new FQDM(),
            ],
        ]);
        $company = Company::create($validated);
        
        //Send mail on registration (uzkomentuota kaip prasoma uzduotyje)
        //Mail::to($company->email)->send(new NewCompanyMail($company->name));

        return response()->json([
            'data' => $company->id
          ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $company = Company::where('id', $id)->get();
        $employees = Employee::where('company_id', $company[0]->id)->orderBy('full_name','desc')->get();
        $count = $employees->count();
        if($request->ajax())
        {
            return DataTables::of($employees)->addColumn('action', function ($id) {
               
                return '<button class="btn btn-danger" id="e'.$id->id.'">Delete</button>
                  '; })->make(true);
        }
        
        return view('company.show', compact('company', 'count'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
            return response()->json([ 'success' => true ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $company = Company::where('id', $id)->first();
        
            $validated = $request->validate([
                'name' => 'required|string|unique:companies,name,'.$company->id,
                'email' => 'required|email',
                'website' => [
                                'required',
                                new FQDM(),
                ],

            ]);
            $company->update($validated);
        $company->save();
        return response()->json([
            'data' => $company
          ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::where('id', $id)->first();
        $employees = Employee::where('company_id', $company->id)->first();
        if($employees === null)
        {
            $company->delete();
            return response()->json([
                'data' => 'Company deleted'
              ]);
        }
        else
        {
            return response()->json([
                'error' => "Not deletable. Company still has employees"
            ], 401);
        }
    }
    public function wipe($id)
    {
        $company = Company::where('id', $id)->first();
        Employee::where('company_id', $company->id)->delete();
        return response()->json([
            'data' => 'All employees wiped'
          ]);
    }
    public function image(Request $request, $id)
    {
        $company = Company::where('id', $request->id)->first();
        $image = $request->file('logo_slug');
        $name = $image->getClientOriginalName();
        $name_no_extension = trim(explode('.', $name)[0]);
        $extension = strtolower($image->getClientOriginalExtension());
        $file = 'public/'.$name;
            
        $i = 1;
        while(Storage::exists($file))
        {
            $file = 'public/'.$name_no_extension.$i.'.'.$extension;
            $name = $name_no_extension.$i.'.'.$extension;
            $i++;
        }
        $company->logo_slug = $name;
        $company->save();
        Storage::put('public/'.$name,file_get_contents($image));
        return response()->json([
            'data' => $company->id
        ]);
    }
}
