<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Routing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\User;
use App\Models\Office;
use Yajra\DataTables\Facades\DataTables;
use App\Events\MessageNotification;





use App\Models\File;



class CampusRecordsController extends Controller
{
        public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:campus_records');
    }

    public function dashboard()
    {
        $user = Auth::user();

        return view('campus_records.dashboard', compact('user'));

    }
    // INCOMING DOCUMENTS
    public function incomingDocuments()
    {
        // Fetch incoming documents
        $incomingDocuments = Routing::where('to_office_id', Auth::user()->office_id)
                                    ->where('status', 'forwarded')
                                    ->where('forwarded_by_user_id', '!=', Auth::user()->id)
                                    ->whereNotIn('document_id', function($query) {
                                        $query->select('document_id')
                                            ->from('routing')
                                            ->where('forwarded_by_user_id', Auth::user()->id)
                                            ->where('status', 'forwarded');
                                    })
                                    ->with(['document.department', 'forwardedBy', 'fromOffice', 'document.files'])
                                    ->get();

        if ($incomingDocuments->isEmpty()) {
            // If there are no incoming documents, display a message
            $message = 'There are no documents forwarded to your office yet.';
        } else {
            // If there are incoming documents, set the message to null
            $message = null;

        // Trigger an event for each new document
        foreach ($incomingDocuments as $routing) {
            event(new MessageNotification($routing));
        }

        }

        // Pass the documents and message (if any) to the view
        return view('campus_records.incoming', ['incomingDocuments' => $incomingDocuments, 'message' => $message]);
    }

    public function downloadFile($id)
    {
        // Find the requested file
        $file = File::findOrFail($id);

        // Define the path to the file
        $path = public_path('documents/' . $file->filename);

        // Check if the file exists
        if (file_exists($path)) {
            // Download the file
            return response()->download($path, $file->filename);
        } else {
            // Return an error if the file is not found
            return abort(404, 'File not found');
        }
    }

    // FORWARDING DOCUMENTS
    public function forwardDocument(Request $request,$id)
    {
        $document = Document::findOrFail($id);

        $campus_extension_office_id = 2;
        $campus_extension_office = Office::findOrFail($campus_extension_office_id);
        $to_role = $campus_extension_office->name;

        // Create a new routing record for this document
        $new_routing = new Routing([
            'document_id' => $document->document_id,
            'from_office_id' => Auth::user()->office_id,
            'to_office_id' => $campus_extension_office_id,
            'forwarded_by_user_id' => Auth::user()->id,
            'status' => 'forwarded',
            'remarks' => $request->input('remarks'),
            'date_forwarded' => now(),
        ]);

        $new_routing->save();

        // Create a new document history record
       
        $document_history = new DocumentHistory([
            'document_id' => $document->document_id,
            'from_role' => Auth::user()->role, // Get the role of the authenticated user
            'to_role' => $to_role,
            'status' => 'forwarded',
            'user_id' => Auth::user()->id  
        ]);
       
        $document_history->save();

        // Update the document status
        $document->status = 'forwarded';
        $document->date_forwarded = now();
        $document->save();

        return redirect()->back()->with('success', 'Document forwarded successfully.');
    }

    // OUTGOING DOCUMENTS
    public function outgoingDocuments()
    {
        // Fetch outgoing documents
        $outgoingDocuments = Routing::where('forwarded_by_user_id', Auth::user()->id)
                                ->with(['document.department', 'forwardedBy', 'toOffice', 'document.files'])
                                ->orderBy('created_at', 'desc')
                                ->get();

        if ($outgoingDocuments->isEmpty()) {
            // If there are no outgoing documents, display a message
            $message = 'You have not forwarded any documents yet.';
        } else {
            // If there are outgoing documents, set the message to null
            $message = null;
        }

        // Pass the documents and message (if any) to the view
        return view('campus_records.outgoing', ['outgoingDocuments' => $outgoingDocuments, 'message' => $message]);
    }

    // document history
    public function documentHistory()
    {
        return view('campus_records.document_history');
    }
    public function documentHistoryData()
    {
        $routing = Routing::with(['document.department', 'forwardedBy', 'fromOffice', 'toOffice'])
                    ->where('from_office_id', Auth::user()->office_id)
                    ->orWhere('to_office_id', Auth::user()->office_id)
                    ->orderBy('date_forwarded', 'desc')
                    ->get();


        return DataTables::of($routing)
            ->editColumn('status', function ($routing) {
                return ucfirst($routing->status);
            })
            ->editColumn('document.document_id', function ($routing) {
                return $routing->document->document_id;
            })
            ->editColumn('fromOffice.name', function ($routing) {
                return $routing->fromOffice->name;
            })
            ->editColumn('toOffice.name', function ($routing) {
                return $routing->toOffice->name;
            })
            ->editColumn('forwardedBy.name', function ($routing) {
                return $routing->forwardedBy->name;
            })
            ->editColumn('document.department.name', function ($routing) {
                return $routing->document->department->name;
            })
            ->editColumn('date_forwarded', function ($routing) {
                return $routing->date_forwarded;
            })
            ->editColumn('date_endorsed', function ($routing) {
                return $routing->date_endorsed;
            })
            ->editColumn('date_approved', function ($routing) {
                return $routing->date_approved;
            })
            ->editColumn('date_signed', function ($routing) {
                return $routing->date_signed;
            })
            ->editColumn('date_released', function ($routing) {
                return $routing->date_released;
            })
            ->make(true);
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
