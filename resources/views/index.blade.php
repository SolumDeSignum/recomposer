@extends('solumdesignum/recomposer::layout.master')

@section('content')
    <div class="container">
        <div class="row mt-5 mb-5">
            <div class="col-xl-9 col-lg-9 col-sm-12 col-xs-12">
                @include('solumdesignum/recomposer::InstalledPackagesWithDependecies')
            </div>
            <div class="col-xl-3 col-lg-3 col-sm-12 col-xs-12">
                @includeIf('solumdesignum/recomposer::laravelEnvironment')
                @includeIf('solumdesignum/recomposer::serverEnvironment')
                @includeIf('solumdesignum/recomposer::extraStats')
            </div>
        </div>
        <div class="card">
            @includeIf('solumdesignum/recomposer::generateReport')
        </div>
    </div>
@endsection
