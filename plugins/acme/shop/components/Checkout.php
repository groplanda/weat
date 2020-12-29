<?php namespace Acme\Shop\Components;

use Cms\Classes\ComponentBase;
use Input;
use Mail;
use Validator;
use ValidationException;
use Flash;
use Acme\Shop\Models\Order;
use Acme\Shop\Models\Product;
use Backend\Models\User;
use YooKassa\Client;

use CdekSDK\CdekClient;
use CdekSDK\Requests;

class Checkout extends ComponentBase {

  public function componentDetails()
  {
    return [
      'name' => 'Оформление заказа',
      'description' => 'Виджет оформления заказа'
    ];
  }

  // получение email админа
  public function getUserMail()
  {
    return User::where('is_superuser', 1)->value('email');
  }

  public function onRun()
  {
    $this->prepareVars();
  }

  public function onCheckout()
  {
    $rules = [
      'user_name'  => 'required|min:3',
      'user_phone' => 'required|min:5',
      'user_email'  => 'email',
      'user_comment' => 'max:200'
    ];

    $messages = [
      'required' => 'Поле обязательно к заполнению!',
      'email'    => 'E-mail имеет неверный формат!',
      'min'      => 'Минимум :min символов!',
      'max'      => 'Максимум :min символов!',
    ];

    $validator = Validator::make(Input::all(), $rules, $messages);

    if ($validator->fails()) { //если не прошло валидацию
      throw new ValidationException($validator);
    } else {
      //переменные
      $allProducts = json_decode($this->convertString(Input::get('products')));
      date_default_timezone_set('Europe/Moscow');
      $time = date('m/d/Y H:i:s', time());
      $vars = [
        'user_name' => Input::get('user_name'),
        'user_phone' => Input::get('user_phone'),
        'user_email' => Input::get('user_email'),
        'user_address' => Input::get('user_address'),
        'user_comment' => Input::get('user_comment'),
        'user_payment_method' => Input::get('user_payment_method'),
        'user_delivery_method' => Input::get('user_delivery_method'),
        'products' => $allProducts,
        'order'=> 'Заказ - '.$time,
        'order_id' => '',
        'ip' => $_SERVER['REMOTE_ADDR'],
      ];
      $items = $this->createProductArray($vars['products']);
      $sum = $this->getSumProducts($vars['products']);

      if($vars['user_delivery_method'] !== 'самовывоз' && (int)$vars['user_delivery_method'] > 0) {
        $delivery_sum = (int)$vars['user_delivery_method'];
        $sum += $delivery_sum;
        array_push($items, $this->createDelivery($delivery_sum));
      }

      Mail::send('acme.shop::mail.message', $vars, function($message) {
        $message->to($this->getUserMail(), 'Admin Person');
        $message->subject('Новый заказ с сайта');
      });

      if(isset($vars['user_email']) && !empty($vars['user_email'])) {
        Mail::send('acme.shop::mail.order', $vars, function($message) use ($vars) {
          $message->to(trim(Input::get('user_email')), 'Admin Person');
          $message->subject('Заказ - '.date('m/d/Y H:i:s', time()));
        });
      };

      if(Input::get('user_payment_method') === 'online') {
        $client = new Client();
        $client->setAuth(env('SHOP_ID'), env('KASSA_KEY'));
        $payment = $client->createPayment(
          [
            'amount' => [
              'value' => $sum,
              'currency' => 'RUB',
            ],
            'confirmation' => [
              'type' => 'redirect',
              'return_url' => 'vseonei.ru/checkout/result',
            ],
            'receipt' => [
              'customer' => [
                'full_name' => Input::get('user_name'),
                'phone' => Input::get('user_phone'),
              ],
              'items' => $items,
            ],
          ],
          uniqid('', true)
        );
        //вставка в базу данных
        $this->insertData($vars, $payment['_id']);
        return \Redirect::to($payment['_confirmation']['confirmation_url']);
      } else {
        $this->insertData($vars, null);
        Flash::success('Вы успешно заказали!');
      }
    }
  }

  public function onFilterTowns() {
    $this->prepareVars();
  }

  public function prepareVars()
  {
    $code = post('code', null);

    $client = new CdekClient();

    $regRequest = new Requests\RegionsRequest();
    $regRequest->setCountryCode('RU');

    $regResponse = $client->sendRegionsRequest($regRequest);

    $regions = [];
    $towns = [];

    foreach($regResponse as $region) {
      array_push($regions, [
        'title' => $region->getName(),
        'id' => $region->getCode(),
        'code' => $region->getCodeExt(),
      ]);
    }

    if($code !== null) {
      $townsRequest = new Requests\CitiesRequest();
      $townsRequest->setPage(0)->setRegionCode($code);
      $townsResponse = $client->sendCitiesRequest($townsRequest);
      foreach($townsResponse as $town) {
        array_push($towns, [
          'title' => $town->getCityName(),
          'id' => $town->getCityCode(),
          'limit' => $town->getPaymentLimit(),
          'kladr' => $town->getKladr(),
        ]);
      }
    }

    $this->page['towns'] = $towns;
    $this->page['regions'] = $regions;
  }

  private function createDelivery($sum) {
    return [
      'description' => 'Доставка CDEK',
      'quantity' => '1',
      'vat_code' => '2',
      'amount' => [
        'value' => $sum.'.00',
        'currency' => 'RUB'
      ],
      'payment_mode' => 'full_prepayment',
      'payment_subject' => 'commodity',
    ];
  }

  private function createProductArray($products)
  {
    $result = [];
    foreach($products as $product) {
      $result[] = [
        'description' => $product->title,
        'quantity' => $product->count,
        'vat_code' => '2',
        'amount' => [
          'value' => $product->price.'.00',
          'currency' => 'RUB'
        ],
        'payment_mode' => 'full_prepayment',
        'payment_subject' => 'commodity',
      ];
    };
    return $result;
  }

  private function getSumProducts($products)
  {
    $sum = 0;
    foreach($products as $product) {
      $sum+= $product->price * $product->count;
    }
    return $sum.'.00';
  }

  private function convertString($string)
  {
    return trim(str_replace('/storage/app/media', '', stripcslashes($string)), '"');
  }

  private function insertData($data, $u_id)
  {
    $order = new Order;
    $order->name = $data['order'];
    $order->ip = $data['ip'];
    $order->status = 'new';
    $order->user_name = $data['user_name'];
    $order->user_phone = $data['user_phone'];
    $order->user_email = $data['user_email'];
    $order->user_address = $data['user_address'];
    $order->user_comment = $data['user_comment'];
    $order->user_payment_method = $data['user_payment_method'];
    $order->user_delivery_method = $data['user_delivery_method'];
    $order->products = $data['products'];
    if(isset($u_id) && !empty($u_id)) {
      $order->order_id = $u_id;
    }
    $query = $order->save();
    return $query;
  }
}