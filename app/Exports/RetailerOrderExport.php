<?php

namespace App\Exports;

use App\RetailerOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class RetailerOrderExport implements FromView
{

    private $order;

    /**
     * RetailerOrderExport constructor.
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }


    /**
     * @inheritDoc
     */
    public function view(): View
    {
        return view('exports.order', [
            'order' => $this->order
        ]);
    }
}
