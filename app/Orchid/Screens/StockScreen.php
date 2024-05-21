<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

class StockScreen extends Screen
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
            'stock.date' => 'required|date_format:Y-m-d',
        ]);

        $dateRange = $request->input('stock.date');
        $pageNo = 1;
        $url = "http://89.108.115.241:6969/api/stocks?dateFrom={$dateRange}&dateTo={$dateRange}&page={$pageNo}&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie";

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
            $stock = new Stock();
            $stock->date = $dataArray[$i]["date"];
            $stock->last_change_date = $dataArray[$i]["last_change_date"];
            $stock->supplier_article = $dataArray[$i]["supplier_article"];
            $stock->tech_size = $dataArray[$i]["tech_size"];
            $stock->barcode = $dataArray[$i]["barcode"];
            $stock->quantity = $dataArray[$i]["quantity"];
            $stock->is_supply = $dataArray[$i]["is_supply"];
            $stock->is_realization = $dataArray[$i]["is_realization"];
            $stock->quantity_full = $dataArray[$i]["quantity_full"];
            $stock->warehouse_name = $dataArray[$i]["warehouse_name"];
            $stock->in_way_to_client = $dataArray[$i]["in_way_to_client"];
            $stock->in_way_from_client = $dataArray[$i]["in_way_from_client"];
            $stock->nm_id = $dataArray[$i]["nm_id"];
            $stock->subject = $dataArray[$i]["subject"];
            $stock->category = $dataArray[$i]["category"];
            $stock->brand = $dataArray[$i]["brand"];
            $stock->sc_code = $dataArray[$i]["sc_code"];
            $stock->price = $dataArray[$i]["price"];
            $stock->discount = $dataArray[$i]["discount"];
            $stock->save();
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
            'stocks' => Stock::latest()->get(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Stocks';
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
            Layout::table('stocks', [
                TD::make('supplier_article'),
                TD::make('date')->sort(),
            ]),
            Layout::rows([
                DateTimer::make('stock.date')
                    ->title('Stock Date')
                    ->format('Y-m-d')
                    ->allowInput()
                    ->required()
                    ->serverFormat('Y-m-d'),
                Button::make('Pull and Save Stocks')
                    ->method('pull'),
            ]),
        ];
    }
}
