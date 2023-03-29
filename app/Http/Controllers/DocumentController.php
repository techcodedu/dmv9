<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\File;
use App\Models\Routing;
use App\Models\User;
use App\Models\Department;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    //
    public function edit($document_id)
    {
        // Get the document with the specified ID
        $document = Document::find($document_id);
        // Get the list of departments
        $departments = Department::all();

        // Get the list of offices
        $offices = Office::all();

        return view('college.document_edit', ['document' => $document, 'departments' => $departments, 'offices' => $offices]);
    }
    public function view($document_id)
    {
        // Get the document with the specified ID
        $document = Document::find($document_id);

        // Get the latest routing for the document
        $latestRouting = $document->latestRouting();

        // Get the file details for the document
        $file = $document->files->first();

        // Get the to office name for the latest routing
        $toOffice = optional($latestRouting)->toOffice;

        // Get the from office name for the latest routing
        $fromOffice = optional($latestRouting)->fromOffice;

        return view('college.document_view', [
            'document' => $document,
            'latestRouting' => $latestRouting,
            'file' => $file,
            'toOffice' => $toOffice,
            'fromOffice' => $fromOffice,
        ]);
    }


}
