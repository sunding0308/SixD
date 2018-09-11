@extends('admin.layout')
@push('title')
添加版本
@endpush

@section('content')

    <div class="row justify-content-center">

            <header class="title-header col-md-9">
                <h3 class="title">{{ __('admin/version.new_version') }}</h3>
            </header>

            <div class="col-md-9">
                <a href="{{ route('admin.version.index') }}" class="btn btn-normal btn-m">{{ __('admin/version.back') }}</a>
                <p class="brdcrmb"><strong class="brdcrmb-item">{{ __('admin/version.new_version') }}</strong></p>
            </div><!-- .col-* -->

            <div class="col-md-9">

                {{ Form::open(['url' => route('admin.version.store'), 'method' => 'POST', 'name'=>'form_version_creation', 'class' => 'form-horizontal', 'files' => true]) }}

                <div class="ibox">

                    <div class="ibox-title">
                        <h5>{{ __('admin/version.version_information') }}</h5>
                    </div>

                    <div class="ibox-content">

                            <div class="form-group row{{ $errors->has('version_name') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/version.version_name') }}*</label>
                                <div class="col-md-9">
                                    <input type="text" name="version_name" value="{{ old('version_name') }}" class="form-control">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'version_name'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('description') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/version.description') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control-area" rows="3" name="description" tabindex="2">{{ old('description') }}</textarea>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'description'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('file') ? ' has-danger' : '' }}">
                                <label class="col-md-3 control-label">{{ __('admin/version.file') }}*</label>
                                <div class="col-md-9">
                                    <label class="custom-file">
                                        <input type="file" name="file" id="file" class="custom-file-input" onchange="fileSelect(event)"/>
                                        <span class="custom-file-control"></span>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'file'])
                                    </label>
                                    <span id="fileName"></span>
                                </div>
                            </div>

                    </div><!-- .ibox-content -->

                </div><!-- .ibox -->

                <div class="form-save">
                    {{ Form::submit(__('admin/version.create'), ['class' => 'btn btn-primary']) }}
                </div>

                {{ Form::close() }}

            </div><!-- .col* -->

        </div><!-- .row -->

@stop

@push('js')
    <script>
        function fileSelect(id, e){
            $('#fileName').text($('#file')[0].files[0]['name']);
        }
    </script>
@endpush
