<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{


    public function getUnpaidInvoices(Request $request)
    {
        // Validate the request
        $request->validate([
            'month' => 'required|date_format:Y-m', // Ensure 'month' is in 'YYYY-MM' format
        ]);

        // Extract the end date of the specified month
        $month = $request->month;
        $endDate = date('Y-m-t', strtotime($month . '-01')); // Last day of the specified month

        // Query invoices before the specified month and completely unpaid
        $invoices = Invoice::with('payments')
            ->where('date', '<', $endDate) // Before the specified month
            ->get()
            ->filter(function ($invoice) {
                $totalPaid = $invoice->payments->sum('amount');
                return $totalPaid < $invoice->total_invoice_amount; // Not fully paid
            })
            ->values(); // Reset collection keys

        // Map the response to a list format
        $mappedInvoices = $invoices->map(function ($invoice) {
            return [
                'invoice_id' => $invoice->id,
                'estate_id' => $invoice->estate_id,
                'estate_name' => optional($invoice->estate)->name, // Assuming 'name' is a field in Estate
                'meter_number' => $invoice->meter_number,
                'account_number' => $invoice->account_number,
                'total_invoice_amount' => $invoice->total_invoice_amount,
                'type' => $invoice->type,
                'date' => $invoice->date,
                'payments' => $invoice->payments->map(function ($payment) {
                    return [
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'date' => $payment->date,
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Return the result
        return response()->json([
            'status' => 'success',
            'unpaid_invoices' => $mappedInvoices, // List format
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // جلب جميع الفواتير مع مجموع الدفعات لكل فاتورة
        $invoices = Invoice::with('payments')
            ->get()
            ->map(function ($invoice) {
                // حساب مجموع الدفعات لكل فاتورة
                $invoice->total_payments = $invoice->payments->sum('amount');
                return $invoice;
            });


        return response()->json([
            'invoices'=>$invoices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $invoice = Invoice::create([
            'estate_id'=>$request['estate_id'],
            'meter_number'=>$request['meter_number'],
            'account_number'=>$request['account_number'],
            'total_invoice_amount'=>$request['total_invoice_amount'],
            'type'=>$request['type'],
            'date'=>$request['date']
        ]);
        $invoice->save();
        return response()->json([
            'message'=>'created successfully',
            'invoice'=>$invoice
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::find($id);
        return response()->json([
            'invoice'=>$invoice
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::find($id);
        if(!$invoice){
            return response()->json([
                'message'=>'not found'
            ]);
        }
        $invoice->update([
            'estate_id'=>$request['estate_id'],
            'meter_number'=>$request['meter_number'],
            'account_number'=>$request['account_number'],
            'total_invoice_amount'=>$request['total_invoice_amount'],
            'type'=>$request['type'],
            'date'=>$request['date'],
        ]);
        return response()->json([
            'message'=>'updated successfully',
            'invoice'=>$invoice
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice){
            return response()->json([
                'message'=>'invoice not found',
            ]);
        }
        $invoice->delete();
        return response()->json([
            'message'=>'deleted successfully',
        ]);
    }

    public function filter(Request $request)
    {
        $query = Invoice::query();

        // تطبيق الفلترة حسب النوع
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        // تطبيق الفلترة حسب رقم العداد
        if ($request->has('meter_number')) {
            $query->where('meter_number', $request->input('meter_number'));
        }

        // تطبيق الفلترة حسب التاريخ
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        } elseif ($request->has('start_date')) {
            $query->where('date', '>=', $request->input('start_date'));
        } elseif ($request->has('end_date')) {
            $query->where('date', '<=', $request->input('end_date'));
        }

        return response()->json([
            'query'=> $query->get() ,
        ]);
    }
    public function getInvoicePayments($invoiceId)
    {
        // البحث عن الفاتورة باستخدام معرفها
        $invoice = Invoice::with('payments')->find($invoiceId);

        // التحقق من وجود الفاتورة
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
       $invoice_payment = $invoice->payments;

        return response()->json([
            'invoice_payment'=> $invoice_payment
        ]);
    }

}
