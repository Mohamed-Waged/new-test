
    @include('emails.en.new.header')

        <!-- start copy -->
          <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;">Tap the button below to confirm your email address. If you didn't create an account with <a href="https://domainName.com">domainName</a>, you can safely delete this email.</p>
            </td>
          </tr>
          <!-- end copy -->

          <!-- start button -->
          <tr>
            <td align="left" bgcolor="#ffffff">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center" bgcolor="#ffffff" style="padding: 12px;">
                    <table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" bgcolor="#51266E" style="border-radius: 6px;">
                          <a href="https://api.domainName.com/api/account/verify/{{$row->validation_code}}" target="_blank" style="display: inline-block; padding: 16px 36px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;">Confirm my email address</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <!-- end button -->

          <!-- start copy -->
          <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;">If that doesn't work, copy and paste the following link in your browser:</p>
              <p style="margin: 0;"><a href="https://api.domainName.com/api/account/verify/{{$row->validation_code}}" target="_blank">https://domainName.com/account/verify/{{$row->validation_code}}</a></p>
            </td>
          </tr>
          <!-- end copy -->

    @include('emails.en.new.footer')
