@extends('admin.layout')
@push('title')
管理员列表
@endpush

@section('content')
    <div class="row">

            <header class="title-header col-md-12">
                <h3 class="title">{{ __('admin/user.users') }}</h3>
            </header>

            <div class="col-4">
                <p class="brdcrmb"><strong class="brdcrmb-item">{{ __('admin/user.users') }}</strong></p>
            </div><!-- .col-* -->

            <div class="col-8 min-menu">
                <div class="right-panel">
                    @if(Auth::user()->name == 'admin')
                        <a href="{{ route('admin.user.create') }}" class="btn btn-normal btn-m m-r">{{ __('admin/user.new_user') }}</a>
                    @endif
                </div>
            </div><!-- .col-* -->

            <div class="col-md-12">

                <div class="ibox">

                    <div class="ibox-content playlist-list">

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th style="width: 8%;">{{ __('admin/user.id') }}</th>
                                <th class="playlist-title ptl" style="width: 50%;">{{ __('admin/user.name') }}/{{ __('admin/user.email') }}</th>
                                <th>{{ __('admin/user.created_at') }}</th>
                                <th>{{ __('admin/user.actions') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="playlist-title">
                                    <a href="#">{{ $user->name }}</a><br/>
                                    <small>{{ $user->email }}</small>
                                </td>
                                <td class="date">
                                    {{ $user->created_at }}
                                </td>
                                <td class="playlist-actions hp">
                                    @if(Auth::user()->name == 'admin')
                                        <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-normal btn-m"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <form method="POST" action="{{ route('admin.user.destroy', $user->id) }}" accept-charset="UTF-8" onsubmit="return ConfirmDelete()">
                                            {{ method_field('DELETE') }}
                                            {{ csrf_field() }}
                                            <a href="#" onclick="$(this).closest('form').submit()" class="btn btn-normal btn-m delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>

                        </table>

                        <div class="row">
                            <div class="col-5">
                                <div class="dataTables_info">
                                    @if ($users->count()>0)
                                        {{
                                            __(
                                                'admin/user.showing_from_to_users',
                                                [
                                                    'from'=>$users->firstItem(),
                                                    'to'=>$users->lastItem(),
                                                    'total'=>$users->total()
                                                ]
                                            )
                                        }}
                                    @else
                                        {{ __('admin/user.no_users') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $users->appends(request()->input())->links() }}
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
            var x = confirm('你确定要删除该管理员吗？');
            return (x) ? true : false;
        }
    </script>
@endpush
