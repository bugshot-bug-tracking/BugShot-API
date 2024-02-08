@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.webpanel_url')])
            {{ config('app.projectname') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <td class="content-cell footer-logo" align="left">
                <!--[if mso]>
                 <table width="50%"><tr><td><img width="200" src="{{ asset('img/bugshot_logo.png') }}" alt="ITEAMS"></td></tr></table>
                 <div style="display:none">
                <![endif]-->
                <img src="{{ asset('img/bugshot_logo.png') }}" width="150" class="logo"
                    alt="{{ config('app.projectname') }} Logo">
                <!--[if mso]>
                 </div>
                <![endif]-->
            </td>
            <td class="content-cell" align="right">
                @if ($locale == 'en')
                    By using BugShot, you are agreeing to our <a href="{{ config('app.proposal_url') . '/en/terms-of-use' }}">terms
                        and conditions</a>.<br />
                    More infos at: <a href="{{ config('app.proposal_url') . '/en' }}">{{ config('app.proposal_url') }}</a><br />
                    <strong>© {{ date('Y') }} {{ config('app.projectname') }}. @lang('All rights reserved.')</strong>
                @elseif($locale == 'de')
                    Indem du BugShot nutzt, stimmst du unseren <a
                        href="{{ config('app.proposal_url') . '/nutzungsbedingungen' }}">Bedingungen und Konditionen</a> zu.<br />
                    Mehr Infos unter: <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a><br /><br />
                    <strong>© {{ date('Y') }} {{ config('app.projectname') }}. @lang('All rights reserved.')</strong>
                @endif
            </td>
        @endcomponent
    @endslot
@endcomponent
