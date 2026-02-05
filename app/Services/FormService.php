<?php

namespace App\Services;

use App\Models\Form;

class FormService
{
    
    public function createForm(array $data)
    {
        
        return Form::create($data);
    }

    
    public function getFormsByDomain($domainId)
    {
        return Form::where('domain_id', $domainId)->get();
    }

    
    public function updateForm($id, array $data)
    {
        $form = Form::findOrFail($id);
        $form->update($data);
        return $form;
    }

    
    public function deleteForm($id)
    {
        return Form::destroy($id);
    }
    public function getAllForms()
{
    return Form::all();
}
}