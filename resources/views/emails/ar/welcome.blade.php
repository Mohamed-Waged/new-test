
    @include('emails.ar.header')

        <p>مرحباً {{ $row->name }},<br/></p>
        <p>تم التحقق من حسابك بنجاح.</p>

    @include('emails.ar.footer')