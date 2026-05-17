<x-mail::message>
# Thawani security code

Hello,

Use the verification code below to complete your sign up. This code expires in {{ $ttlMinutes }} minutes.

<x-mail::panel>
{{ $otp }}
</x-mail::panel>

If you did not request this code, you can safely ignore this email.

Thanks,
{{ $appName }} Support

<x-mail::subcopy>
This message was sent by {{ $appName }} as part of account security.
</x-mail::subcopy>
</x-mail::message>
