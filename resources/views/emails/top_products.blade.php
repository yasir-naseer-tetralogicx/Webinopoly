<!DOCTYPE html>
<html>
<head>
    <title>Top Products</title>
</head>
<body style="margin: 0">
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
        padding: 1px 20px
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
                    <div class="wrap">
                        <div class="left">
                            <h3 style="color: #ffffff; margin-right: 5px;">Our Top Products</h3>
                        </div>
                    </div>

                    <div class="" style="padding: 15px;">
                        <table class="table table-borderless table-striped table-vcenter">
                            <thead>
                            <tr class="" style="text-align: left;"></tr>
                            </thead>
                            <tbody>
                                @foreach($top_products_stores as $product)
                                    @php
                                        $prods = json_decode($template->products);
                                    @endphp

                                @if(in_array($product->id, $prods))
                                    <tr style="text-align: left">
                                        <td class="">
                                            @foreach($product->has_images()->orderBy('position')->get() as $index => $image)
                                                @if($index == 0)
                                                    @if($image->isV == 0)
                                                        <img class=""  src="{{asset('images')}}/{{$image->image}}" style="width: 80px !important; height: auto;"alt="">
                                                    @else
                                                        <img class=""  src="{{asset('images/variants')}}/{{$image->image}}" alt="" style="width: 80px !important; height: auto;">
                                                    @endif
                                                @endif
                                            @endforeach
                                            <a href="{{route('store.product.wefulfill.show',$product->id)}}" class="title">{{$product->title}}</a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                        <br><br>
                        <hr>
                    </div>
                </div>
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
