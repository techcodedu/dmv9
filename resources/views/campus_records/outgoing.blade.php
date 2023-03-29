@extends('layouts.dashlayout')
@section('title', 'OutGoing Records')

@section('styles')
<style>
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 60%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
  background-color: #fefefe;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #888;
  max-width: 50%;
  height: 90vh;
  overflow: auto;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}
#pdfFrame {
  height: calc(100vh - 80px);
}
.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}


</style>
@section('nav')
    <!-- Sidebar -->
    <div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-folder-open"></i>
                    <p>
                        Document
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('campus_records.incoming') }}" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Incoming Documents</p>
                        </a>     
                    </li>
                    <li class="nav-item">
                         <a href="{{ route('campus_records.outgoing') }}" class="nav-link">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Outgoing Documents</p>
                        </a>
                    </li>
                    <li class="nav-item">
                         <a href="{{ route('document_history') }}" class="nav-link">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Document History</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Settings</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-bell"></i>
                    <p>Notifications</p>
                </a>
            </li>
        </ul>
    </nav>
</div>

    <!-- /.sidebar -->
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            <h2>Outgoing Documents</h2>
            @if($message)
                <p>{{ $message }}</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Document ID</th>
                            <th>Document Type</th>
                            <th>Status</th>
                            <th>Forwarded To</th>
                            <th>Date Forwarded</th>
                            <th>File</th>
                            <!-- Add more columns as needed -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outgoingDocuments as $outgoingDocument)
                            <tr>
                                <td>{{ $outgoingDocument->document->document_id }}</td>
                                <td>{{ $outgoingDocument->document->document_type }}</td>
                                <td>{{ $outgoingDocument->status }}</td>
                                <td>{{ $outgoingDocument->toOffice->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($outgoingDocument->date_forwarded)->format('F j, Y \a\t g:i a') }}</td>
                                 <td>
                                   @foreach($outgoingDocument->document->files as $file)
                                        <a href="#" onclick="openModalAndDisplayPDF('{{ asset('documents/' . $file->filename) }}', '{{ $outgoingDocument->document->document_id }}')">Preview</a>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            <!-- Add the necessary closing HTML tags -->
            </div>
        </div>
    </div>
    {{-- modal --}}
    <div id="fileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <iframe id="pdfFrame" width="100%" height="500px"></iframe>
            <div class="forward-document mt-3">
                <form id="forwardDocumentForm" action="{{ route('campus_records.forward', '__DOCUMENT_ID__') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="remarks">Remarks:</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Forward</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const fileModal = document.getElementById('fileModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        const pdfFrame = document.getElementById('pdfFrame');

         function openModalAndDisplayPDF(fileUrl, documentId) {
            pdfFrame.setAttribute('src', fileUrl);
            fileModal.style.display = 'block';

            // Set the form action
            var forwardDocumentForm = document.getElementById('forwardDocumentForm');
            forwardDocumentForm.action = forwardDocumentForm.action.replace('__DOCUMENT_ID__', documentId);
        }

         closeBtn.onclick = function () {
            pdfFrame.setAttribute('src', '');
            fileModal.style.display = 'none';
        };

    </script>
    <script>
    const pdfFrame = document.getElementById('pdfFrame');

    pdfFrame.addEventListener('load', function() {
    const contentHeight = pdfFrame.contentWindow.document.body.scrollHeight;
    pdfFrame.style.height = contentHeight + 'px';
    });

    </script>
@endsection
