<?php

namespace App\Imports;
use App\AdminFileTemp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class BulkTrackingImport  extends HeadingRowFormatter implements ToModel,WithHeadingRow
{

    private $file;

    /**
     * BulkTrackingImport constructor.
     * @param $file
     */
    public function __construct($file)
    {
        HeadingRowFormatter::default('none');
        $this->file = $file;
    }


    /**
     * @inheritDoc
     */
    public function model(array $row)
    {
        if (!isset($row['Order Name']) || !isset($row['Order ID']) || !isset($row['Tracking Company']) || !isset($row['Tracking Number'])  || !isset($row['Tracking URL']) || !isset($row['Tracking Notes'])) {
            return null;
        }
        $valid_comapanies = '4PX,APC,Amazon Logistics UK,Amazon Logistics US,Anjun Logistics,Australia Post,Bluedart,Canada Post,Canpar,China Post,Chukou1,Correios,Couriers Please,DHL Express,DHL eCommerce,DHL eCommerce Asia,DPD,DPD Local,DPD UK,Delhivery,Eagle,FSC,Fastway Australia,FedEx,GLS,GLS (US),Globegistics,Japan Post (EN),Japan Post (JA),La Poste,New Zealand Post,Newgistics,PostNL,PostNord,Purolator,Royal Mail,SF Express,SFC Fulfillment,Sagawa (EN),Sagawa (JA),Sendle,Singapore Post,StarTrack,TNT,Toll IPEC,UPS,USPS,Whistl,Yamato (EN),Yamato (JA),YunExpress';
        $valid_comapanies_array = explode(',',$valid_comapanies);

        if(!isset($row['Tracking Company']) && !in_array($row['Tracking Company'],$valid_comapanies_array)){
            return null;
        }

        return new AdminFileTemp([
            'order_name' => $row['Order Name'],
            'order_id' => $row['Order ID'],
            'tracking_company' => $row['Tracking Company'],
            'tracking_number' => $row['Tracking Number'],
            'tracking_url' => $row['Tracking URL'],
            'tracking_notes' => $row['Tracking Notes'],
            'file_id' => $this->file->id,
        ]);
    }
}
