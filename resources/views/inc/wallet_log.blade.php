<div class="row">
    <div class="col-md-12">
        <div class="block">
            <div class="block-header">
                <div class="block-title">
                    Wallet History
                </div>
            </div>
            <div class="block-content">
                <ul class="timeline timeline-alt">
                    @foreach($wallet->logs()->orderBy('id','DESC')->get() as $log)
                    <li class="timeline-event">
                        @if($log->status == "CREATED")
                        <div class="timeline-event-icon bg-warning">
                            <i class="fa fa-sync"></i>
                        </div>
                        @elseif($log->status == "Top-up Request Through Bank Transfer")
                        <div class="timeline-event-icon bg-primary">
                            <i class="fa fa-dollar-sign"></i>
                        </div>
                        @elseif($log->status == "Bank Transfer Approved")
                        <div class="timeline-event-icon bg-success">
                            <i class="fa fa-dollar-sign"></i>
                        </div>
                        @elseif($log->status == "Top-up By Admin")
                        <div class="timeline-event-icon bg-success">
                            <i class="fa fa-star"></i>
                        </div>
                        @elseif($log->status == "Wallet Amount updated by Admin")
                            <div class="timeline-event-icon bg-success">
                                <i class="fa fa-star"></i>
                            </div>
                        @elseif($log->status == "Top-up By Manager")
                            <div class="timeline-event-icon bg-success">
                                <i class="fa fa-user"></i>
                            </div>
                        @elseif($log->status == "Order Payment")
                        <div class="timeline-event-icon bg-amethyst">
                            <i class="fa fa-credit-card" aria-hidden="true"></i>
                        </div>

                        @elseif($log->status == "Admin Edit Wallet Payment")
                            <div class="timeline-event-icon bg-amethyst">
                                <i class="fa fa-edit" aria-hidden="true"></i>
                            </div>

                        @elseif($log->status == "Top-up By Paypal")
                            <div class="timeline-event-icon bg-success">
                                <i class="fab fa-paypal"></i>
                            </div>
                        @elseif($log->status == "Top-up through Refund")
                            <div class="timeline-event-icon bg-danger">
                                <i class="fa fa-money-bill"></i>
                            </div>
                        @elseif($log->status == "Completed")
                        <div class="timeline-event-icon" style="background: darkslategray">
                            <i class="fa fa-check"></i>
                        </div>
                        @endif
                        <div class="timeline-event-block block js-appear-enabled animated fadeIn" data-toggle="appear">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">{{$log->status}}</h3>
                                <div class="block-options">
                                    <div class="timeline-event-time block-options-item font-size-sm font-w600">
                                        {{date_create($log->created_at)->format('d M, Y h:i a')}}
                                    </div>
                                </div>
                            </div>
                            <div class="block-content">
                                <p> {{$log->message}} </p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
