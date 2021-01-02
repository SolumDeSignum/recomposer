@if($extraStats)
    <div class="card pt-4">
        <div class="card-header">
            <h3 class="card-title">Extra Stats</h3>
        </div>

        <ul class="list-group">
            @foreach($extraStats as $key => $value)
                <li class="list-group-item">
                    {{ $key }} : {!! is_bool($value) ? ($value ? $iconCheck : $iconUncheck) : $value !!}
                </li>
            @endforeach
        </ul>
    </div>
@endif
