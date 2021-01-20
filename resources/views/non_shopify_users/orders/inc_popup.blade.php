<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="block block-rounded block-themed block-transparent mb-0">
            <div class="block-content cst_content_wrapper font-size-sm text-center">
                <h2>Are your sure?</h2>
                <div class="text-center"> <p>
                        Subtotal: {{number_format($order->cost_to_pay,2)}} USD
                        <br>
                        WeFullFill Paypal Fee ({{$settings->paypal_percentage}}%): {{number_format($order->cost_to_pay*$settings->paypal_percentage/100,2)}} USD
                        <br>Total Cost : {{number_format($order->cost_to_pay+($order->cost_to_pay*$settings->paypal_percentage/100),2)}} USD</p>
                </div>
                <p> A amount of  {{number_format($order->cost_to_pay+($order->cost_to_pay*$settings->paypal_percentage/100),2)}} USD will be deducted through your Paypal Account</p>

                <div class="paypal_btn_trigger">
                    <div id="paypal-button-container"></div>
                </div>

            </div>
            <div class="block-content block-content-full text-center border-top">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
