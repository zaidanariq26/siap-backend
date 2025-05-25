@props(['url'])
<tr>
<td class="header">
<span style="display: inline-block;">
@if (trim($slot) === 'SIAP')
<img src="{{ asset('image/logo-siap.svg') }}" class="logo" alt="SIAP Logo">
@else
{{ $slot }}
@endif
</span>
</td>
</tr>
