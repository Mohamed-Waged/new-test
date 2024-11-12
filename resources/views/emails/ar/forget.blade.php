
    @include('emails.ar.header')

        <!-- start copy -->
        <tr>
            <td align="right" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                <p style="margin: 0;">
                    <p>مرحباً {{ $row->name }},<br/></p>
                    <p>
                        لقد طلبت مؤخرًا إعادة تعيين كلمة المرور الخاصة بك. لإعادة تعيين كلمة المرور الخاصة بك،
                        فقط انسخ والصق الكود أدناه:
                    </p>
                    <p><b>{{ $row->validation_code }}</b></p>
                </p>
            </td>
        </tr>
        <!-- end copy -->

    @include('emails.ar.footer')