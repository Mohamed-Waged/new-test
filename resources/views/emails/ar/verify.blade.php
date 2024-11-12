
    @include('emails.ar.header')

        <!-- start copy -->
          <tr>
            <td align="right" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px">
              <p style="margin: 0;">اضغط على الزر أدناه لتأكيد عنوان بريدك الإلكتروني. إذا لم تقم بإنشاء حساب مع <a href="https://domainName.com/">domainName</a>, يمكنك حذف هذا البريد الإلكتروني بأمان.</p>
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
                          <a href="https://api.domainName.com/api/v1/account/verify/{{$row->validation_code}}" target="_blank" style="display: inline-block; padding: 16px 36px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;">تأكيد عنوان بريدي الإلكتروني</a>
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
            <td align="right" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;">إذا لم يعمل الزر ، فانسخ الرابط التالي والصقه في متصفحك:</p>
              <p style="margin: 0;"><a href="https://api.domainName.com/api/account/verify/{{$row->validation_code}}" target="_blank">https://domainName.com/account/verify/{{$row->validation_code}}</a></p>
            </td>
          </tr>
          <!-- end copy -->

    @include('emails.ar.footer')
