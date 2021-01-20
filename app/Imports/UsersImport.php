<?php

namespace App\Imports;

use App\UserFileTemp;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class UsersImport extends HeadingRowFormatter implements ToModel,WithHeadingRow
{

    /**
     * @param array $row
     *
     * @return Model|null
     */

    private $file_id;
    private $user_id;

    /**
     * UsersImport constructor.
     * @param $file_id
     * @param $user_id
     */
    public function __construct($file_id, $user_id)
    {
        HeadingRowFormatter::default('none');
        $this->file_id = $file_id;
        $this->user_id = $user_id;
    }


    public function model(array $row)
    {
        if (!isset($row['Order Id']) || !isset($row['Name']) || !isset($row['Contact phone']) || !isset($row['Postcode']) || !isset($row['Country']) || !isset($row['Sku']) || !isset($row['Quantity']) || !isset($row['Province']) || !isset($row['Order date'])) {
            return null;
        }
       $usertemp =  new UserFileTemp([
            'order_number' => $row['Order Id'],
            'quantity' => $row['Quantity'],
            'sku' => $row['Sku'],
            'name' => $row['Name'],
            'address1' => $row['Address'],
            'address2' => $row['Address2'],
            'city' => $row['City'],
            'province' => $row['Province'],
            'postcode' => $row['Postcode'],
            'country' => $row['Country'],
            'phone' => $row['Contact phone'],
            'email' => $row['Email'],
            'user_id' => $this->user_id,
            'file_id' => $this->file_id,
        ]);
//        $usertemp->timestamps = false;
        $time = strtotime($row['Order date']);
        $newformat = date('Y-d-m H:i:s',$time);
        $usertemp->created_at =  $newformat;
        $usertemp->save();

        return $usertemp;
    }
}
