@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row">

            @if (Session::has('status'))
                <div class="alert alert-info">{{ Session::get('status') }}</div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif

            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2>File integrity</h2>
                    </div>
                    <div class="panel-body">

                        <p>
                            <p>File integrity checker found errors in the following files:</p>

                            <a href="{{ route('admin.integrity.delete.corrupted') }}" class="btn btn-danger">Delete all</a>
                        </p>

                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Uploaded</th>
                                    <th>CRC32 on upload</th>
                                    <th>CRC32 now</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                <tr>
                                    <td><a href="{{ route('file.get.code', $file->code) }}">{{ $file->code }}</a></td>
                                    <td>{{ $file->created_at }}</td>
                                    <td>{{ $file->crc }}</td>
                                    <td>{{ $file->currentCRC }}</td>
                                    <td>
                                        <a href="{{ route('admin.integrity.delete', $file->code) }}" class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection