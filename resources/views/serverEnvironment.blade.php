<div class="card pt-4">
    <div class="card-header">
        <h3 class="card-title">Server Environment</h3>
    </div>

    <ul class="list-group">
        <li class="list-group-item">
            PHP Version: {{ $serverEnv['version'] }}</li>
        <li class="list-group-item">
            Server Software: {{ $serverEnv['server_software'] }}</li>
        <li class="list-group-item">
            Server OS: {{ $serverEnv['server_os'] }}</li>
        <li class="list-group-item">
            Database: {{ $serverEnv['database_connection_name'] }}</li>
        <li class="list-group-item">
            SSL Installed: {!! $serverEnv['ssl_installed'] ? $iconCheck : $iconUncheck !!}</li>
        <li class="list-group-item">
            Cache Driver: {{ $serverEnv['cache_driver'] }}</li>
        <li class="list-group-item">
            Session Driver: {{ $serverEnv['session_driver'] }}</li>
        <li class="list-group-item">
            Openssl Ext: {!! $serverEnv['openssl'] ? $iconCheck : $iconUncheck !!}</li>
        <li class="list-group-item">
            PDO Ext: {!! $serverEnv['pdo'] ? $iconCheck : $iconUncheck !!}</li>
        <li class="list-group-item">
            Mbstring Ext: {!! $serverEnv['mbstring'] ? $iconCheck : $iconUncheck !!}</li>
        <li class="list-group-item">
            Tokenizer Ext: {!! $serverEnv['tokenizer']  ? $iconCheck : $iconUncheck!!}</li>
        <li class="list-group-item">
            XML Ext: {!! $serverEnv['xml'] ? $iconCheck : $iconUncheck !!}</li>
        @foreach($serverExtras as $extraStatKey => $extraStatValue)
            <li class="list-group-item">{{ $extraStatKey }}:
                {!! is_bool($extraStatValue) ? ($extraStatValue ? $iconCheck : $iconUncheck) : $extraStatValue !!}
            </li>
        @endforeach
    </ul>
</div>
