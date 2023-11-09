<?php

namespace App\Http\Controllers;

use App\Models\Court_case;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourtCaseController extends Controller
{
    public function create(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'plaintiff_id' => 'required',
            'plaintiff_name' => 'required',
            'defendant_id' => 'required',
            'defendant_name' => 'required',
            'case_name' => 'required',
            'type_of_case' => 'required',
        ]);

        // Check validation failure and return errors if validation fails
        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new instance of the Court_case model
        $court_case = new Court_case();

        // Assign values from the request to the model's properties
        $court_case->key = $request->key;
        $court_case->plaintiff_id = $request->plaintiff_id;
        $court_case->plaintiff_name = $request->plaintiff_name;
        $court_case->defendant_id = $request->defendant_id;
        $court_case->defendant_name = $request->defendant_name;
        $court_case->case_name = $request->case_name;
        $court_case->type_of_case = $request->type_of_case;

        // Save the model to the database
        $court_case->save();

        // Assuming that storelog and storerandom_log are custom functions
        storelog($court_case['type_of_case'], 'Abraham', 'MacOs');
        storerandom_log($court_case->id, '45', $request->key);

        return response([
            'status' => 'success',
            'data' => $court_case,
        ]);
    }

    public function show()
    {
        $cases = Court_case::select('id', 'case_name','plaintiff_name','defendant_name','type_of_case','defendant_id','plaintiff_id')->get();
        return response([
            'status' => 'success',
            'cases' => $cases,
        ]);
    }
    public function change_status(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',

        ]);

        // Check validation failure and return errors if validation fails
        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $cases = Court_case::find($id);
        $cases ->status=$request->status;
        $cases->update();

        return response([
            'status' => 'success',
            'cases' => $cases,
        ]);
    }
    public function edit(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'plaintiff_id' => 'required',
            'plaintiff_name' => 'required',
            'defendant_id' => 'required',
            'defendant_name' => 'required',
            'case_name' => 'required',
            'type_of_case' => 'required',
        ]);

        // Check validation failure and return errors if validation fails
        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new instance of the Court_case model
        $court_case = Court_case::find($id);

        // Assign values from the request to the model's properties
        $court_case->key = $request->key;
        $court_case->plaintiff_id = $request->plaintiff_id;
        $court_case->plaintiff_name = $request->plaintiff_name;
        $court_case->defendant_id = $request->defendant_id;
        $court_case->defendant_name = $request->defendant_name;
        $court_case->case_name = $request->case_name;
        $court_case->type_of_case = $request->type_of_case;

        // Save the model to the database
        $court_case->update();

        // Assuming that storelog and storerandom_log are custom functions
        storelog($court_case['type_of_case'], 'Abraham', 'MacOs');
        storerandom_log($court_case->id, '45', $request->key);

        return response([
            'status' => 'success',
            'message' => 'Successfully updated',
            'data' => $court_case,
        ]);
    }
}
