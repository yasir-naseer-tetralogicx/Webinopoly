<?php

namespace App\Exports;

use App\RetailerOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class OrdersExport implements FromView
{
    private $orders;

    /**
     * RetailerOrderExport constructor.
     * @param $orders
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @inheritDoc
     */
    public function view(): View
    {
        return view('exports.orders', [
            'orders' => $this->orders
        ]);
    }
}
