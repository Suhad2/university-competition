{{-- resources/views/results/details.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">

        <h4 class="mb-4">
            Results for:
            <span class="text-primary">{{ $user->name }}</span>
        </h4>

        <table class="table table-striped table-bordered">
            <thead class="table-dark text-center">
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Selected Answer</th>
                    <th>Correct Answer</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($answers as $i => $answer)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $answer->question->title }}</td>

                        <td class="text-center">
                            {{ $answer->selected_answer ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ $answer->question->correct_answer }}
                        </td>

                        <td class="text-center">
                            @if ($answer->is_correct)
                                <span class="badge bg-success">✔ Correct</span>
                            @else
                                <span class="badge bg-danger">✖ Wrong</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
