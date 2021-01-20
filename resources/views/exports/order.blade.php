<table class="table js-table-sections table-hover table-borderless table-striped table-vcenter">
    <thead>
    <tr>
        <th>Order ID</th>
        <th>Line Item</th>
        <th>SKU</th>
        <th>Quantity</th>
        <th>Line Item Status</th>
        <th>Line Item Cost</th>
        <th>Order Date</th>
        <th>Order Cost</th>
        <th>Order Status</th>
        <th>Customer</th>
        <th>Customer Email</th>
        <th>Customer Phone</th>
        <th>Shipping Address 1</th>
        <th>Shipping Address 2</th>
        <th>Shipping City</th>
        <th>Shipping State</th>
        <th>Shipping Country </th>
        <th>Shipping Zipcode</th>


    </tr>
    </thead>
    @php
    if($order->shipping_address != null){
      $shipping = json_decode($order->shipping_address);
        }
    @endphp

        <tbody class="">
        @foreach($order->line_items as $item)
        <tr>
            <td class="font-w600">{{ $order->name }}</td>
            <td class="font-w600">{{ $item->name }}</td>
            <td class="font-w600">{{ $item->sku }}</td>
            <td>{{$item->quantity}}</td>
            @if($item->fulfillment_status != null)
            <td>
                Fulfilled
            </td>
                @else
                <td>Unfulfilled</td>
            @endif
            <td>
                {{$item->cost}}
            </td>
            <td>
                {{date_create($order->shopify_created_at)->format('D m, Y h:i a') }}
            </td>
            <td>
                {{number_format($order->cost_to_pay,2)}} {{$order->currency}}

            </td>
            <td>
                @if($order->status == 'paid')
                    <span class="badge badge-primary" style="float: right;font-size: medium"> {{$order->status}}</span>

                @elseif($order->status == 'unfulfilled')
                    <span class="badge badge-warning" style="font-size: small"> {{$order->status}}</span>
                @elseif($order->status == 'partially-shipped')
                    <span class="badge " style="font-size: small;background: darkolivegreen;color: white;"> {{$order->status}}</span>
                @elseif($order->status == 'shipped')
                    <span class="badge " style="font-size: small;background: orange;color: white;"> {{$order->status}}</span>
                @elseif($order->status == 'delivered')
                    <span class="badge " style="font-size: small;background: deeppink;color: white;"> {{$order->status}}</span>
                @elseif($order->status == 'completed')
                    <span class="badge " style="font-size: small;background: darkslategray;color: white;"> {{$order->status}}</span>
                @elseif($order->status == 'new')
                    <span class="badge badge-warning" style="font-size: small"> Draft </span>
                @else
                    <span class="badge badge-success" style="font-size: small">  {{$order->status}} </span>
                @endif

            </td>
            <td>{{$shipping->first_name}} {{$shipping->last_name}} </td>
            <td>{{$order->email}}</td>
            <td>@if(isset($shipping->phone))
                  {{$shipping->phone}}
                @endif</td>
            <td>{{$shipping->address1}}</td>
            <td>{{$shipping->address2}}</td>
            <td>{{$shipping->city}}</td>
            <td>{{$shipping->province}}</td>
            <td>{{$shipping->country}}</td>
            <td>{{$shipping->zip}}</td>

        </tr>
            @endforeach
        </tbody>
</table>
