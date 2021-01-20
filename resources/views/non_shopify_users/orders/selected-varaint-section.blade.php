<div class="table-responsive">
    <table class="table table-borderless table-striped table-vcenter">
        <tbody class="variants-body">
        @foreach($variants as $variant)
            <input type="hidden" name="line_items[]" class="line_items_ids" value="{{$variant->id}}">
            <tr>
                <td class="text-center">
                    <img class="img-avatar " style="border: 1px solid whitesmoke"
                         @if($variant->has_image == null)  src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                         @else @if($variant->has_image->isV == 0) src="{{asset('images')}}/{{$variant->has_image->image}}" @else src="{{asset('images/variants')}}/{{$variant->has_image->image}}" @endif @endif alt="">
                </td>
                <td class="font-w600" style="vertical-align: middle">
                   {{$variant->linked_product->title}} - {{ $variant->title }}
                </td>

                <td style="vertical-align: middle">
                    ${{ number_format($variant->price, 2) }}
                </td>
                <td style="vertical-align: middle">
                    X
                </td>
                <td style="width: 15%"><input type="number" name="quantity[]" min="1" class="form-control line-item-quantity" data-price="{{$variant->price}}" value="1"></td>
                <td style="vertical-align: middle;text-align: center">
                    <i class="delete-row fa fa-times"></i>
                </td>
            </tr>
        @endforeach

        @foreach($single_variants as $product)
            <input type="hidden" name="single_variant_line_items[]" class="single_line_items_ids" value="{{$product->id}}">
            <tr>
                <td class="text-center">
                    @if(count($product->has_images) > 0)
                        @foreach($product->has_images()->orderBy('position')->get() as $index => $image)
                            @if($index == 0)
                                @if($image->isV == 0)
                                    <img class="img-avatar"  src="{{asset('images')}}/{{$image->image}}">
                                @else   <img class="img-avatar"  src="{{asset('images/variants')}}/{{$image->image}}" alt="">
                                @endif
                            @endif
                        @endforeach
                    @else
                        <img class="img-avatar" src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                    @endif
                </td>
                <td class="font-w600" style="vertical-align: middle">
                    {{ $product->title }}
                </td>

                <td style="vertical-align: middle">
                    ${{ number_format($product->price, 2) }}
                </td>
                <td style="vertical-align: middle">
                    X
                </td>
                <td style="width: 15%"><input type="number" name="single_quantity[]" min="1" class="form-control line-item-quantity" data-price="{{$product->price}}" value="1"></td>
                <td style="vertical-align: middle;text-align: center">
                    <i class="delete-row fa fa-times"></i>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-md-8">

    </div>
    <div class="col-md-4">
        <div class="p-2 h5" style="text-align: end">
           Subtotal : <span class="badge badge-success total-cost-badge"> {{number_format($total,2)}} USD </span>
        </div>
        <div class="p-2 h5" style="text-align: end">
            Shipping Cost : <span class="badge badge-light total-ship-badge"> To be Estimate</span>
        </div>
        <div class="p-2 h5" style="text-align: end">
            Total : <span class="badge badge-success total-final-badge"> {{number_format($total,2)}} USD </span>
        </div>
    </div>
</div>
