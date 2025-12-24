<div id="question-container" class="question-card">
    <div class="card question-card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Question</h5>
                <div class="timer-display" id="timer">35s</div>
            </div>
        </div>
        <div class="card-body">
            <div class="question-content mb-4">
                <h4>{{ $question->title }}</h4>
            </div>

            <div class="answer-options">
                <form id="answerForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="A">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">A</span>
                                    <span>{{ $question->option_a }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="B">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">B</span>
                                    <span>{{ $question->option_b }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="C">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">C</span>
                                    <span>{{ $question->option_c }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="answer-option" data-answer="D">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3" style="font-size: 1.2rem;">D</span>
                                    <span>{{ $question->option_d }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="text-center mt-4">
                <div id="statusMessage"></div>
            </div>

            <div class="text-center mt-4">
                <button type="button" id="submitAnswerBtn" class="btn btn-success btn-lg"
                    onclick="submitAnswer()">
                    <i class="fas fa-check"></i> Submit Answer
                </button>
            </div>
        </div>
    </div>

    <input type="hidden" id="questionId" value="{{ $question->id }}">
    <input type="hidden" id="testId" value="{{ $currentTest->id }}">
    <input type="hidden" id="startTime" value="{{ $currentTest->question_start_time }}">
    <input type="hidden" id="timeLimit" value="35">
    <input type="hidden" id="currentQuestionId" value="{{ $question->id }}">
</div>
