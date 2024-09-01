<div class="card ">
    <div class="card-header">
        <h3 class="card-title">Laravel Environment</h3>
    </div>
    <ul class="list-group">
        <li class="list-group-item">Laravel
            Version: {{ $laravelEnv['version'] }}</li>
        <li class="list-group-item">
            Timezone: {{ $laravelEnv['timezone'] }}</li>
        <li class="list-group-item">
            Debug Mode: {!! $laravelEnv['debug_mode'] ? $iconCheck : $iconUncheck !!}
        </li>
        <li class="list-group-item">
            Storage Dir Writable: {!! $laravelEnv['storage_dir_writable'] ? $iconCheck : $iconUncheck !!}
        </li>
        <li class="list-group-item">
            Cache Dir Writable: {!! $laravelEnv['cache_dir_writable'] ? $iconCheck : $iconUncheck !!}
        </li>
        <li class="list-group-item">Recomposer
            Version: {{ $laravelEnv['recomposer_version'] }}</li>
        <li class="list-group-item">
            App Size: {{ $laravelEnv['app_size'] }}</li>
        @foreach($laravelExtras as $key => $value)
            <li class="list-group-item">
                {{ $key }} : {!! is_bool($value) ? ($value ? $iconCheck : $iconUncheck) : $value !!}
            </li>
        @endforeach
    </ul>
</div>
