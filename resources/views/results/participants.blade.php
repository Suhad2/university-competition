{{-- resources/views/results/participants.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="mb-4">Participants Results</h4>

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($participants as $i => $participant)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $participant->name }}</td>
                        <td>{{ $participant->email }}</td>
                        <td>
                            <a href="{{ route('results.participant.details', $participant) }}" class="btn btn-sm btn-primary">
                                View Answers
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
