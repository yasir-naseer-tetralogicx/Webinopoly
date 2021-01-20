<div @if(count($stores) > 5) class="sales-stores-section"  @else class="mb2" @endif >
    <label style="margin-left: 15px" for="material-error">Stores</label>
    @if(count($stores) > 0)
        @foreach($stores as $store)
            <div class="col-md-12">
                <div class="custom-control custom-checkbox d-inline-block">
                    <input type="checkbox" name="stores[]" value="{{$store->id}}" class="custom-control-input checkbox-to-check" id="store_{{$store->id}}">
                    <label class="custom-control-label"  for="store_{{$store->id}}">{{explode('.',$store->shopify_domain)[0]}} ({{$store->shopify_domain}})</label>
                </div>
            </div>

        @endforeach
    @else
        <div class="col-md-12">
            <p> No Store Available</p>
        </div>
    @endif
</div>
<div @if(count($users) > 5) class="sales-stores-section" @else class="mb2" @endif>
    <label style="margin-left: 15px" for="material-error">Non-Shopify Users</label>
    @if(count($users) > 0)
        @foreach($users as $user)
            <div class="col-md-12">
                <div class="custom-control custom-checkbox d-inline-block">
                    <input type="checkbox" name="users[]" value="{{$user->id}}" class="custom-control-input checkbox-to-check" id="user_{{$user->id}}">
                    <label class="custom-control-label"  for="user_{{$user->id}}">{{$user->name}} ({{$user->email}})</label>
                </div>
            </div>
        @endforeach
    @else  <div class="col-md-12">
        <p> No User Available</p>
    </div>
    @endif
</div>
