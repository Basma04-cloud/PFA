@component('mail::message')
# Invitation à rejoindre notre plateforme

Vous avez été invité à rejoindre l’espace de {{ $invitation->inviter->name }}.

@component('mail::button', ['url' => route('register', ['token' => $invitation->token])])
Accepter l'invitation
@endcomponent

Cette invitation expire le {{ $invitation->expires_at->format('d/m/Y') }}.

@endcomponent

