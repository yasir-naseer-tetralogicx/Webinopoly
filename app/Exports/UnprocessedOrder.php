<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class UnprocessedOrder implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data;

    /**
     * UnprocessedOrder constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
 * @inheritDoc
 */public function view(): View
{
    // TODO: Implement view() method.
    return view('exports.unprocessed_orders', [
        'data' => $this->data
    ]);
}


}
