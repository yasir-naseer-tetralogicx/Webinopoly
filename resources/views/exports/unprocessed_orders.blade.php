<table class="table table-hover table-borderless table-striped table-vcenter">
    <thead>
    <tr>

        <th>Order Id</th>
        <th>SKU</th>
        <th>Quantity</th>
        <th>Name</th>
        <th>Email</th>
        <th>Country</th>
    </tr>
    </thead>

    @foreach($data as $index => $item)
        <tbody class="">
        <tr>
            <td>{{$item->order_number}}</td>
            <td>{{$item->sku}}</td>
            <td>{{$item->quantity}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->email}}</td>
            <td>{{$item->country}}</td>
        </tr>
        </tbody>

    @endforeach
</table>
