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

            $progressLevel = "danger";

            if ( $progress >= 40 && $progress <= 60) {
                $progressLevel = "warning";
            } else if ($progress > 60) {
                $progressLevel = "success";
            }

        @endphp
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h1>{{ $room->name }}</h1>
                    <li class="list-group-item mb-3">Rooms : {{ $room->rooms->count() }} | Mates : {{ $room->rooms->pluck('mates')->flatten()->count() }}</li>
                    <li class="list-group-item">
                        <span>Progress :</span>
                        <div class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height: 40px">
                            <div class="progress-bar progress-bar-animated progress-bar-striped bg-{{ $progressLevel }}" style="min-width: fit-content; width: {{ $progress }}%"><div class="px-2">{{ $progress }}%</div>
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

<div class="mt-5">
    <h1>Son On/Off</h1>
    <button id="soundToggle" class="btn btn-primary">Activer le son</button>
</div>
@endsection

@push('scripts')

@endpush


@push('footer-scripts')
    <script src="https://unpkg.com/animejs@3.0.1/lib/anime.min.js"></script>
    <script src="https://pbutcher.uk/flipdown/js/flipdown/flipdown.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        let sparkler = new Audio('{{ asset("sounds/sparkler.mp3") }}');
        let explosion = new Audio('{{ asset("sounds/explosion.mp3") }}');

        let soundEnabled = true;

        // Récupérez une référence au bouton
        const soundToggleButton = document.getElementById("soundToggle");

        // Ajoutez un gestionnaire d'événements au bouton pour activer/désactiver le son
        soundToggleButton.addEventListener("click", function () {
            soundEnabled = !soundEnabled; // Inverser l'état du son

            if (soundEnabled) {
                soundToggleButton.textContent = "Activer le son";
            } else {
                soundToggleButton.textContent = "Désactiver le son";
            }
        });

        function playSparkler() {
            if (soundEnabled) {
                console.log("S1 played");
                sparkler.play();
            }
        }

        function playBomb() {
            if (soundEnabled) {
                sparkler.pause();
                console.log("S2 played");
                explosion.play();
            }
        }
    </script>

    <script src="{{ asset("js/index.js") }}"></script>

    <script>
            let flipdown = new FlipDown({{ $end }})
            .start()
            .ifEnded(() => {
                if ({{ $totalProgress }} <= {{ env("WIN_MIN") }} ) {
                    startBomb()
                } 
            });

            let pusher = new Pusher('f121ba7d19d3efa9090d', {
                cluster: 'eu'
            });

            let channel = pusher.subscribe('my-channel');
            channel.bind('my-event', function(data) {
                if (data.message == "refresh" && {{ !$isEnded }}) {
                    window.location.reload();
                }
            });
    </script> 
@endpush

@push('styles')
    <link rel="stylesheet" href="https://pbutcher.uk/flipdown/css/flipdown/flipdown.css">
    <link rel="stylesheet" href="{{ asset("css/index.css") }}">
@endpush

