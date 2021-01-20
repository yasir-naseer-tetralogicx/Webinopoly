<form action="{{ route('store.order.paypal.pay.success', $order->id) }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $order->id }}">
    <textarea name="response"></textarea>
</form>
