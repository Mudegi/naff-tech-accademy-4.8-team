@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div class="flex items-center mb-4 sm:mb-0">
            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Students</h1>
                <p class="text-gray-600 mt-1">Manage your school's student records</p>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
                <a href="{{ route('admin.school.students.import') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-file-import mr-2"></i>
                    Import CSV
                </a>
                <a href="{{ route('admin.school.students.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Student
                </a>
            @endif
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('admin.school.students.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone, reg #..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                <select name="class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                <select name="level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Levels</option>
                    @foreach($levels as $value => $label)
                        <option value="{{ $value }}" {{ request('level') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                @if(request('search') || request('status') || request('level') || request('class_id'))
                    <a href="{{ route('admin.school.students.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Students Content -->
    @if(isset($students) && count($students))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user-graduate text-blue-600 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500">Reg: {{ $student->student->registration_number ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student->email ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $student->phone_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <div>Class: {{ $student->student->class ?? 'N/A' }}</div>
                                    <div>Level: {{ $student->student->level ?? 'Unspecified' }}</div>
                                    <div>Combo: {{ $student->student->combination ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
                                        <a href="{{ route('admin.school.students.edit', $student->id) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.school.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(method_exists($students, 'hasPages') && $students->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    @elseif(isset($studentsByClass) && count($studentsByClass))
        @foreach($classes as $class)
            @php $classStudents = $studentsByClass->get($class->id, collect()); @endphp
            @if($classStudents->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ $class->name }} ({{ $classStudents->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($classStudents as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-user-graduate text-blue-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                            <div class="text-xs text-gray-500">Reg: {{ $student->student->registration_number ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $student->email ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->phone_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div>Level: {{ $student->student->level ?? 'Unspecified' }}</div>
                                        <div>Combo: {{ $student->student->combination ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
                                            <a href="{{ route('admin.school.students.edit', $student->id) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.school.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @endforeach
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-graduate text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No students found</h3>
            <p class="text-gray-500 mb-6">Get started by adding your first student or importing from a CSV file.</p>
            @if(Auth::user()->isSchoolAdmin() || Auth::user()->isDirectorOfStudies())
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('admin.school.students.import') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-file-import mr-2"></i>
                        Import CSV
                    </a>
                    <a href="{{ route('admin.school.students.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Student
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
function copyPassword(elementId) {
    const passwordElement = document.getElementById(elementId);
    const password = passwordElement.textContent;
    
    navigator.clipboard.writeText(password).then(() => {
        // Show temporary feedback
        const btn = event.target.closest('.copy-password-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = '#10b981';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
        }, 2000);
    });
}

function copyNewCredentials() {
    const credentials = {
        name: '{{ session("new_student_credentials.name") ?? "" }}',
        email: '{{ session("new_student_credentials.email") ?? "" }}',
        phone: '{{ session("new_student_credentials.phone") ?? "" }}',
        regNumber: '{{ session("new_student_credentials.registration_number") ?? "" }}',
        password: document.getElementById('new-password').textContent
    };
    
    const text = `Student Login Credentials\n\nName: ${credentials.name}\nEmail: ${credentials.email || 'N/A'}\nPhone: ${credentials.phone}\nRegistration Number: ${credentials.regNumber || 'N/A'}\nPassword: ${credentials.password}`;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Credentials copied to clipboard!');
    });
}

function copyUpdatedCredentials() {
    const credentials = {
        name: '{{ session("updated_student_credentials.name") ?? "" }}',
        email: '{{ session("updated_student_credentials.email") ?? "" }}',
        password: document.getElementById('updated-password').textContent
    };
    
    const text = `Updated Student Password\n\nName: ${credentials.name}\nEmail: ${credentials.email || 'N/A'}\nNew Password: ${credentials.password}`;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Password copied to clipboard!');
    });
}
</script>
@endsection
