<h6>Teams</h6>

<ul>
@foreach(Auth::user()->teams as $team)
    <li><a href="{{ request()->fullUrlWithQuery(['team_id' => $team->id]) }}">{{ $team->name }}</a></li>
@endforeach
</ul>