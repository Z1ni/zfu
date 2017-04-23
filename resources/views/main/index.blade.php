@extends("layouts.app")

@section("content")

    <div class="container">
        @if (Session::has('status'))
            <div class="alert alert-info">{{ Session::get('status') }}</div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
    </div>

    <div class="panel panel-default file-list-panel">
        <div class="panel-body">
            @forelse ($files as $file)
                <div class="up-file {{ !$file->visible ? 'up-file-hidden' : '' }}">
                    <div class="up-file-zoomable">
                        <a href="{{ route('file.get', [$file->code, explode('.', $file->location, 2)[1]]) }}" target="_blank">
                            <img src="{{ route('file.thumb', $file->code) }}">
                        </a>
                        @if (Auth::check())
                            {{-- Show controls only for users --}}
                            <div class="up-file-controls btn-group btn-group-justified" role="group">
                                @if ($file->visible)
                                    <a href="{{ route('file.hide', $file->code) }}" class="btn btn-default" title="Hide"><span class="glyphicon glyphicon-eye-close"></span></a>
                                @else
                                    <a href="{{ route('file.show', $file->code) }}" class="btn btn-success" title="Show"><span class="glyphicon glyphicon-eye-open"></span></a>
                                @endif
                                <a href="{{ route('file.trash', $file->code) }}" class="btn btn-danger" title="Trash"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                            </div>
                        @endif
                    </div>
                    <div class="up-file-info-container">
                        <div class="up-file-info">
                            <table class="table table-condensed">
                                <tr>
                                    <td>Uploaded:</td>
                                    <td>{{ $file->created_at }} ({{ $file->created_at->diffForHumans() }})</td>
                                </tr>
                                <tr>
                                    <td>Type:</td>
                                    <td>{{ $file->mimetype }}</td>
                                </tr>
                                <tr>
                                    <td>Views:</td>
                                    <td>{{ $file->views }}</td>
                                </tr>
                                <tr>
                                    <td>Size:</td>
                                    <td>{{ BytesConverter::bytesToHuman($file->currentRealSize()) }}</td>
                                </tr>
                                @if ($file->type == 'image' || $file->type == 'video')
                                    <tr>
                                        <td>Dimensions:</td>
                                        <td>{{ $file->width }}x{{ $file->height }}{{ ($file->type == 'video') ? ' @ '.$file->vid_fps.' FPS ('.$file->vid_codec.')' : '' }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <p style="text-align: center;">No uploads!</p>
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