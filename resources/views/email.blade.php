<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body style="margin: 0">
<style>
    .email-body
    {
        color:black;
    }
    .email-content
    {
        max-width: 450px;
    }
    .email-content-detail
    {
        margin: 50px 0;
    }
    @media (max-width: 570px) {
        .email_btn
        {
            padding:15px 30px !important;
            font-size:18px !important;
        }
    }
    @media (max-width: 430px) {
        .email_btn {
            padding: 15px 20px !important;
            font-size: 12px !important;
        }
    }
    @media (max-width: 400px) {
        .email_btn {
            padding: 15px 10px !important;
            font-size: 12px !important;
        }
        span
        {
            font-size:18px !important ;
        }
    }
</style>
<div class="email-body" style="padding: 20px;max-width: 700px;margin: auto; font-family: DIN Next,sans-serif;">
    <div class="email-contaner" style="border: 4px solid #7daa40;padding: 25px;">
        <div class="email-content" style=" max-width: 450px;  margin: auto;  text-align: center; ">
            <div class="email-logo">

                <img src="https://cdn.shopify.com/s/files/1/0370/7361/7029/files/image_3.png?v=1585895317" alt="Wefullfill" style="width: 50%">

            </div>
            <div class="email-content-detail" style="margin: 50px 0;">
                <h1 class="email-title" style="margin: 0;margin-bottom: 30px;font-size: 34px;">We’ve received your order.</h1>
                <p class="email-message-1" style=" margin: 0;margin-bottom: 15px;font-size: 20px;line-height: 1.53;">We have refunded you <span id="refound_amount">Your order has been received. It’ll ship out to you in the next few days — we’ll let you know when it does.</span></p>
                <p class="email-message-2" style=" margin: 0;margin-bottom: 15px;font-size: 20px;line-height: 1.53;">Order Number<br> <span>#798998</span></p>
                <div class="orderinvoice" style="  width: 100%;">
                    <div class="order-invoice-detail" style=" margin-bottom: 60px; ">
                       <!--Line Items Sections-->
                        <div class="invoice-product" style="margin-bottom: 15px;  padding-bottom: 15px;  padding-top: 15px; margin-top: 15px;border-top: 1px solid #D7D7D7;   border-bottom: 1px solid #D7D7D7;">
                            <div style="width: 24%; display:inline-block; vertical-align: middle; " class="invoice-product-image">
                                <img src="https://cdn.shopify.com/s/files/1/0370/7361/7029/files/image_3.png?v=1585895317" alt="" style=" width: 120px; "></div>
                            <div class="invoice-product-detail" style="width: 70%; display:inline-block; vertical-align: middle;  padding-left: 15px; ">
                                <div class="invoice-product-detail-left" style="     vertical-align: top; width: 78%; display: inline-block;  text-align: left; ">
                                    <h4 class="invoice-product-title" style=" margin: 0; ">Line Item</h4>
                                    <p class="invoice-product-varient" style="  margin: 0;     font-size: 15px; " >X2
                                    </p>
                                </div>
                                <div class="invoice-product-detail-right " style="width: 20% ; display: inline-block;">
                                    <span class="invoice-product-regularpice">0923890</span>
                                </div>
                            </div>
                        </div>

                        <div class="order-invoice-subtotal">
                            <ul style="  list-style: none;  padding: 0;">
                                <li style=" display: flex; margin-bottom: 10px;"><span style="width: 50%;
    text-align: left;" class="order-invoice-price-type">Subtotal</span><span style="width: 50%;
    text-align: right;" class="invoice-price-amount">0923890</span></li>
                                <li style=" display: flex; margin-bottom: 10px;"><span style="width: 50%;
    text-align: left;" class="order-invoice-price-type">Shipping</span><span style="width: 50%;
    text-align: right;" class="invoice-price-amount">0923890</span>
                                </li>
                                <li style=" display: flex;  margin-bottom: 10px;"><span style="width: 50%;
    text-align: left;" class="order-invoice-price-type">Tax</span><span style="width: 50%;
    text-align: right;" class="invoice-price-amount">0923890</span></li>
                                <li style=" display: flex; margin-bottom: 10px;"><span style="width: 50%;
    text-align: left;" class="order-invoice-price-type">Total Savings</span><span class="invoice-price-amount" style="width: 50%;
    text-align: right;">0923890</span></li>
                                <li style="display: flex; margin-bottom: 10px;  margin-top: 5px; padding-top: 10px;  border-top: 1px solid #D7D7D7;"><span tyle="width: 50%;
    text-align: left;" class="order-invoice-price-type"><strong>Total</strong></span><span style="width: 50%;
    text-align: right;" class="invoice-price-amount">98748973298479</span></li>
                            </ul>
                        </div>
                    </div>
                    <a href="" class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;">FOLLOW YOUR ORDER</a>
                </div>
            </div>
        </div>
    </div>
    <div class="email-footer" style=" padding: 25px 10px; background: {{shop.email_accent_color}}; color: white; ">
        <nav>
            <ul style="list-style: none; padding: 0; color: white; text-align: center;">
                <li style=" width: max-content; display: inline-block; margin-right: 15px"><a href="{{shop.url}}/collections/all" style="color: white;">Shop Now</a></li>
                <li style="width: max-content; display: inline-block; margin-right: 15px"><a href="{{shop.url}}/pages/contact-us" style="color: white;">Contact</a></li>
                <li style="width: max-content; display: inline-block; margin-right: 15px;"><a href="{{shop.url}}/collections" style="color: white">Feature Brands</a></li>
                <li style="width: max-content; display: inline-block; margin-right: 15px"><a href="{{shop.url}}/collections/new-arrivals" style="color: white">New Arrivals</a></li>
                <li style="width: max-content; display: inline-block; margin-right: 15px"><a href="{{shop.url}}/pages/contact-us" style="color: white">Need Help?</a></li>
            </ul>
        </nav>
        <div class="email-footer-caption">
            <ul style=" color: white; list-style: none; padding: 0 ;  margin-top: 25px;text-align: center; ">
                <li class="site-name" style="width: max-content; display: inline-block; margin-right: 15px;padding-right: 15px;border-right: 1px solid white;"><a href="{{shop.url}}" style="color: white;text-decoration: none;">Cobalt</a></li>
                <li class="dalls" style="width: max-content; display: inline-block; margin-right: 15px; padding-right: 15px; border-right: 1px solid white;">Dalls TX 75229</li>
                <li class="country" style="width: max-content; display: inline-block;">USA</li>
            </ul>
        </div>
        <div class="email-footer-social-icos" style="margin-top: 25px;">
            <ul style="list-style: none; padding: 0 ; text-align: center;">
                <li style="margin-right: 15px; width: max-content; display: inline-block;"><a href="facebook.com" style="color: white;"><span>
<img style="    width: 24px;
height: 24px;" src="https://cdn.shopify.com/s/files/1/0270/4156/2685/files/faceboog.png?v=1597652235">
                    </svg></span></a></li>
                <li style="width: max-content; display: inline-block;"><a href="instagram.com" style="color:white"><span>
<img src="https://cdn.shopify.com/s/files/1/0270/4156/2685/files/New_Text_Document_5.png?v=1597652235">
</span></a></li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
