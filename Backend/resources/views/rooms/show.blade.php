@extends('layout')

@section('title', $room->title)

@section('content')
<a href="{{ route("home") }}" class="btn btn-primary mb-3"><- Back</a>

<h1>{{ $room->title }}</h1>
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="progress" role="progressbar" style="height: 30px" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-animated progress-bar-striped bg-success" style="min-width: fit-content; width: {{ $progress}}%"><div class="px-2">{{ $progress }}%</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <h4>Mates</h4>
                </div>
                <hr>
                @forelse ($room->mates as $mate)
                    <form action="{{ route("mate.destroy", $mate) }}" method="POST" class="d-flex gap-3 mb-3">
                        <input type="text" class="form-control" disabled value="{{ $mate->name }}">
                        @csrf
                        @method("DELETE")
                        <button type="submit" class="btn btn-danger">x</button>
                    </form>
                @empty
                    <h6>Any mates yet !</h6>
                @endforelse
                <hr>
                <form action="{{ route("mate.store") }}" method="POST" class="d-flex gap-3 mb-3">
                    @csrf
                    <input type="text" name="room" value="{{ $room->id }}" hidden>
                    <input type="text" class="form-control" placeholder="Username" name="name" value="{{ old("username") }}" required min="2">
                    <button type="submit" class="btn btn-success">+</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-grid mb-3">
                    <a href="{{ asset("storage/export/" . $room->file) }}" class="btn btn-primary" download><i class="bi bi-download"></i> Download file</a>
                </div>
                <hr>
                <h4>Codes :</h4>

                <div class="row">
                    @foreach ($foundedCodes as $code)
                        <div class="col-md-3">
                            <div class=" mb-3">
                                <input @disabled($code->founded) type="text" class="form-control" value="{{ $code->founded ?  $code->code : "" }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($progress < 100)
                    <form action="{{ route("room.check", $room) }}" method="post">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="code" required min="2">
                            <button  class="btn btn-outline-success" type="submit">Test code</button>
                        </div>
                    </form>
                @else
                    <div class="d-flex justify-content-center">
                        <img src="https://i.giphy.com/media/fah08IDMr10VtDrcoh/giphy.webp" class="img-fluid" style="border-radius: 10px">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

@endpush

@push('footer-scripts')

@endpush

@push('styles')
 
@endpush

