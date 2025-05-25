<x-mail::message>
# Permintaan Reset Password

Halo {{ $name }},

Kami menerima permintaan untuk mereset kata sandi akun Anda.
Klik tombol di bawah ini untuk membuat kata sandi baru:

<x-mail::button :url="$resetLink">
Reset Password
</x-mail::button>

Jika Anda tidak meminta reset password, silakan abaikan email ini. Tidak akan ada perubahan apa pun pada akun Anda.

Terima kasih,<br>
Admin {{ config('app.name') }}
</x-mail::message>
