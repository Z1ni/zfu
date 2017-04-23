@extends("layouts.app")

@section("content")

    @if (Session::has('status'))
        <div class="alert alert-info">{{ Session::get('status') }}</div>
    @endif
    @if (Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="panel panel-default file-list-panel">
        <div class="panel-heading text-center">
            <h3><span class="glyphicon glyphicon-trash"></span> Recycle bin</h3>
            @if ($files->count() > 0)
                <a href="{{ route('trash.restore') }}" class="btn btn-info" title="Restore all">Restore all</a>
                <a href="{{ route('trash.delete') }}" class="btn btn-danger" title="Delete all">Delete all</a>
            @endif
        </div>
        <div class="panel-body">
            @forelse ($files as $file)
                <div class="up-file">
                    <div class="up-file-zoomable">
                        <a href="{{ route('file.get', [$file->code, explode('.', $file->location, 2)[1]]) }}" target="_blank">
                            <img src="{{ route('file.thumb', $file->code) }}">
                        </a>
                        @if (Auth::check())
                            {{-- Show controls only for users --}}
                            <div class="up-file-controls btn-group btn-group-justified" role="group">
                                <a href="{{ route('file.restore', $file->code) }}" class="btn btn-success" title="Restore"><span class="glyphicon glyphicon-save"></span></a>
                                <a href="{{ route('file.delete', $file->code) }}" class="btn btn-danger" title="Delete"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p style="text-align: center;">No trash!</p>
            @endforelse
        </div>
    </div>

    {{-- Display pagination if necessary --}}
    @if ($files->total() > $files->perPage())
        <div class="panel panel-default file-list-pagination-panel text-center">
            {{ $files->links() }}
        </div>
    @endif

@endsection