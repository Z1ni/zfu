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
                        <h2>File upload</h2>
                    </div>
                    <div class="panel-body">

                        <form method="POST" action="{{ route('file.upload') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="file">File</label>
                                <input type="file" id="file" name="file">
                            </div>

                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection