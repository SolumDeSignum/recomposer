<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Installed Packages with their Dependencies
        </h3>
    </div>
    <div class="card-body">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Package Name : Version</th>
                <th>Dependency Name : Version</th>
            </tr>
            </thead>
            <tbody>
            @foreach($packages as $package)
                <tr>
                    <td>
                        <a href="https://github.com/{{ $package['name'] }}"
                           target="_blank">{{ $package['name'] }}
                        </a> :
                        <span class="badge badge-danger">
                            {{ $package['version'] }}
                        </span>
                    </td>
                    <td>
                        <ul>
                            @if(is_array($package['dependencies']))
                                @foreach($package['dependencies'] as $dependencyName => $dependencyVersion)
                                    @if(Str::contains($dependencyName, '/'))
                                        <li>
                                            <a href="https://github.com/{{ $dependencyName }}"
                                               target="_blank">{{ $dependencyName }}</a>
                                            : <span class="badge badge-danger">
                                                {{ $dependencyVersion }}
                                            </span>
                                        </li>
                                    @else
                                        <li>{{ $dependencyName }} :
                                            <span class="badge badge-danger">
                                                {{ $dependencyVersion }}
                                            </span>
                                        </li>
                                    @endif
                                @endforeach
                            @else
                                <li>
                                    <span class="label label-primary">
                                        {{ $package['dependencies'] }}
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
