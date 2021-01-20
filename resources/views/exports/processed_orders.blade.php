
    <table class="table js-table-sections table-hover table-borderless table-striped table-vcenter">
        <thead>
        <tr>
            <th>Order ID</th>
            <th>SKU</th>
            <th>Order Date</th>
            <th>Cost</th>
            <th>Status</th>
        </tr>
        </thead>

        @foreach($orders as $index => $order)
            <tbody class="">
            <tr>
                <td class="font-w600">{{ $order->name }}</td>
                <td class="font-w600">{{ $order->imported->sku }}</td>
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
            </tr>
            </tbody>
        @endforeach
    </table>
