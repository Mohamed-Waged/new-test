
    @include('emails.en.header')

        <!-- start copy -->
        <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;">
                <p>Hi {{ $row->name }},<br/></p>
                <p>
                    You recently requested to reset your password. To reset your password, 
                    just copy & past the code below:
                </p>
                <p><b>{{ $row->validation_code }}</b></p>
              </p>
            </td>
        </tr>
        <!-- end copy -->

    @include('emails.en.footer')
