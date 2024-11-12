
    @include('emails.en.header')

        <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;">
                <p>Hi {{ $row->name }},<br/></p>
                <p>
                    Your password was changed successfully.<br/>
                    If you did not make this change, please contact us immediately at info@domainName.com
                </p>
              </p>
            </td>
          </tr>
          <!-- end copy -->

    @include('emails.en.footer')
