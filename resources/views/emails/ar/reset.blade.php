
    @include('emails.ar.header')

        <p>مرحباً {{ $row->name }},<br/></p>
        <p>
            تم تغيير كلمة المرور الخاصة بك بنجاح.<br/>
            إذا لم تقم بإجراء هذا التغيير ، فيرجى الاتصال بنا على الفور على info@mynurserykw.com
        </p>

    @include('emails.ar.footer')