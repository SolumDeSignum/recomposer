@php
    $iconCheck = '<i class="fas fa-check"></i>';
    $iconRemove = '<i class="fas fa-times"></i>';
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="bs-callout bs-callout-primary">
                <p>Please share this information for troubleshooting:</p>
                <button id="btn-report" class="btn btn-info btn-sm">Get System Report</button>
                <a href="https://github.com/nguyentranchung/laravel-decomposer/blob/master/report.md" target="_blank" id="btn-about-report"
                   class="btn btn-default btn-sm">Understand Report</a>

                <div id="report-wrapper">
                    <label for="txt-report"></label>
                    <textarea name="txt-report" id="txt-report" class="col-sm-12" rows="10" spellcheck="false" onfocus="this.select()">
                        ### Laravel Environment

                        - Laravel Version: {{ $laravelEnv['version'] }}
                        - Timezone: {{ $laravelEnv['timezone'] }}
                        - Debug Mode: {!! $laravelEnv['debug_mode'] ? '&#10004;' : '&#10008;' !!}
                        - Storage Dir Writable: {!! $laravelEnv['storage_dir_writable'] ? '&#10004;' : '&#10008;' !!}
                        - Cache Dir Writable: {!! $laravelEnv['cache_dir_writable'] ? '&#10004;' : '&#10008;' !!}
                        - Decomposer Version: {{ $laravelEnv['decomposer_version'] }}
                        - App Size: {{ $laravelEnv['app_size'] }}
                        @foreach($laravelExtras as $extraStatKey => $extraStatValue)
                            - {{ $extraStatKey }}: {{ is_bool($extraStatValue) ? ($extraStatValue ? '&#10004;' : '&#10008;') : $extraStatValue }}
                        @endforeach

                        ### Server Environment

                        - PHP Version: {{ $serverEnv['version'] }}
                        - Server Software: {{ $serverEnv['server_software'] }}
                        - Server OS: {{ $serverEnv['server_os'] }}
                        - Database: {{ $serverEnv['database_connection_name'] }}
                        - SSL Installed: {!! $serverEnv['ssl_installed'] ? '&#10004;' : '&#10008;' !!}
                        - Cache Driver: {{ $serverEnv['cache_driver'] }}
                        - Session Driver: {{ $serverEnv['session_driver'] }}
                        - Openssl Ext: {!! $serverEnv['openssl'] ? '&#10004;' : '&#10008;' !!}
                        - PDO Ext: {!! $serverEnv['pdo'] ? '&#10004;' : '&#10008;' !!}
                        - Mbstring Ext: {!! $serverEnv['mbstring'] ? '&#10004;' : '&#10008;' !!}
                        - Tokenizer Ext: {!! $serverEnv['tokenizer']  ? '&#10004;' : '&#10008;'!!}
                        - XML Ext: {!! $serverEnv['xml'] ? '&#10004;' : '&#10008;' !!}
                        @foreach($serverExtras as $extraStatKey => $extraStatValue)
                            - {{ $extraStatKey }}: {{ is_bool($extraStatValue) ? ($extraStatValue ? '&#10004;' : '&#10008;') : $extraStatValue }}
                        @endforeach

                        ### Installed Packages &amp; their version numbers

                        @foreach($packages as $package)
                            - {{ $package['name'] }} : {{ $package['version'] }}
                        @endforeach

                        @if(!empty($extraStats))
                            ### Extra Information

                            @foreach($extraStats as $extraStatKey => $extraStatValue)
                                - {{ $extraStatKey }} : {{ is_bool($extraStatValue) ? ($extraStatValue ? '&#10004;' : '&#10008;') : $extraStatValue }}
                            @endforeach
                        @endif
                    </textarea>
                    <button id="copy-report" class="btn btn-info btn-sm">Copy Report</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row"> <!-- Main Row -->

        <div class="col-sm-8"> <!-- Package & Dependency column -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Installed Packages and their Dependencies</h3>
                </div>
                <div class="panel-body">
                    <table id="decomposer" class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>Package Name : Version</th>
                            <th>Dependency Name : Version</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td><a href="https://github.com/{{ $package['name'] }}" target="_blank">{{ $package['name'] }}</a> : <span class="badge badge-danger">{{ $package['version'] }}</span></td>
                                <td>
                                    <ul>
                                        @if(is_array($package['dependencies']))
                                            @foreach($package['dependencies'] as $dependencyName => $dependencyVersion)
                                                @if(Str::contains($dependencyName, '/'))
                                                    <li><a href="https://github.com/{{ $dependencyName }}" target="_blank">{{ $dependencyName }}</a> : <span class="badge badge-danger">{{ $dependencyVersion }}</span></li>
                                                @else
                                                    <li>{{ $dependencyName }} : <span class="badge badge-danger">{{ $dependencyVersion }}</span></li>
                                                @endif
                                            @endforeach
                                        @else
                                            <li><span class="label label-primary">{{ $package['dependencies'] }}</span></li>
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- / Package & Dependency column -->

        <div class="col-sm-4"> <!-- Server Environment column -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Laravel Environment</h3>
                </div>

                <ul class="list-group">
                    <li class="list-group-item">Laravel Version: {{ $laravelEnv['version'] }}</li>
                    <li class="list-group-item">Timezone: {{ $laravelEnv['timezone'] }}</li>
                    <li class="list-group-item">Debug Mode: {!! $laravelEnv['debug_mode'] ? $iconCheck : $iconRemove !!}</li>
                    <li class="list-group-item">Storage Dir Writable: {!! $laravelEnv['storage_dir_writable'] ? $iconCheck : $iconRemove !!}</li>
                    <li class="list-group-item">Cache Dir Writable: {!! $laravelEnv['cache_dir_writable'] ? $iconCheck : $iconRemove !!}</li>
                    <li class="list-group-item">Decomposer Version: {{ $laravelEnv['decomposer_version'] }}</li>
                    <li class="list-group-item">App Size: {{ $laravelEnv['app_size'] }}</li>
                    @foreach($laravelExtras as $extraStatKey => $extraStatValue)
                        <li class="list-group-item">{{ $extraStatKey }}: {!! is_bool($extraStatValue) ? ($extraStatValue ? $iconCheck : $iconRemove) : $extraStatValue !!}</li>
                    @endforeach
                </ul>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Server Environment</h3>
                </div>

                <ul class="list-group">
                    <li class="list-group-item">PHP Version: {{ $serverEnv['version'] }}</li>
                    <li class="list-group-item">Server Software: {{ $serverEnv['server_software'] }}</li>
                    <li class="list-group-item">Server OS: {{ $serverEnv['server_os'] }}</li>
                    <li class="list-group-item">Database: {{ $serverEnv['database_connection_name'] }}</li>
                    <li class="list-group-item">SSL Installed: {!! $serverEnv['ssl_installed'] ? $iconCheck : $iconRemove !!}</li>
                    <li class="list-group-item">Cache Driver: {{ $serverEnv['cache_driver'] }}</li>
                    <li class="list-group-item">Session Driver: {{ $serverEnv['session_driver'] }}</li>
                    <li class="list-group-item">Openssl Ext: {!! $serverEnv['openssl'] ? $iconCheck : $iconRemove !!}</li>
                    <li class="list-group-item">PDO Ext: {!! $serverEnv['pdo'] ? $iconCheck : $iconRemove !!}</li>
                    <li class="list-group-item">Mbstring Ext: {!! $serverEnv['mbstring'] ? $iconCheck : $iconRemove !!}</li>
                    <li class="list-group-item">Tokenizer Ext: {!! $serverEnv['tokenizer']  ? $iconCheck : $iconRemove!!}</li>
                    <li class="list-group-item">XML Ext: {!! $serverEnv['xml'] ? $iconCheck : $iconRemove !!}</li>
                    @foreach($serverExtras as $extraStatKey => $extraStatValue)
                        <li class="list-group-item">{{ $extraStatKey }}: {!! is_bool($extraStatValue) ? ($extraStatValue ? $iconCheck : $iconRemove) : $extraStatValue !!}</li>
                    @endforeach
                </ul>
            </div>

            @if(!empty($extraStats))
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Extra Stats</h3>
                    </div>

                    <ul class="list-group">
                        @foreach($extraStats as $extraStatKey => $extraStatValue)
                            <li class="list-group-item">{{ $extraStatKey }}: {!! is_bool($extraStatValue) ? ($extraStatValue ? $iconCheck : $iconRemove) : $extraStatValue !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div> <!-- / Server Environment column -->

    </div> <!-- / Main Row -->
</div>
