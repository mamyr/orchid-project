<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

class IncomeScreen extends Screen
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
            'income.date' => 'required|date_format:Y-m-d',
        ]);

        $dateRange = $request->input('income.date');
        $pageNo = 1;
        $url = "http://89.108.115.241:6969/api/incomes?dateFrom={$dateRange}&dateTo={$dateRange}&page={$pageNo}&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie";

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
            $income = new Income();
            $income->income_id = $dataArray[$i]["income_id"];
            $income->number = $dataArray[$i]["number"];
            $income->date = $dataArray[$i]["date"];
            $income->last_change_date = $dataArray[$i]["last_change_date"];
            $income->supplier_article = $dataArray[$i]["supplier_article"];
            $income->tech_size = $dataArray[$i]["tech_size"];
            $income->barcode = $dataArray[$i]["barcode"];
            $income->quantity = $dataArray[$i]["quantity"];
            $income->total_price = $dataArray[$i]["total_price"];
            $income->date_close = $dataArray[$i]["date_close"];
            $income->warehouse_name = $dataArray[$i]["warehouse_name"];
            $income->nm_id = $dataArray[$i]["nm_id"];
            $income->status = $dataArray[$i]["status"];
            $income->save();
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
            'incomes' => Income::latest()->get(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Incomes';
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
            Layout::table('incomes', [
                TD::make('supplier_article'),
                TD::make('date')->sort(),
            ]),
            Layout::rows([
                DateTimer::make('income.date')
                    ->title('Income Date')
                    ->format('Y-m-d')
                    ->allowInput()
                    ->required()
                    ->serverFormat('Y-m-d'),
                Button::make('Pull and Save Incomes')
                    ->method('pull'),
            ]),
        ];
    }
}
