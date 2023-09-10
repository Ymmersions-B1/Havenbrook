@extends('layout')

@section('title', "Accueil")


@section('content')
<div class="text-center">
    <h1>The HavenBrook</h1>
</div>

<div class="card w-full mb-3">
    <div class="card-body">
        <div class="bomb-container">

            @include('components.bomb', ["message" => $message])

            <div id="flipdown" class="flipdown mb-3"></div>
        </div>
    </div>
</div>
<div class="row">
    @forelse ($classrooms as $room)
        @php
            $progress = $room->calculateOverallFoundPercentage();
        @endphp
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h1>{{ $room->name }}</h1>
                    <li class="list-group-item">Rooms : {{ $room->rooms->count() }} | Mates : {{ $room->rooms->pluck('mates')->flatten()->count() }}</li>
                    <li class="list-group-item">
                        <span>Progress :</span>
                        <div class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-animated progress-bar-striped bg-success" style="min-width: fit-content; width: {{ $progress }}%"><div class="px-2">{{ $progress }}%</div>
                        </div>
                    </li>
                    <div class="d-grid mt-3">
                        <a href="{{ route("classroom.show", $room) }}" class="btn btn-primary">View classroom</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h3>Any classroom yet</h3>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')

@endpush

@include("components.refresh")

@push('footer-scripts')
    <script src="https://unpkg.com/animejs@3.0.1/lib/anime.min.js"></script>
    <script src="https://pbutcher.uk/flipdown/js/flipdown/flipdown.js"></script>
    <script src="{{ asset("js/index.js") }}"></script>

    <script>
        let totalProgress = "";
        console.log("totalprogress", totalProgress);
        if (totalProgress >= 95) {
            document.getElementById("rupee").innerHTML = "You won 🎉"
        } else {
            let flipdown = new FlipDown({{$end}})
            .start()
            .ifEnded(() => startBomb());
        }
        
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://pbutcher.uk/flipdown/css/flipdown/flipdown.css">
    <link rel="stylesheet" href="{{ asset("css/index.css") }}">
@endpush

