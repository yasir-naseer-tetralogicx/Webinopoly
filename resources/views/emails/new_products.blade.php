<!DOCTYPE html>
<html>
<head>
    <title>New Products</title>
    <style>
        .email-body
        {
            color:black;
        }
        .email-content
        {
            /*max-width: 450px;*/
            width : 90%;
        }
        .email-content-detail
        {
            margin: 50px 0;
        }

        .wrap {
            padding-left: 20px;
            background-color: #7daa40 !important;
            color: #ffffff !important;
            padding: 1px 20px;
        }

        .wrap .right{
            text-align: right !important;
        }

        .wrap .left{
            text-align: left !important;
        }

        a.title {
            text-decoration: none;
            color: #7daa40 !important;
            font-size: 18px;
            margin-left: 5px;
        }

        a.title:hover{
            text-decoration: none;
            color: #7daa40 !important;
            font-size: 18px;
            margin-left: 5px;

        }

        a.title:active{
            text-decoration: none;
            color: #7daa40 !important;
            font-size: 18px;
            margin-left: 5px;

        }

        .wrapper{
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        .product_div{
            margin: 10px 0;
            box-sizing: border-box;
            padding: 10px;
            width: 33.3%;
        }

        .product_div p{
            text-align: left;
        }

        .product_price{
            color: #ff0000db;
            font-weight: bold;

        }

        .product_div .product_img{
            width: 100%;
            height: auto;
        }

        .inner{
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            -webkit-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
            -moz-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
            box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
        }

        .product-btn{
            width: 100%;
            background-color: #1f6fb2;
            color: white;
            padding: 15px 0;
            border-radius: 5px;
            border: 1px solid #1f6fb2;
            font-size: 16px;
            display: block;
            text-decoration: none;
        }

        .clear{
            content: "";
            display: table;
            clear: both;

        }



        @media (max-width: 742px) {
            .product_div {
                width: 50%;
            }
        }
        @media (max-width: 570px) {
            .email_btn
            {
                padding:15px 30px !important;
                font-size:18px !important;
            }

            .product_div {
                width: 100%;
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
</head>
<body style="margin: 0">

<div class="email-body" style="padding: 20px;max-width: 80%;margin: auto; font-family: cursive;">
    <div class="email-contaner" style="border: 2px solid #7daa40;padding: 25px;">
        <div class="email-content" style="margin: auto;  text-align: center; ">
            <div class="email-logo">
                <img src="https://cdn.shopify.com/s/files/1/0370/7361/7029/files/image_3.png?v=1585895317" alt="Wefullfill" style="width: 35%">
            </div>

            <div class="email-content-detail" style="margin: 50px 0;">
                <h1 class="email-title" style="margin: 0;margin-bottom: 30px;font-size: 34px;">{{ $template->subject }}</h1>
                <p class="email-message-1" style=" margin: 0;margin-bottom: 30px;font-size: 20px;line-height: 1.53;" >{{ $template->body }} </p>
                <hr>

                <div class="" style="width: 100%">
                    <div style=" padding-left: 20px; background-color: #7daa40 !important;color: #ffffff !important;padding: 1px 20px;">
                        <div style=" text-align: left !important;">
                            <h3 style="color: #ffffff; margin-right: 5px;">Our Top Products</h3>
                        </div>
                    </div>
                </div>
                <div style="width: 100%;">
                    @foreach($new_products as $product)
                            <div style=" margin: 10px 0;
                            box-sizing: border-box;
                            padding: 10px;
                            float: right;
                            display: inline;
                            width: 33%;
                            ">
                                <div style="    padding: 15px;
                                                border: 1px solid #ccc;

                                                border-radius: 5px;
                                                -webkit-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
                                                -moz-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
                                                box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
                                                text-align: center">
                                    @foreach($product->has_images()->orderBy('position')->get() as $index => $image)
                                        @if($index == 0)
                                            @if($image->isV == 0)

                                                <img style=" width: 70%;height: auto;"  src="{{asset('images')}}/{{$image->image}}">
                                            @else
                                                <img style=" width: 70%;height: auto;"  src="{{asset('images/variants')}}/{{$image->image}}">
                                            @endif
                                        @endif
                                    @endforeach
                                    <p><a href="{{route('store.product.wefulfill.show',$product->id)}}" class="title">{{$product->title}}</a></p>
                                    <p class=" color: #ff0000db;
                                                font-weight: bold;">From ${{ $product->price }}</p>
                                    <a href="{{route('store.product.wefulfill.show',$product->id)}}" style="  width: 100%;
                                            background-color: #1f6fb2;
                                            color: white;
                                            padding: 15px 0;
                                            border-radius: 5px;
                                            border: 1px solid #1f6fb2;
                                            font-size: 16px;
                                            display: block;
                                            text-decoration: none;">View Product</a>
                                </div>
                            </div>
                    @endforeach
                </div>
                <br><br><br><br>
                <hr>
                <div class="clear"></div>
                <div>
                    <a href="{{ route('store.product.wefulfill') }}" target="_blank" class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">View Products</a>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="email-footer" style=" padding: 25px 10px; color: white; ">

    <div class="email-footer-caption">
        <ul style=" color: white; list-style: none; padding: 0 ;  margin-top: 25px;text-align: center; ">
            <li class="site-name" style="width: max-content; display: inline-block; margin-right: 15px;padding-right: 15px;border-right: 1px solid white;"><a href="" style="color: white;text-decoration: none;">WeFullFill</a></li>
            <li class="dalls" style="width: max-content; display: inline-block; margin-right: 15px; padding-right: 15px; border-right: 1px solid white;">ROOM 2103 TUNG CHIU COMMERCIAL CENTRE 193,LOCKHART ROAD WAN</li>
            <li class="country" style="width: max-content; display: inline-block;">China</li>
        </ul>
    </div>

</div>

</body>
</html>


