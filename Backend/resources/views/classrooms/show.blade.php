@extends('layout')

@section('title', $classroom->name)

@section('content')
<a href="{{ route("home") }}" class="btn btn-primary mb-3"><- Back</a>
<div class="d-flex justify-content-between mb-3">
    <h1>{{ $classroom->name }}</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal" style="height: fit-content">Create a room</button>
</div>
<div class="row">
    @forelse ($classroom->rooms as $room)
    @php
        $progress = $room->calculateFoundPercentage();
    @endphp

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h3>{{ $room->title }}</h3>
                <div class="progress mb-2" role="progressbar" aria-label="Success striped example" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-animated progress-bar-striped bg-success" style="min-width: fit-content; width: {{ $progress }}%"><div class="px-2">{{ $progress }}%</div></div>
                </div>

                @foreach ($room->mates as $mate)
                    <span>{{ $mate->name }} | </span>
                @endforeach

                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route("room.show", $room) }}" class="btn btn-primary">View room</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h3>Any room yet</h3>
            </div>
        </div>
    </div>
    @endforelse
</div>
  
  <!-- Modal -->
  <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <form action="{{ route("room.store") }}" method="post">
        @csrf
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="createModalLabel">Create a new room on {{ $classroom->name }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route("room.store") }}" method="post">
                        @csrf

                        <input type="text" hidden name="classroom" value="{{ $classroom->id }}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="title" required>
                    </div>
                    
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" type="submit">Create !</button>
                </div>
            </div>
        </div>
    </form>
  </div>
@endsection

@push('scripts')

@endpush

@push('footer-scripts')

@endpush

@push('styles')
 
@endpush

