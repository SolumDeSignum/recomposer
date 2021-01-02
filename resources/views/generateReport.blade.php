<div class="accordion accordion-flush"
     id="accordionGenerateReport">
    <div class="accordion-item">
        <h2 class="accordion-header"
            id="flush-headingOne">
            <button class="accordion-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne"
                    aria-expanded="false"
                    aria-controls="flush-collapseOne">
                Generate Report
            </button>
        </h2>
        <div id="flush-collapseOne"
             class="accordion-collapse collapse"
             aria-labelledby="flush-headingOne"
             data-bs-parent="#accordionGenerateReport">
            <div class="accordion-body">
            <textarea
                  id="generatedReport"
                  class="form-control"
                  rows="55"
                  spellcheck="false"
                  onfocus="this.select()">
### Laravel Environment

- Laravel Version: {{ $laravelEnv['version'] }}
- Timezone: {{ $laravelEnv['timezone'] }}
- Debug Mode: {!! $laravelEnv['debug_mode'] ? '&#10004;' : '&#10008;' !!}
- Storage Dir Writable: {!! $laravelEnv['storage_dir_writable'] ? '&#10004;' : '&#10008;' !!}
- Cache Dir Writable: {!! $laravelEnv['cache_dir_writable'] ? '&#10004;' : '&#10008;' !!}
- Decomposer Version: {{ $laravelEnv['decomposer_version'] }}
- App Size: {{ $laravelEnv['app_size'] }}
@foreach($laravelExtras as $key => $value)
- {{ $key }} : {{ is_bool($value) ? ($value ? '&#10004;' : '&#10008;') : $value }}
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
@foreach($serverExtras as $key => $value)
- {{ $key }} : {{ is_bool($value) ? ($value ? '&#10004;' : '&#10008;') : $value }}
@endforeach

### Installed Packages &amp; their version numbers

@foreach($packages as $package)
- {{ $package['name'] }} : {{ $package['version'] }}
@endforeach

@if(!empty($extraStats))
### Extra Information

@foreach($extraStats as $key => $value)
- {{ $key }} : {{ is_bool($value) ? ($value ? '&#10004;' : '&#10008;') : $value }}
@endforeach @endif</textarea>
                <button id="copyGeneratedReport" class="btn btn-primary mt-3">
                    Copy Report
                </button>
            </div>
        </div>
    </div>
</div>
