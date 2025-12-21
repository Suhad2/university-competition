@extends('layouts.app')

@section('title', 'Create Question - Admin Panel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Create New Question</h2>
    <a href="{{ route('admin.questions') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Questions
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Question Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.questions.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="title" class="form-label">Question Text *</label>
                        <textarea class="form-control @error('title') is-invalid @enderror" 
                                  id="title" 
                                  name="title" 
                                  rows="3" 
                                  required>{{ old('title') }}</textarea>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="option_a" class="form-label">Option A *</label>
                                <input type="text" 
                                       class="form-control @error('option_a') is-invalid @enderror" 
                                       id="option_a" 
                                       name="option_a" 
                                       value="{{ old('option_a') }}" 
                                       required>
                                @error('option_a')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="option_b" class="form-label">Option B *</label>
                                <input type="text" 
                                       class="form-control @error('option_b') is-invalid @enderror" 
                                       id="option_b" 
                                       name="option_b" 
                                       value="{{ old('option_b') }}" 
                                       required>
                                @error('option_b')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="option_c" class="form-label">Option C *</label>
                                <input type="text" 
                                       class="form-control @error('option_c') is-invalid @enderror" 
                                       id="option_c" 
                                       name="option_c" 
                                       value="{{ old('option_c') }}" 
                                       required>
                                @error('option_c')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="option_d" class="form-label">Option D *</label>
                                <input type="text" 
                                       class="form-control @error('option_d') is-invalid @enderror" 
                                       id="option_d" 
                                       name="option_d" 
                                       value="{{ old('option_d') }}" 
                                       required>
                                @error('option_d')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correct_answer" class="form-label">Correct Answer *</label>
                                <select class="form-select @error('correct_answer') is-invalid @enderror" 
                                        id="correct_answer" 
                                        name="correct_answer" 
                                        required>
                                    <option value="">Select the correct answer</option>
                                    <option value="A" {{ old('correct_answer') == 'A' ? 'selected' : '' }}>Option A</option>
                                    <option value="B" {{ old('correct_answer') == 'B' ? 'selected' : '' }}>Option B</option>
                                    <option value="C" {{ old('correct_answer') == 'C' ? 'selected' : '' }}>Option C</option>
                                    <option value="D" {{ old('correct_answer') == 'D' ? 'selected' : '' }}>Option D</option>
                                </select>
                                @error('correct_answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" 
                                       class="form-control @error('category') is-invalid @enderror" 
                                       id="category" 
                                       name="category" 
                                       value="{{ old('category') }}" 
                                       placeholder="e.g., Mathematics, Science, History">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional category for organizing questions</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.questions') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Section -->
<div class="row justify-content-center mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-eye"></i> Preview</h6>
            </div>
            <div class="card-body">
                <div id="questionPreview">
                    <p class="text-muted">Fill in the question details to see a preview...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Live preview functionality
function updatePreview() {
    const title = document.getElementById('title').value;
    const optionA = document.getElementById('option_a').value;
    const optionB = document.getElementById('option_b').value;
    const optionC = document.getElementById('option_c').value;
    const optionD = document.getElementById('option_d').value;
    const correctAnswer = document.getElementById('correct_answer').value;

    if (title && optionA && optionB && optionC && optionD) {
        const preview = `
            <h5>${title || 'Question title will appear here'}</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="option-preview ${correctAnswer === 'A' ? 'correct' : ''}">
                        <strong>A.</strong> ${optionA || 'Option A text'}
                    </div>
                    <div class="option-preview ${correctAnswer === 'B' ? 'correct' : ''}">
                        <strong>B.</strong> ${optionB || 'Option B text'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="option-preview ${correctAnswer === 'C' ? 'correct' : ''}">
                        <strong>C.</strong> ${optionC || 'Option C text'}
                    </div>
                    <div class="option-preview ${correctAnswer === 'D' ? 'correct' : ''}">
                        <strong>D.</strong> ${optionD || 'Option D text'}
                    </div>
                </div>
            </div>
            ${correctAnswer ? `<div class="mt-2"><small class="text-success"><strong>Correct Answer:</strong> ${correctAnswer}</small></div>` : ''}
        `;
        document.getElementById('questionPreview').innerHTML = preview;
    }
}

// Add event listeners to all form fields
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('input', updatePreview);
});

// Initialize preview
updatePreview();
</script>

<style>
.option-preview {
    padding: 8px 12px;
    margin: 4px 0;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: #f8f9fa;
}

.option-preview.correct {
    border-color: #28a745;
    background: #d4edda;
    color: #155724;
}
</style>
@endsection
@endsection
