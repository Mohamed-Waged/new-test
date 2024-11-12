<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Helper;
use Carbon\Carbon;

class UPayments
{
    // KNET TEST CARD DETAILS
    // Name: Knet Test Card[KNET1]
    // Card Number: 888888 0000000001
    // Expire Date: 09/2025
    // PIN: 1234

    // live mode
    const MERCHANT_ID       = '';
    const USERNAME          = '';
    const PASSWORD          = '';
    const LIVE_TOKEN        = '';
    const LIVE_URL          = 'https://api.upayments.com/payment-request';

    // test mode
    const TEST_MERCHANT_ID  = 1201;
    const TEST_USERNAME     = 'test';
    const TEST_PASSWORD     = 'test';
    const TEST_TOKEN        = 'jtest123';
    const TEST_URL          = 'https://api.upayments.com/test-payment';

    const UPAYMENT_DOCS     = 'https://developers.upayments.com/reference/addcharge';

    /**
     * @createPaymentRequest
     * @param int $orderId
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function createPaymentRequest($orderId)
    {
        // $orderId
    }
}
