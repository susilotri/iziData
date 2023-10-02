<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuoteController extends Controller
{
    //
    public function getQuote()
    {
        $sources = [
            'Chuck Norris Jokes' => 'https://api.chucknorris.io/jokes/random',
            'Dog Facts' => 'https://dog-facts-api.herokuapp.com/api/v1/resources/dogs?number=1',
            'Cat Facts' => 'https://catfact.ninja/fact',
        ];

        // Random source
        $selectedSource = array_rand($sources);
        $sourceUrl = $sources[$selectedSource];

        // Get Quote from soure url
        $response = Http::get($sourceUrl);
        $data = $response->json();

        return response()->json([
            'quote' => $data['value'] ?? '',
            'status' => 'success',
            'source' => $selectedSource,
        ], 200);
    }
}
