
    <label for="">Choose shipping method</label>
    <div class="shipping_hethod_choices">
        <div class="shipping_hethod_choices_items">
            <div class="choices_item t_hed">Shipping Method</div>
            <div class="choices_item t_hed">Delivery Time Estimate (Business Days)</div>
            <div class="choices_item t_hed">Shipping Sub-total</div>
        </div>
        @foreach($zones as $index => $zone)
            @if($zone->has_rate != null)
            @foreach($zone->has_rate as $i => $rate)
                <div class="shipping_hethod_choices_items">
                    <div class="choices_item ">
                        <input type="radio" name="shipping_method" id="shipping_method_1">
                        <label for="shipping_method_1"> {{$zone->name}} / {{$rate->name}}</label></div>
                    <div class="choices_item ">{{$rate->processing_time}}</div>
                    <div class="choices_item shipping_price">$ {{number_format($rate->shipping_price,2)}}</div>
                </div>
                @endforeach
            @endif
        @endforeach
    </div>

