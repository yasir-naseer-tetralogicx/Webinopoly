<?php

namespace App\Exports;

use App\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class CustomersExport implements FromView
{

    private $customers;

    /**
     * CustomersExport constructor.
     * @param $customers
     */
    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    /**
     * @inheritDoc
     */
    public function view(): View
    {
        return view('exports.customers', [
            'customers' => $this->customers
        ]);
    }
}
