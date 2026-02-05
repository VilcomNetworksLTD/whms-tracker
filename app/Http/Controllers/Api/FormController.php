<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    // READ (List All)
    public function index()
    {
        return response()->json(Form::all(), 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'title'          => 'required|string',
            'client_name'    => 'required|string',
            'date'           => 'required|date',
            'payment_method' => 'required|string',
            'amount_in'      => 'required|numeric',
            'fees'           => 'required|numeric',
        ]);

        $data = $request->all();
        
        $data['amount_out'] = (float)$request->amount_in - (float)$request->fees;

        $form = Form::create($data);
        return response()->json($form, 201);
    }

    // READ (Single Record)
    public function show($id)
    {
        $form = Form::findOrFail($id);
        return response()->json($form, 200);
    }

    // UPDATE (Allows editing any field)
    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        
        $data = $request->all();

        // If you change the money, it recalculates the total automatically
        if ($request->has('amount_in') || $request->has('fees')) {
            $in = $request->input('amount_in', $form->amount_in);
            $fees = $request->input('fees', $form->fees);
            $data['amount_out'] = (float)$in - (float)$fees;
        }

        $form->update($data);
        return response()->json($form, 200);
    }

    // DELETE
    public function destroy($id)
    {
        Form::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}