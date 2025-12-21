@extends('layouts.app')

@section('title', 'Manage Questions - Admin Panel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list"></i> Manage Questions</h2>
    <div>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Question
        </a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import"></i> Import Excel
        </button>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Questions from Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.questions.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Excel File</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            Please upload an Excel file with columns: title, option_a, option_b, option_c, option_d, correct_answer, category
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Import Questions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($questions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Options</th>
                            <th>Correct Answer</th>
                            <th>Category</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                        <tr>
                            <td>{{ $question->id }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $question->title }}">
                                    {{ $question->title }}
                                </div>
                            </td>
                            <td>
                                <small>
                                    A: {{ Str::limit($question->option_a, 20) }}<br>
                                    B: {{ Str::limit($question->option_b, 20) }}<br>
                                    C: {{ Str::limit($question->option_c, 20) }}<br>
                                    D: {{ Str::limit($question->option_d, 20) }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $question->correct_answer }}</span>
                            </td>
                            <td>
                                @if($question->category)
                                    <span class="badge bg-info">{{ $question->category }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $question->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.questions.edit', $question) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" 
                                          action="{{ route('admin.questions.delete', $question) }}" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this question?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $questions->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No questions found</h5>
                <p class="text-muted">Start by creating your first question or importing from Excel.</p>
                <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Question
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
