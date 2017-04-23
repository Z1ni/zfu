@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row">

            @if (Session::has('status'))
                <div class="alert alert-info">{{ Session::get('status') }}</div>
            @endif

            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2>Control panel</h2>
                    </div>
                    <div class="panel-body">

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Administrative Actions
                            </div>
                            <div class="panel-body">

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>Delete all files</strong>
                                                <div>
                                                    <em>Will permanently delete all uploaded files and thumbnails</em>
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.truncate') }}" class="btn btn-danger">Delete all files</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <strong>Check file integrity</strong>
                                                <div>
                                                    <em>Check file integrity and report possible errors</em>
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.integrity.check') }}" class="btn btn-primary">Check files</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Settings
                            </div>
                            <div class="panel-body">
                                <p>
                                    Change these in <code>{{ app_path('config/upload.php') }}</code>
                                </p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <th>Description</th>
                                        <th>Value</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>File code length:</td>
                                            <td>{{ config('upload.file_code_length') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Files visible by default:</td>
                                            @if (config('upload.file_default_visible', false))
                                                <td class="bg-success">Yes</td>
                                            @else
                                                <td class="bg-danger">No</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>Optimize PNG images:</td>
                                            @if (config('upload.optimize.png.enabled', false))
                                                <td class="bg-success">Yes</td>
                                            @else
                                                <td class="bg-danger">No</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>Optimize JPEG images:</td>
                                            @if (config('upload.optimize.jpeg.enabled', false))
                                                <td class="bg-success">Yes</td>
                                            @else
                                                <td class="bg-danger">No</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>Gather metadata on upload:</td>
                                            @if (config('upload.gather_metadata', false))
                                                <td class="bg-success">Yes</td>
                                            @else
                                                <td class="bg-danger">No</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>Files shown per page:</td>
                                            <td>{{ config('upload.files_per_page') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Statistics
                            </div>
                            <div class="panel-body">
                            @if ($stats != null)
                                <p class="text-warning">
                                    Note: Only lifetime statistics (and the optimization queue status) are updated in real time.
                                </p>
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <td>Files in optimization queue:</td>
                                        <td>{{ $job_count }}</td>
                                    </tr>
                                    <tr>
                                        <td>Uploaded files:</td>
                                        <td>{{ $stats->uploaded_files }}</td>
                                    </tr>
                                    <tr>
                                        <td>Uploaded files (lifetime):</td>
                                        <td>{{ $stats->uploaded_files_total }}</td>
                                    </tr>
                                    <tr>
                                        <td>Visible files:</td>
                                        <td>{{ $stats->visible_files }}</td>
                                    </tr>
                                    <tr>
                                        <td>Hidden files:</td>
                                        <td>{{ $stats->hidden_files }}</td>
                                    </tr>
                                    <tr>
                                        <td>Trashed files:</td>
                                        <td>{{ $stats->trashed_files }}</td>
                                    </tr>
                                    <tr>
                                        <td>Deleted files (lifetime):</td>
                                        <td>{{ $stats->deleted_files_total }}</td>
                                    </tr>
                                    <tr>
                                        <td>Files optimized (lifetime):</td>
                                        <td>{{ $stats->optimized_files_total }}</td>
                                    </tr>
                                    <tr>
                                        <td>Used disk space (files):</td>
                                        <td>{{ BytesConverter::bytesToHuman($stats->used_disk_space_files) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Used disk space (thumbnails):</td>
                                        <td>{{ BytesConverter::bytesToHuman($stats->used_disk_space_thumbs) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Used disk space (total):</td>
                                        <td>{{ BytesConverter::bytesToHuman($stats->used_disk_space_files + $stats->used_disk_space_thumbs) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Disk space saved with optimization:</td>
                                        <td>{{ BytesConverter::bytesToHuman($stats->optimized_files_savings) }}</td>
                                    </tr>
                                </table>
                                <a href="{{ route('admin.update.stats') }}" class="btn btn-primary">Update statistics</a>
                            @else
                                <div>
                                    <p>No statistics!</p>
                                    <a href="{{ route('admin.update.stats') }}" class="btn btn-primary">Update statistics</a>
                                </div>
                            @endif
                            </div>
                        </div>

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                User Information
                            </div>
                            <div class="panel-body">
                                <form>
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" value="{{ Auth::user()->name }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="apiKey">API Key</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="apiKey" value="{{ Auth::user()->api_token }}" readonly>
                                            <div class="input-group-btn">
                                                <a class="btn btn-danger" href="{{ route('user.generate_token') }}">Regenerate</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection