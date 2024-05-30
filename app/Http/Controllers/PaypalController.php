<?php

namespace App\Http\Controllers;
// Import the class namespaces first, before using it directly
use Srmklive\PayPal\Services\PayPal as PayPalClient;

use Illuminate\Http\Request;
use App\Models\PaypalPayment;

class PaypalController extends Controller
{
    public function payment(Request $request)
    {
        // dd($request->price);
        $provider = new PayPalClient;

        // Through facade. No need to import namespaces
        $provider = \PayPal::setProvider();

        // call the paypal.php from the config folder directly
        $provider->setApiCredentials(config('paypal'));

        # After setting the PayPal API configuration, you need to get access token before performing any API calls
        $paypalToken = $provider->getAccessToken();
        
        // Create order

        $response = [
            "intent"=> "CAPTURE",
            "application_context"=> [
                "return_url"=> route('paypal_success'),
                "cancel_url"=> route('paypal_cancel')
            ],
            "purchase_units"=> [
              [
                "amount"=> [
                    "currency_code"=> "USD",
                    "value"=> $request->course_price
                  ]
              ]
            ],
            
        ];
        
        $response = $provider->createOrder($response);
        
        // This will print what is in the response when trying to make a payment
        // array:3 [▼ // app\Http\Controllers\PaypalController.php:47
        // "id" => "3E527326XW375410H"
        // "status" => "CREATED"
        // "links" => array:4 [▼
        //     0 => array:3 [▼
        //        "href" => "https://api.sandbox.paypal.com/v2/checkout/orders/3E527326XW375410H"
        //        "rel" => "self"
        //        "method" => "GET"
        //        ]
        //     1 => array:3 [▼
        //     "href" => "https://www.sandbox.paypal.com/checkoutnow?token=3E527326XW375410H"
        //     "rel" => "approve"
        //     "method" => "GET"
        //     ]
        //     2 => array:3 [▼
        //     "href" => "https://api.sandbox.paypal.com/v2/checkout/orders/3E527326XW375410H"
        //     "rel" => "update"
        //     "method" => "PATCH"
        //     ]
        //     3 => array:3 [▼
        //     "href" => "https://api.sandbox.paypal.com/v2/checkout/orders/3E527326XW375410H/capture"
        //     "rel" => "capture"
        //     "method" => "POST"
        //     ]
        // ]
        // ]
        //dd($response);

        if(isset($response['id']) && $response['id'] != null){
            session()->put('course_name', $request->course_name);
            session()->put('course_price', $request->course_price);
            foreach($response['links'] as $link){
                if($link['rel'] === 'approve'){
                    return redirect()->away($link['href']);
                }
            }
        } else{
            redirect()->route('paypal_cancel');
        }

    }

    public function cancel()
    {
        return response()->json([
            'status'=> 'FAIL',
            'message'=> 'Order failed'
        ]);
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;

        $provider->setApiCredentials(config('paypal'));

        $paypalToken = $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);


        // array:6 [▼ // app\Http\Controllers\PaypalController.php:105
        // "id" => "5FV600561M846414K"
        // "status" => "COMPLETED"
        // "payment_source" => array:1 [▼
        //     "paypal" => array:5 [▼
        //     "email_address" => "sb-yky47i28028844@personal.example.com"
        //     "account_id" => "NR6K9VYK4KV3W"
        //     "account_status" => "VERIFIED"
        //     "name" => array:2 [▼
        //         "given_name" => "John"
        //         "surname" => "Doe"
        //     ]
        //     "address" => array:1 [▼
        //         "country_code" => "US"
        //     ]
        //     ]
        // ]
        // "purchase_units" => array:1 [▼
        //     0 => array:3 [▼
        //     "reference_id" => "default"
        //     "shipping" => array:2 [▼
        //         "name" => array:1 [▼
        //         "full_name" => "John Doe"
        //         ]
        //         "address" => array:5 [▼
        //         "address_line_1" => "1 Main St"
        //         "admin_area_2" => "San Jose"
        //         "admin_area_1" => "CA"
        //         "postal_code" => "95131"
        //         "country_code" => "US"
        //         ]
        //     ]
        //     "payments" => array:1 [▼
        //         "captures" => array:1 [▼
        //         0 => array:9 [▼
        //             "id" => "9K683308DT425060F"
        //             "status" => "COMPLETED"
        //             "amount" => array:2 [▼
        //             "currency_code" => "USD"
        //             "value" => "50.00"
        //             ]
        //             "final_capture" => true
        //             "seller_protection" => array:2 [▼
        //             "status" => "ELIGIBLE"
        //             "dispute_categories" => array:2 [▼
        //                 0 => "ITEM_NOT_RECEIVED"
        //                 1 => "UNAUTHORIZED_TRANSACTION"
        //             ]
        //             ]
        //             "seller_receivable_breakdown" => array:3 [▼
        //             "gross_amount" => array:2 [▶]
        //             "paypal_fee" => array:2 [▼
        //                 "currency_code" => "USD"
        //                 "value" => "2.66"
        //             ]
        //             "net_amount" => array:2 [▼
        //                 "currency_code" => "USD"
        //                 "value" => "47.34"
        //             ]
        //             ]
        //             "links" => array:3 [▶]
        //             "create_time" => "2023-11-21T19:14:25Z"
        //             "update_time" => "2023-11-21T19:14:25Z"
        //         ]
        //         ]
        //     ]
        //     ]
        // ]
        // "payer" => array:4 [▼
        //     "name" => array:2 [▶]
        //     "email_address" => "sb-yky47i28028844@personal.example.com"
        //     "payer_id" => "NR6K9VYK4KV3W"
        //     "address" => array:1 [▼
        //     "country_code" => "US"
        //     ]
        // ]
        // "links" => array:1 [▼
        //     0 => array:3 [▼
        //     "href" => "https://api.sandbox.paypal.com/v2/checkout/orders/5FV600561M846414K"
        //     "rel" => "self"
        //     "method" => "GET"
        //     ]
        // ]
        // ]
        //dd($response);

        if (isset($response['status']) && $response['status'] == 'COMPLETED'){
                $payment = new PaypalPayment;

                $payment->customer_name = $response['purchase_units'][0]['shipping']['name']['full_name'];
                $payment->customer_email = $response['payment_source']['paypal']['email_address'];
                $payment->country_code = $response['purchase_units'][0]['shipping']['address']['country_code'];
                $payment->transaction_reference = $response['purchase_units'][0]['payments']['captures'][0]['id'];
                $payment->course_name = session()->get('course_name');
                $payment->course_price = session()->get('course_price');
                $payment->save();

                dd('Payment Success');

                session()->forget('product_name'); // or use unset($_SESSION['product_nme'])
                unset($_SESSION['product_price']);
        } else{
            redirect()->route('paypal_cancel');
        }
    }

     public function users_payment_data()
    {

        $data = PaypalPayment::all();

        return response()->json([
            "data"=> $data,
        ], 200,);
    }

}

// For Laravel Documentation : https://srmklive.github.io/laravel-paypal/docs.html
