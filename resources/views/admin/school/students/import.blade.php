@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content-inner">
    <!-- Page Title & Breadcrumbs -->
    <div class="dashboard-breadcrumbs">
        <h1 class="dashboard-title">Import Students from CSV/Excel</h1>
        <div class="breadcrumbs">
            <span>Home</span> <span class="breadcrumb-sep">/</span> 
            <span><a href="{{ route('admin.school.students.index') }}">Students</a></span> <span class="breadcrumb-sep">/</span> 
            <span class="breadcrumb-active">Import</span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Import Results -->
    @if (session('import_results'))
        @php $results = session('import_results'); @endphp
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Import Results</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $results['success'] }}</div>
                    <div class="text-sm text-green-700">Successfully Imported</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ $results['failed'] }}</div>
                    <div class="text-sm text-red-700">Failed</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ count($results['credentials']) }}</div>
                    <div class="text-sm text-blue-700">Credentials Generated</div>
                </div>
            </div>

            @if (count($results['errors']) > 0)
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-2">Errors (showing first 50):</h4>
                    <div class="bg-red-50 border border-red-200 rounded p-4 max-h-64 overflow-y-auto">
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach(array_slice($results['errors'], 0, 50) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            @if(count($results['errors']) > 50)
                                <li class="text-gray-600">... and {{ count($results['errors']) - 50 }} more errors</li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif

            @if (count($results['credentials']) > 0)
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-2">Student Credentials (showing first 100):</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded p-4 max-h-96 overflow-y-auto">
                        <div class="flex justify-end mb-2">
                            <button onclick="downloadCredentials()" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                <i class="fas fa-download mr-1"></i> Download All Credentials
                            </button>
                        </div>
                        <table class="min-w-full text-sm">
                            <thead class="bg-blue-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">Name</th>
                                    <th class="px-3 py-2 text-left">Email</th>
                                    <th class="px-3 py-2 text-left">Phone</th>
                                    <th class="px-3 py-2 text-left">Reg. Number</th>
                                    <th class="px-3 py-2 text-left">Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_slice($results['credentials'], 0, 100) as $cred)
                                <tr class="border-b border-blue-200">
                                    <td class="px-3 py-2">{{ $cred['name'] }}</td>
                                    <td class="px-3 py-2">{{ $cred['email'] ?? 'N/A' }}</td>
                                    <td class="px-3 py-2">{{ $cred['phone_number'] }}</td>
                                    <td class="px-3 py-2">{{ $cred['registration_number'] ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 font-mono text-xs">{{ $cred['password'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($results['credentials']) > 100)
                            <p class="text-xs text-gray-600 mt-2">... and {{ count($results['credentials']) - 100 }} more students</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Instructions -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">How to Import Students</h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
            <li>Download the template using the button below (CSV or Excel format)</li>
            <li>Fill in the student information in the file</li>
            <li>Required fields: <strong>First Name</strong>, <strong>Last Name</strong>, <strong>Phone Number</strong>, <strong>Level (O Level or A Level)</strong></li>
            <li><strong>Combination</strong> is required for A Level students or students in S.5/S.6 (e.g., PCM/ICT, BCM/ICT)</li>
            <li>Optional fields: Middle Name, Email, Registration Number, Class, Date of Birth</li>
            <li>Save the file as CSV or Excel format (.xlsx, .xls)</li>
            <li>Upload the file using the form below</li>
            <li>Review the import results and save the credentials</li>
        </ol>
        <p class="mt-3 text-sm text-gray-600">
            Acceptable values for level: <strong>O Level</strong>, <strong>A Level</strong> (case-insensitive). We also accept UCE/UACE equivalents.
        </p>
        <div class="mt-4 flex gap-2 flex-wrap">
            <a href="{{ route('admin.school.students.import.template', ['format' => 'excel']) }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-file-excel mr-2"></i> Download Excel Template
            </a>
            <a href="{{ route('admin.school.students.import.template', ['format' => 'csv']) }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-file-csv mr-2"></i> Download CSV Template
            </a>
        </div>
    </div>

    <!-- Import Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.school.students.import.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="csv_file" class="block text-sm font-medium text-gray-700">Import File *</label>
                    <input type="file" 
                           id="csv_file" 
                           name="csv_file" 
                           accept=".csv,.txt,.xlsx,.xls"
                           required
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">Maximum file size: 10MB. Supports CSV, TXT, Excel (.xlsx, .xls) formats.</p>
                    @error('csv_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="generate_passwords" 
                               value="1"
                               checked
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Generate secure passwords automatically (12 characters)</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">If unchecked, a default 8-character password will be used</p>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Important:</strong> Make sure to save the student credentials after import. 
                                They will be displayed on this page but cannot be retrieved later. 
                                Duplicate phone numbers or emails will be skipped.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.school.students.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-upload mr-2"></i> Import Students
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function downloadCredentials() {
    @if(session('import_results') && count(session('import_results.credentials')) > 0)
        const credentials = @json(session('import_results.credentials'));
        let csv = 'Name,Email,Phone Number,Registration Number,Password\n';
        
        credentials.forEach(cred => {
            csv += `"${cred.name}","${cred.email || ''}","${cred.phone_number}","${cred.registration_number || ''}","${cred.password}"\n`;
        });
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'student_credentials_' + new Date().getTime() + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    @endif
}
</script>
@endsection

