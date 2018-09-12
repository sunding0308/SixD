@extends('admin.layout')
@push('title')
版本列表
@endpush

@section('content')
    <div class="row">

            <header class="title-header col-md-12">
                <h3 class="title">{{ __('admin/version.versions') }}</h3>
            </header>

            <div class="col-4">
                <p class="brdcrmb"><strong class="brdcrmb-item">{{ __('admin/version.versions') }}</strong></p>
            </div><!-- .col-* -->

            <div class="col-8 min-menu">
                <div class="right-panel">
                    <a href="{{ route('admin.version.create') }}" class="btn btn-normal btn-m m-r">{{ __('admin/version.new_version') }}</a>
                </div>
            </div><!-- .col-* -->

            <div class="col-md-12">

                <div class="ibox">

                    <div class="ibox-content playlist-list">

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th style="width: 8%;">{{ __('admin/version.id') }}</th>
                                <th>{{ __('admin/version.version_name') }}</th>
                                <th>{{ __('admin/version.version_code') }}</th>
                                <th>{{ __('admin/version.created_at') }}</th>
                                <th>{{ __('admin/version.actions') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($versions as $version)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="playlist-title">
                                    <small>{{ $version->version_name }}</small>
                                </td>
                                <td class="playlist-title">
                                    <small>{{ $version->version_code }}</small>
                                </td>
                                <td class="date">
                                    {{ $version->created_at }}
                                </td>
                                <td class="playlist-actions hp">
                                    <a href="{{ route('admin.version.download', ['url' => $version->url]) }}" class="btn btn-normal btn-m">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.version.destroy', $version->id) }}" accept-charset="UTF-8" onsubmit="return ConfirmDelete()">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <a href="#" onclick="$(this).closest('form').submit()" class="btn btn-normal btn-m delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>

                        </table>

                        <div class="row">
                            <div class="col-5">
                                <div class="dataTables_info">
                                    @if ($versions->count()>0)
                                        {{
                                            __(
                                                'admin/version.showing_from_to_versions',
                                                [
                                                    'from'=>$versions->firstItem(),
                                                    'to'=>$versions->lastItem(),
                                                    'total'=>$versions->total()
                                                ]
                                            )
                                        }}
                                    @else
                                        {{ __('admin/version.no_versions') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $versions->appends(request()->input())->links() }}
                                </div>
                            </div>
                        </div>

                    </div><!-- .ibox-content -->

                </div><!-- .ibox -->

            </div><!-- .col* -->

        </div><!-- .row -->
@stop

@push('foot-scripts')
    <script>
        function ConfirmDelete() {
            var x = confirm('你确定要删除该版本吗？');
            return (x) ? true : false;
        }
    </script>
@endpush