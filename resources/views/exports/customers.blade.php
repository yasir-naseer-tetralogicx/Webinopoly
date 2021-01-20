<table class="table js-table-sections table-hover table-borderless table-striped table-vcenter">
    <thead>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Total Orders</th>
        <th>Total Spends</th>

    </tr>
    </thead>
    <tbody class="">
    @foreach($customers as $customer)
        <tr>
            <td>{{$customer->first_name}}  </td>
            <td>{{$customer->last_name}}</td>
            <td>{{$customer->email}}</td>
            <td>@if(isset($customer->phone))
                    {{$customer->phone}}
                @endif</td>
            <td>{{count($customer->has_orders)}}</td>
            <td>
                {{number_format($customer->has_orders->sum('cost_to_pay'),2)}} USD
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
