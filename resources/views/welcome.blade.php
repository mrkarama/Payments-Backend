<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Paypal Payment</title>

        
    </head>
    <body>
        <center>        
            <form action="{{ route('paypal') }}" method="post">
            @csrf
            <input type="hidden" name="course_price" value="13">
            <input type="hidden" name="course_name" value="Flutter">
            <button type='submit'>
                <img width="100" src="{{ asset('storage/assets/payments/img/paypal.png') }}">
            </button>
            
        </center>
    </body>
</html>
