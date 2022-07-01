<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'BugShot')
<img src="{{ asset('img/bugshot_logo_white.png') }}" class="logo" alt="{{ config('app.projectname') }} Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
