<?php

namespace App\Console\Commands;

use App\Branch;
use App\Product_list;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateStockBalanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:stockBalanceReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = "1,3,4,5,6,7,13,hq";
        $branch_id = explode(",",$data);

        $date = date('d-M-Y h:i:s A', strtotime(now()));
        $branch = Branch::whereIn('id',$branch_id)->get();
        $count = count($branch);

        $branch_list = "";
        foreach($branch as $key => $result){
        if($key+1 == $count){
            $branch_list .= $result->branch_name;
        }else{
            $branch_list .= $result->branch_name.',';
        }
        }

        if(end($branch_id) == 'hq' && $count == 0){
            $branch_list .= 'HQ Warehouse';
        }else if(end($branch_id) == 'hq' && $count != 0){
            $branch_list .= ',HQ Warehouse';
        }

        $product_lists = Product_list::query();
        $product_lists =  $product_lists->with('category','department');

        if(in_array(1,$branch_id)){
        $product_lists = $product_lists->with('wb1');
        }

        if(in_array(3,$branch_id)){
        $product_lists = $product_lists->with('wb2');
        }

        if(in_array(4,$branch_id)){
        $product_lists = $product_lists->with('bak');
        }

        if(in_array(5,$branch_id)){
        $product_lists = $product_lists->with('pc');
        }

        if(in_array(6,$branch_id)){
        $product_lists = $product_lists->with('pm1');
        }

        if(in_array(7,$branch_id)){
        $product_lists = $product_lists->with('pm2');
        }

        if(in_array(13,$branch_id)){
        $product_lists = $product_lists->with('g01');
        }

        if(in_array('hq',$branch_id)){
        $product_lists = $product_lists->with('hq');
        }

        $product_lists = $product_lists->get();

        $row = 5;
        $col = 11;

        $set_col = $col; 
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
        $spreadsheet->getActiveSheet()->mergeCells('A2:I2');
        $spreadsheet->getActiveSheet()->mergeCells('G4:H4');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        //Header
        $sheet->setCellValue('A1', 'HomeU (M) Sdn Bhd');
        $sheet->setCellValue('A2', 'Stock Balance Stock');
        $sheet->setCellValue('A3', 'Date');
        $sheet->setCellValue('B3', $date);
        $sheet->setCellValue('A4', 'Branch');
        $sheet->setCellValue('B4', $branch_list);
        $sheet->setCellValue('G4', 'Balance Stock');
        foreach($branch as $result){
        $sheet->getCellByColumnAndRow($set_col, $row)->setValue($result->branch_name);
        $set_col++;
        }

        if(end($branch_id)=='hq'){
        $sheet->getCellByColumnAndRow($set_col, $row)->setValue('HQ Warehouse');
        $set_col++;
        }

        //Data
        $sheet->setCellValue('A5', 'Bil');
        $sheet->setCellValue('B5', 'Department');
        $sheet->setCellValue('C5', 'Category');
        $sheet->setCellValue('D5', 'Barcode');
        $sheet->setCellValue('E5', 'Product Name');
        $sheet->setCellValue('F5', 'Cost');
        $sheet->setCellValue('G5', 'Total Quantity');
        $sheet->setCellValue('H5', 'Selling Price');
        $sheet->setCellValue('I5', 'Total Cost');

        $count=6;
        $total_balance_stock = 0;
        foreach($product_lists as $key => $product){
        $sheet->setCellValue('A'.$count, $key+1);
        $sheet->setCellValue('B'.$count, $product->department->department_name);
        $sheet->setCellValue('C'.$count, $product->category->category_name);
        $sheet->setCellValue('D'.$count, $product->barcode);
        $sheet->setCellValue('E'.$count, $product->product_name);
        $sheet->setCellValue('F'.$count, $product->cost);
        $sheet->setCellValue('G'.$count, "=SUM(K".$count.":W".$count.")");
        $sheet->setCellValue('H'.$count, $product->price);
        $sheet->setCellValue('I'.$count, "=SUM(F".$count."*G".$count.")");
        $set_col = 11;
        foreach($branch_id as $branch){
            switch($branch){
            case 1:
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->wb1->quantity ?? 0);
                break;
            case 3:
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->wb2->quantity ?? 0);
                break;
            case 4:
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->bak->quantity ?? 0);
                break;
            case 5:
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->pc->quantity ?? 0);
                break;
            case 6:
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->pm1->quantity ?? 0);
                break;
            case 7:
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->pm2->quantity ?? 0);
                break;
            case 13:
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->g01->quantity ?? 0);
                break;
            case 'hq':
                $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->hq->quantity ?? 0);
                break;
            }
            $set_col++;
        }
        $count++;
        }
        $key += 7;
        $sheet->setCellValue('I4', "=SUM(I6:I".$key.")");

        $sheet->getStyle('D')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A5:A25000')->getAlignment()->setHorizontal('left');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(47);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode('################################');

        $writer = new Xlsx($spreadsheet);

        $directory = 'public/storage/monthly-report/';
        if(!Storage::exists('public/monthly-report'))
        {
            Storage::makeDirectory('public/monthly-report/', 0775, true); //creates directory
        }

        $writer = new Xlsx($spreadsheet);
        $path = $directory.'Stock Balance ('.date("m-Y",strtotime('now -1 days')).').xlsx';
        $writer->save($path);

        exit('done');
    }
}
