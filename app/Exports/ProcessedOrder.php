<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ProcessedOrder implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $orders;

    /**
     * ProcessedOrder constructor.
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
        // TODO: Implement view() method.
        return view('exports.processed_orders', [
            'orders' => $this->orders
        ]);
    }
}
