# Yii2 AWS SNS
Yii2 AWS SNS
An Amazon SNSClient wrapper as Yii2 component.

## Installation
Run Composer to install latest aws sdk
```php
composer require aws/aws-sdk-php
```

Add component to `config/main.php`
```php
'components' => [
// ...
'sns' => array (
            'class' => 'app\components\AmazonSNS',
            'key' => 'your aws sns key',
            'secret' => 'your aws sns secret',
        ),
// ...        
],        
```
## Usage

## Create Platform Endpoint
```php
$customerData = array('push_token'=>'Token','cust_id'=>'CustomUserData');
yii::$app->sns->generateEndpoint($customerObject, 'platformApplicationArn');
```

## Subscription
```php
$topicArn = 'topicArn';
$platformApplicationArn = 'platformApplicationArn';

yii::$app->sns->generateSubscriptionArn($topicArn, $platformApplicationArn);
```

## publish
```php
$message = json_encode(
                       array("aps" => array(
                           "alert" => 'test ios push 123345',
                           "badge" => 0,
                           "sound" => "default"
                       ),
                           "userAction" => array(
                               "type" => "restaurant",
                               "id" => 'restaurant id',
                               "name" => 'restaurant name'
                           ),
                       )
            ); // test message
$message = json_encode(["default" => "test", "APNS" => $message]);
$topicArn = 'topicArn';

yii::$app->sns->publish($topicArn, $message,$subject='');
```
