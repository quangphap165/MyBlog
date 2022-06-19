@component('mail::message')
# Visitor Message

Some Visitor Left a message:

<br>
Firstname: {{$firstname}}
Secondname: {{$secondname}}
Email: {{$email}}
Sinject: {{$subject}}
<br>
Message: {{$message}}

@component('mail::button', ['url' => ''])
View Message
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent