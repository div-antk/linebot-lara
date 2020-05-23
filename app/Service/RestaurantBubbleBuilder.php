<?php

namespace App\Services;

use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;

class RestaurantBubbleBuilder implements ContainerBuilder
{
  // プロパティの宣言
  private $imageUrl;
  private $name;
  private $closestStation;
  private $minutesByFoot;
  private $category;
  private $budget;
  private $latitude;
  private $longitude;
  private $phoneNumber;
  private $restaurantUrl;

  public static function builder(): RestaurantBubbleBuilder
  {
    // 自クラスのインスタンスを作成し、メソッド呼び出し元に返す
    return new self();
  }
}

?>
