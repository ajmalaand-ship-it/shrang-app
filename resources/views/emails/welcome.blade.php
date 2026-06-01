@component('mail::message')

# Welcome to Shrang, {{ $name }}!

Your account is ready. You have **{{ $credits }} free credits** to start creating music from your words.

**What you can do:**
- Turn poetry or lyrics into an original song
- Generate background music for your content
- Create shareable reels for social media

Shrang supports Pashto, Dari, Urdu, Arabic, Hindi, and English.

@component('mail::button', ['url' => config('app.url') . '/create'])
Start Creating
@endcomponent

If you have any questions, reply to this email and we will help you.

Regards,
The Shrang Team

@endcomponent
