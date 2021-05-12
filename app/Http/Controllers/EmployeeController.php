<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\DataTables\EmployeeDataTable;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(EmployeeDataTable $dataTable, Request $request)
    {
        if($request->ajax())
        {
            $employyes_w_company = DB::table('companies')
                                    ->rightJoin('employees', 'employees.company_id', '=', 'companies.id')
                                    ->get();;
            return DataTables::of($employyes_w_company)->addColumn('action', function ($id) {
                return '<button class="btn btn-primary" id="e'.$id->id.'">Edit</button>
                <button class="btn btn-danger" id="d'.$id->id.'">Delete</button>
                  '; })->make(true);
        }

        $companies = Company::orderBy('name','asc')->get();
        return view('employees.index', compact('companies'));
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
            'full_name' => 'required|string|',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'starts_with:3706|required|max:11|min:11',
            'company_id' => 'required',
        ]);
        Employee::create($validated);
        return response()->json([
            'data' => "Employee created"
          ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $employee = Employee::where('id', $id)->first();
        $validated = $request->validate([
            'full_name' => 'required|string|',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'starts_with:3706|required|max:11|min:11',
            'company_id' => 'required',
        ]);
        $employee->update($validated);
        $employee->save();
        return response()->json([
            'data' => "Employee updated"
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
        $employee = Employee::where('id', $id)->first();
        $employee->delete();
        return response()->json([
            'data' => "Deleted successfully!"
          ]);

    }
}
