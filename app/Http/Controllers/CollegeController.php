<?php

namespace App\Http\Controllers;

use Log;
use App\Models\File;
use App\Models\User;
use App\Models\Office;
use App\Models\Routing;
use App\Models\Document;
use App\Models\Department;
use App\Models\DocumentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\IncomingDocumentNotification;




class CollegeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:college');
    }

    public function dashboard()
    {
        $user = Auth::user();
        return view('college.dashboard', compact('user'));
    }
    public function showCreateDocument()
    {
        $departments = Department::all();
        $offices = Office::all();
        $user = Auth::user();
    
        return view('college.show_create_document', compact('departments', 'offices','user'));
    }
    public function storeDocument(Request $request)
    {
        // Listen to SQL queries executed during the method's execution
        $messages = [
            'file.required' => 'Please upload a file',
            'file.mimes' => 'Please upload a PDF, DOC, or DOCX file',
            'file.max' => 'The file must be smaller than 2 MB',
            'file.duplicate' => 'This file has already been uploaded'
        ];

        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048'
        ], $messages);

        // Get the uploaded file
        $file = $request->file('file');

        // Generate a hash value for the file content
        $hash = hash_file('sha256', $file->path());

        // Check if a file with the same hash value already exists in the database
        $existingFile = File::where('hash', $hash)->first();

        if ($existingFile) {
            // Check if the file has already been forwarded to the selected office
            $existingRouting = Routing::where('document_id', $existingFile->document_id)
                                       ->where('to_office_id', $request->input('office_id'))
                                       ->first();
            if ($existingRouting) {
                // the file has already been forwarded to the selected office
                return redirect()->back()->withErrors(['file' => 'This file has already been forwarded to the selected office.'])->withInput()->with('showDuplicateFileModal', true);
            }
            
            // need to confirm to mam if the document with is same file is forwarded to different office under college will it 
            //save or not?
            // the file already exists
            return redirect()->back()->withErrors(['file' => 'Sorry,We cannot proceed to this routing, the document should be float to the routing according.'])->withInput();
        } else {
            // Extract the filename and extension
            $originalFilename = $file->getClientOriginalName();
            $filename = pathinfo($originalFilename, PATHINFO_FILENAME);
            $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);

            // Generate a unique filename for the file
            $uniqueFilename = $filename . '_' . time() . '.' . $extension;

            // Move the file to the public/documents directory
            $file->move(public_path('documents'), $uniqueFilename);

            // Create a new Document instance with the validated data
            $document = new Document([
                'document_type' => $request->input('document_type'),
                'status' => $request->input('status'),
                'current_owner_id' => Auth::user()->id,
                'date_created' => now(),
                'date_forwarded' => now(),
                'date_modified' => now(),
                'department_id' => Auth::user()->department_id
            ]);
   
            // Save the document to the database
            $document->save();

            // Create a new Routing instance
            $routing = new Routing([
                'document_id' => $document->document_id,
                'from_office_id' => Auth::user()->office_id,
                'to_office_id' => $request->input('office_id'),
                'forwarded_by_user_id' => Auth::user()->id,
                'status' => Routing::STATUS_FORWARDED,
                'date_forwarded' => now(),
            ]);
            
            // Validate the routing data
            $validator = Validator::make($routing->toArray(), [
                'document_id' => 'required|exists:documents,document_id',
                'from_office_id' => 'required|exists:offices,id',
                'to_office_id' => 'required|exists:offices,id',
                'forwarded_by_user_id' => 'required|exists:users,id',
                'status' => 'required|in:draft,forwarded,endorsed,approved,signed,released,received',

            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            // Set the document_id attribute of the Routing instance
            $routing->document_id = $document->document_id;

            // Save the routing to the database
            $routing->save();

            // Create a new DocumentHistory instance
            $documentHistory = new DocumentHistory([
                'document_id' => $document->document_id,
                'from_role' => Auth::user()->role, // Get the role of the authenticated user
                'to_role' => User::where('office_id', $request->input('office_id'))->first()->role, // Get the role of the user in the destination office
                'status' => $request->input('status'),
                'user_id' => Auth::user()->id,
            ]);

            // Save the document history to the database
            $documentHistory->save();

            // Create a new File instance
            $fileModel = new File([
                'document_id' => $document->document_id,
                'filename' => $uniqueFilename,
                'file_path' => 'public/documents/' . $uniqueFilename,
                'file_type' => $extension,
                'file_size' => filesize(public_path('documents/' . $uniqueFilename)), // Get the file size after moving it
                'hash' => $hash,
            ]);

            $fileModel->document_id = $document->document_id;
            // Save the file to the database
            $fileModel->save();

            
            // Send the notification to the users of the destination office
            // $users = User::where('office_id', $routing->to_office_id)->get();
            // foreach ($users as $user) {
            //     $user->notify(new IncomingDocumentNotification($routing));
            // }

            // Redirect back to the create document page with a success message
            return redirect()->route('college.show_create_document')->with('success', 'Document created successfully.');
        }
    }

        public function documentHistory()
        {
            // Get the current authenticated user
            $user = Auth::user();
        
            //get all document uploader by specific user
            $documents = Document::with(['latestRouting', 'department', 'files'])
                ->join('departments', 'documents.department_id', '=', 'departments.id')
                ->select('documents.*', 'departments.name AS department_name', 'documents.document_id AS document_id')
                ->where('current_owner_id', $user->id)
                ->orderBy('date_forwarded', 'desc')
                ->paginate(5);

            //Get all the documents uploaded by the current user as per deparment
            // $documents = $documents = Document::with(['latestRouting', 'department', 'files'])
            //           ->join('departments', 'documents.department_id', '=', 'departments.id')
            //           ->select('documents.*', 'departments.name AS department_name', 'documents.document_id AS document_id')
            //           ->where('department_id', $user->department_id)
            //           ->orderBy('date_forwarded', 'desc')
            //           ->paginate(5);

            //   gets all the documents regardless on deparment_id
            // $documents = Document::with(['latestRouting', 'department', 'files'])
            // ->join('departments', 'documents.department_id', '=', 'departments.id')
            // ->select('documents.*', 'departments.name AS department_name', 'documents.document_id AS document_id')
            // ->orderBy('date_forwarded', 'desc')
            // ->paginate(2);

            // Loop through each document and add additional information
            foreach ($documents as $document) {
                // Get the to office name and status of the latest routing
                $latestRouting = $document->latestRouting;
                if ($latestRouting) {
                    $toOffice = Office::find($latestRouting->to_office_id);
                    $document->to_office_name = $toOffice->name;
                    $document->status = $latestRouting->status;
                } else {
                    $document->to_office_name = '-';
                    $document->status = '-';
                }
        
                // Get the file details
                $file = $document->files->first();
                if ($file) {
                    $document->filename = $file->filename;
                } else {
                    $document->filename = '-';
                }
                // Format the date_forwarded field
                $dateForwarded = $latestRouting ? date('F j, Y \a\t g:i A', strtotime($latestRouting->date_forwarded)) : '-';
                $document->date_forwarded = $dateForwarded;

            }
        
            // Return the view with the documents data
            return view('college.document_history', ['documents' => $documents]);
        }
    
    // public function profile()
    // {
    //     $user = Auth::user();

    //     return view('college.profile', compact('user'));
    // }

    // public function updateProfile(Request $request)
    // {
    //     $user = Auth::user();

    //     // Update user profile data

    //     return redirect()->back()->with('success', 'Profile updated successfully.');
    // }

 
}
