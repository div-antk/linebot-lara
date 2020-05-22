<?php 

namespace App\Services;

use GuzzleHttp\Client;
 
  class Gurunavi
  {
    private const RESTAURANTS_SEARCH_API_URL = 'https://api.gnavi.co.jp/RestSearchAPI/v3/';

    // 引数 $word は文字列の型であると宣言
    // また、戻り値が配列であると宣言
    public function searchRestaurants(string $word): array
    {
      $client = new Client();
      $response = $client
        ->get(self::RESTAURANTS_SEARCH_API_URL, [
          'query' => [
            'keyid' => env('GURUNAVI_ACCESS_KEY'),
            'freeword' => str_replace(' ', ',', $word),
          ],
          'http_errors' => false,
        ]);

      // HTTPレスポンスボディで返ってくる
      return json_decode($response->getBody()->getContents(), true);
    }
  }

?>
