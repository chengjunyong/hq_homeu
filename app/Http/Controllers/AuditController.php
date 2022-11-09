<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Do_list;

use App\Category;
use App\Department;
use App\transaction;
use App\Product_list;
use App\Branch_product;
use App\Warehouse_stock;
use App\transaction_detail;
use Illuminate\Http\Request;
use App\Branch_stock_history;
use App\StockCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AuditController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth', 'user_access']);

      $this->url = route('home')."?p=audit_menu";
    }

    public function stockMovementMenu()
    {
        $branches = Branch::all();

        return view('audit.stock-movement-menu')->with('url',$this->url)->with('branches',$branches);
    }

    public function getStockMovementMenu(Request $request)
    {
        $product = Product_list::where('product_name',$request->product)->first();
        $date_target = $request->report_date_from." Til ".$request->report_date_to;
        if($product == null){
            return back()->with('error','Product Not Found');
        }

        $selected_branch = "";

        if($request->branch != 'warehouse'){
            $branch = Branch::find($request->branch);
            $selected_branch = $branch->name;
            $stock_transfer = Do_list::join('do_detail as dd','dd.do_number','=','do_list.do_number')
                                        ->where('do_list.completed_time','>',$request->report_date_from)
                                        ->where('do_list.completed_time','<=',$request->report_date_to." 23:59:59")
                                        ->where('do_list.to_branch_id',$branch->id)
                                        ->where('dd.barcode',$product->barcode)
                                        ->groupBy('dd.do_number')
                                        ->select('do_list.do_number AS transaction_no','dd.quantity','dd.price','do_list.completed_time AS transaction_date');                          

            $transaction_data = transaction::join('transaction_detail as td',function($q) use ($branch,$request){
                                                $q->on('transaction.branch_transaction_id','=','td.branch_transaction_id');
                                                $q->on('td.transaction_date','>',DB::raw("'".$request->report_date_from."'"));
                                                $q->on('td.transaction_date','<=',DB::raw("'".$request->report_date_to." 23:59:59'"));
                                            })
                                            ->where('transaction.transaction_date','>',$request->report_date_from)
                                            ->where('transaction.transaction_date','<=',$request->report_date_to." 23:59:59'")
                                            ->where('td.barcode',$product->barcode)
                                            ->where('transaction.branch_id',$branch->token)
                                            ->select('transaction.transaction_no','td.quantity','td.price','transaction.transaction_date')
                                            ->union($stock_transfer)
                                            ->get();
        }else{
            $selected_branch = "warehouse";

            $stock_transfer = Do_list::join('do_detail as dd','dd.do_number','=','do_list.do_number')
                                        ->where('do_list.completed_time','>',$request->report_date_from)
                                        ->where('do_list.completed_time','<=',$request->report_date_to." 23:59:59")
                                        ->where('do_list.to_branch_id',0)
                                        ->where('dd.barcode',$product->barcode)
                                        ->groupBy('dd.do_number')
                                        ->select('do_list.do_number AS transaction_no','dd.quantity','dd.price','do_list.completed_time AS transaction_date');   

        }

        return view('audit.stock-movement-report',compact('transaction_data','product','date_target','selected_branch'));
    }

    public function ajaxStockMovementMenu(Request $request)
    {
        $product = Product_list::where('product_name',$request->product)->first();

        if($product == null){
            return response()->json("error");
        }
        $branch = Branch::find($request->branch);

        $stock_transfer = Do_list::join('do_detail as dd','dd.do_number','=','do_list.do_number')
                                    ->where('do_list.completed_time','>',$request->report_date_from)
                                    ->where('do_list.completed_time','<=',$request->report_date_to." 23:59:59")
                                    ->where('do_list.to_branch_id',$branch->id)
                                    ->where('dd.barcode',$product->barcode)
                                    ->groupBy('dd.do_number')
                                    ->select('do_list.do_number AS transaction_no','dd.quantity','dd.price','do_list.completed_time AS transaction_date');

        $transaction_data = transaction::join('transaction_detail as td',function($q) use ($branch,$request){
                                        $q->on('transaction.branch_transaction_id','=','td.branch_transaction_id');
                                        $q->on('td.transaction_date','>',DB::raw("'".$request->report_date_from."'"));
                                        $q->on('td.transaction_date','<=',DB::raw("'".$request->report_date_to." 23:59:59'"));
                                    })
                                    ->where('transaction.transaction_date','>',$request->report_date_from)
                                    ->where('transaction.transaction_date','<=',$request->report_date_to." 23:59:59'")
                                    ->where('td.barcode',$product->barcode)
                                    ->where('transaction.branch_id',$branch->token)
                                    ->select('transaction.transaction_no','td.quantity','td.price','transaction.transaction_date')
                                    ->union($stock_transfer)
                                    ->get();

        //Start exporting
        $files = Storage::allFiles('public/report');
        Storage::delete($files);                  
        if(!Storage::exists('public/report'))
        {
        Storage::makeDirectory('public/report', 0775, true); //creates directory
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A4:G4');
        $sheet->getStyle('A1:g1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:g2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:g3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:g4')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
        $sheet->setCellValue('A2', 'Item Movement Report - '.$branch->branch_name);
        $sheet->setCellValue('A3', 'Date: '.$request->report_date_from." til ".$request->report_date_to."");
        $sheet->setCellValue('A4', 'Product: '.$product->product_name." ( ".$product->barcode." )");

        $sheet->setCellValue('A6','No');
        $sheet->setCellValue('B6','Type');
        $sheet->setCellValue('C6','Transaction No');
        $sheet->setCellValue('D6','Price');
        $sheet->setCellValue('E6','Qty');
        $sheet->setCellValue('F6','Total');
        $sheet->setCellValue('G6','Transaction Date');

        $start = 7;
        foreach($transaction_data as $index => $result){
            $sheet->setCellValue('A'.$start, $index+1);
            if(str_contains($result->transaction_no,"WTS")){
                $sheet->setCellValue('B'.$start, 'Warehouse Transfer');
            }else{
                $sheet->setCellValue('B'.$start, 'Sales');
            }
            $sheet->setCellValue('C'.$start, $result->transaction_no);
            $sheet->setCellValue('D'.$start, number_format($result->price,2));
            if(str_contains($result->transaction_no,"WTS")){
                $sheet->setCellValue('E'.$start, '+'.$result->quantity);
            }else{
                $sheet->setCellValue('E'.$start, '-'.$result->quantity);
            }
            $sheet->setCellValue('F'.$start, number_format($result->price * $result->quantity,2));
            $sheet->setCellValue('G'.$start, date("d-M-Y H:i:s A",strtotime($result->transaction_date)));
            $start++;
        }

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

        $date = strtotime("now");
        $writer = new Xlsx($spreadsheet);
        $path = 'storage/report/Item Movement Report - '.$date.'.xlsx';
        $writer->save($path);

        return response()->json($path);
    }

    public function stockCheckList(Request $request)
    {
        $branch = Branch::all();
        if(isset($request->branch)){
            $data = StockCheck::with('branch','product','user')->where('destination',$request->branch)->get();
        }else{
            $data = [];
        }

        return view('audit.stock-checklist',compact('branch','data'))->with('url',$this->url);
    }

    public function changeStockCheckQuantity(Request $request)
    {
        $stock = StockCheck::where('id',$request->id)->first();
        $raw = json_decode($stock->raw);
        $raw->quantity = $request->qty;
    
        $stock->update([
            'stock_count' => $request->qty,
            'raw' => json_encode($raw),
        ]);

        return "true";
    }

    public function deleteStockCheck(Request $request)
    {
        StockCheck::where('id',$request->id)->delete();

        return "true";
    }

    public function approveStockCheck(Request $request)
    {
        $stocks = StockCheck::where('destination',$request->branch_id)->get();
        if($request->branch_id == 'warehouse'){

            foreach($stocks as $result){
                $data = json_decode($result->raw,true);
                Warehouse_stock::where('barcode',$result->barcode)
                            ->update($data);
            }
        }else{

            foreach($stocks as $result){
                $data = json_decode($result->raw,true);
                Branch_product::where('barcode',$result->barcode)
                                ->where('branch_id',$request->branch_id)
                                ->update($data);
            }
        }

        StockCheck::where('destination',$request->branch_id)->delete();

        return "true";
    }
}
