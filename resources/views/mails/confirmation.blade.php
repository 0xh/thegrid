Hi {{$name}},

<p>Please click the link to confirm your email</p>
<a href="{{ env('APP_URL') }}confirmation/{{ $confirmation_token }}">Confirm your email</a>
