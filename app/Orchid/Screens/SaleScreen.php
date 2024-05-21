<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

use Carbon\Carbon;
class SaleScreen extends Screen
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
            'sale.date' => 'required|date_format:Y-m-d',
        ]);

        //var_dump($request->input('sale.date'));
        //$dateRange = Carbon::createFromFormat('Y-m-d', $request->input('sale.date'));
        //var_dump($dateRange);
        $dateRange = $request->input('sale.date');
        //var_dump($dateRange);
        $pageNo = 1;
        $url = "http://89.108.115.241:6969/api/sales?dateFrom={$dateRange}&dateTo={$dateRange}&page={$pageNo}&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie";
        //var_dump($url);
        $dataArray = [];
        $data = [];
        $response = Http::get($url);

        $response = $response->json();
        //var_dump($response);
        //var_dump($response["meta"]);
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
            $sale = new Sale();
            $sale->g_number = $dataArray[$i]["g_number"];
            $sale->date = $dataArray[$i]["date"];
            $sale->last_change_date = $dataArray[$i]["last_change_date"];
            $sale->supplier_article = $dataArray[$i]["supplier_article"];
            $sale->tech_size = $dataArray[$i]["tech_size"];
            $sale->barcode = $dataArray[$i]["barcode"];
            $sale->total_price = $dataArray[$i]["total_price"];
            $sale->discount_percent = $dataArray[$i]["discount_percent"];
            $sale->is_supply = $dataArray[$i]["is_supply"];
            $sale->is_realization = $dataArray[$i]["is_realization"];
            $sale->promo_code_discount = $dataArray[$i]["promo_code_discount"];
            $sale->warehouse_name = $dataArray[$i]["warehouse_name"];
            $sale->country_name = $dataArray[$i]["country_name"];
            $sale->oblast_okrug_name = $dataArray[$i]["oblast_okrug_name"];
            $sale->region_name = $dataArray[$i]["region_name"];
            $sale->income_id = $dataArray[$i]["income_id"];
            $sale->sale_id = $dataArray[$i]["sale_id"];
            $sale->odid = $dataArray[$i]["odid"];
            $sale->spp = $dataArray[$i]["spp"];
            $sale->for_pay = $dataArray[$i]["for_pay"];
            $sale->finished_price = $dataArray[$i]["finished_price"];
            $sale->price_with_disc = $dataArray[$i]["price_with_disc"];
            $sale->nm_id = $dataArray[$i]["nm_id"];
            $sale->subject = $dataArray[$i]["subject"];
            $sale->category = $dataArray[$i]["category"];
            $sale->brand = $dataArray[$i]["brand"];
            $sale->is_storno = $dataArray[$i]["is_storno"];
            $sale->save();
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
            'sales' => Sale::latest()->get(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Sales';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [            
            Layout::table('sales', [
                TD::make('g_number'),
                TD::make('date')->sort(),
            ]),
            Layout::rows([
                DateTimer::make('sale.date')
                    ->title('Sale Date')
                    ->format('Y-m-d')
                    ->allowInput()
                    ->required()
                    ->serverFormat('Y-m-d'),
                Button::make('Pull and Save Sales')
                    ->method('pull'),
            ]),
        ];
    }
}
