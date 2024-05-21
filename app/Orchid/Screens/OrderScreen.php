<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

class OrderScreen extends Screen
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function pull(Request $request)
    {
        // Validate form data, save task to database, etc.
        $request->validate([
            'order.date' => 'required|date_format:Y-m-d',
        ]);

        $dateRange = $request->input('order.date');
        $pageNo = 1;
        $url = "http://89.108.115.241:6969/api/orders?dateFrom={$dateRange}&dateTo={$dateRange}&page={$pageNo}&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie";

        $dataArray = [];
        $data = [];
        $response = Http::get($url);

        $response = $response->json();
        //var_dump($response);
        $last_page = $pageNo;
        if (isset($response["meta"]))
            $last_page = $response["meta"]["last_page"];
        do {
            // EXECUTE:
            $response = Http::get($url);

            $response = $response->json();

            if (isset($response["data"]))
                $data = $response["data"];
            $dataArray = [...$dataArray, ...$data];
            $pageNo++;
        } while ($pageNo<=$last_page);

        for($i=0; $i<count($dataArray); $i++) {
            $order = new Order();
            $order->g_number = $dataArray[$i]["g_number"];
            $order->date = $dataArray[$i]["date"];
            $order->last_change_date = $dataArray[$i]["last_change_date"];
            $order->supplier_article = $dataArray[$i]["supplier_article"];
            $order->tech_size = $dataArray[$i]["tech_size"];
            $order->barcode = $dataArray[$i]["barcode"];
            $order->total_price = $dataArray[$i]["total_price"];
            $order->discount_percent = $dataArray[$i]["discount_percent"];
            $order->warehouse_name = $dataArray[$i]["warehouse_name"];
            $order->oblast = $dataArray[$i]["oblast"];
            $order->income_id = $dataArray[$i]["income_id"];
            $order->odid = $dataArray[$i]["odid"];
            $order->nm_id = $dataArray[$i]["nm_id"];
            $order->subject = $dataArray[$i]["subject"];
            $order->category = $dataArray[$i]["category"];
            $order->brand = $dataArray[$i]["brand"];
            $order->is_cancel = $dataArray[$i]["is_cancel"];
            $order->cancel_dt = $dataArray[$i]["cancel_dt"];
            $order->save();
        }
    }
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'orders' => Order::latest()->get(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Orders';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('orders', [
                TD::make('g_number'),
                TD::make('date')->sort(),
            ]),
            Layout::rows([
                DateTimer::make('order.date')
                    ->title('Order Date')
                    ->format('Y-m-d')
                    ->allowInput()
                    ->required()
                    ->serverFormat('Y-m-d'),
                Button::make('Pull and Save Orders')
                    ->method('pull'),
            ]),
        ];
    }
}
