{{-- 
    Question Card Partial
    
    Displays the current question with options and timer.
    This component is updated dynamically via Pusher events.
    
    Expected variables:
    - $question: Question model instance
    - $currentTest: Test model instance
    - $user: User model instance
--}}

<div id="question-container" class="d-none">
    <div class="card question-card">
        {{-- Card Header with Timer --}}
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Question</h5>
                <div class="timer-display" id="timer">35s</div>
            </div>
        </div>

        {{-- Card Body with Question Content --}}
        <div class="card-body">
            
            {{-- Question Text --}}
            <div class="question-content mb-4">
                <h4>{{ $question ? $question->title : 'Loading question...' }}</h4>
            </div>

            {{-- Answer Options Form --}}
            <div class="answer-options">
                <form id="answerForm" onsubmit="return false;">
                    @csrf
                    
                    {{-- Row 1: Options A and B --}}
                    <div class="row">
                        {{-- Option A --}}
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="A" onclick="QuizApp.actions.selectAnswer('A')">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">A</span>
                                    <span id="option-a">{{ $question ? $question->option_a : '...' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Option B --}}
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="B" onclick="QuizApp.actions.selectAnswer('B')">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">B</span>
                                    <span id="option-b">{{ $question ? $question->option_b : '...' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Options C and D --}}
                    <div class="row mt-3">
                        {{-- Option C --}}
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="C" onclick="QuizApp.actions.selectAnswer('C')">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">C</span>
                                    <span id="option-c">{{ $question ? $question->option_c : '...' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Option D --}}
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="D" onclick="QuizApp.actions.selectAnswer('D')">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">D</span>
                                    <span id="option-d">{{ $question ? $question->option_d : '...' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Status Message Area --}}
            <div class="text-center mt-4">
                <div id="statusMessage"></div>
            </div>

            {{-- Submit Button --}}
            <div class="text-center mt-4">
                <button type="button" id="submitAnswerBtn" class="btn btn-success btn-lg" 
                    onclick="QuizApp.actions.submitAnswer()" disabled>
                    <i class="fas fa-check"></i> Submit Answer
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden Inputs for State Management --}}
    <input type="hidden" id="questionId" value="{{ $question ? $question->id : '' }}">
    <input type="hidden" id="testId" value="{{ $currentTest ? $currentTest->id : '' }}">
    <input type="hidden" id="startTime" value="{{ $currentTest ? $currentTest->question_start_time : '' }}">
    <input type="hidden" id="timeLimit" value="35">
    <input type="hidden" id="hasAnswered" value="{{ $existingAnswer ? 'true' : 'false' }}">
    <input type="hidden" id="currentQuestionId" value="{{ $question ? $question->id : '' }}">
</div>