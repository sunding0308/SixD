@extends('admin.layout')

@push('title')
调试列表
@endpush

@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">调试列表</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item"><strong class="brdcrmb-item">文件列表</strong></a></p>
        </div><!-- .col-* -->

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>文件名</th>
                                <th>{{ __('admin/machine.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td>{{ basename($file) }}</td>
                                    <td class="playlist-actions hp">
                                        <a href="{{ route('admin.machine.debug.download', [$machine->id, basename($file)]) }}" class="btn btn-normal btn-m">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="row">
                        <div class="col-5">
                            <div class="dataTables_info">
                                @if ($files->count()>0)
                                {{
                                        __(
                                            'admin/machine.showing_from_to_files',
                                            [
                                                'from'=>$files->firstItem(),
                                                'to'=>$files->lastItem(),
                                                'total'=>$files->total()
                                            ]
                                        )
                                    }}
                                @else
                                    {{ __('admin/machine.no_files') }}
                                @endif
                            </div>
                        </div>

                        <div class="col-7">
                            <div class="dataTables_paginate paging_simple_numbers">
                                {{ $files->appends(request()->input())->links() }}
                            </div>
                        </div>
                    </div>

                </div><!-- .ibox-content -->

            </div><!-- .ibox -->

        </div><!-- .col* -->

    </div><!-- .row -->
@stop