<?php
/**
 * @author: Jayanta Kundu
 */
namespace app\components;

use Aws\Sns\SnsClient;
use Aws\Sns\Exception\SnsException;
class AmazonSNS extends \yii\base\Component
{
    public $bucket;
    public $key;
    public $secret;

    private $_client;

    public function init()
    {
        parent::init();

        $this->_client = SnsClient::factory(array(
            'credentials' => array(
                'key'    => $this->key,
                'secret' => $this->secret,
            ),
            'region' => 'ap-southeast-1',
            'version' => '2010-03-31'
        ));

    }

    /* Generating an endpoint */
    public function generateEndpoint($data,$arn) {
        try {
            $response = $this->client->createPlatformEndpoint(array(
                // PlatformApplicationArn is required
                'PlatformApplicationArn' => $arn,
                // Token is required
                'Token' => $data['push_token'],
                'CustomUserData' => $data['cust_id']));
        }
        catch(SnsException $e) {

            $message = $e->getMessage();
            preg_match("/(arn:aws:sns[^ ]+)/", $message, $matches);

            if(isset($matches[0]) && !empty($matches[0]))
                return $matches[0];
            return null;
        }

        if(isset($response['EndpointArn'])) {
            return $response['EndpointArn'];
        }
        else
            return null;
    }


    /* Generating an Subscription */
    public function generateSubscriptionArn($topicArn,$endpoint) {
        try {
            $response = $this->client->subscribe(array(
                // TopicArn is required
                'TopicArn' => $topicArn,
                // Protocol is required
                'Protocol' => 'application',
                'Endpoint' => $endpoint,
            ));
        }
        catch(SnsException $e) {
            //
        }

        if(isset($response['SubscriptionArn'])) {
            return $response['SubscriptionArn'];
        }
        else
            return null;
    }

    /* Generating an publish
     * @link http://docs.amazonwebservices.com/sns/latest/api/API_Publish.html
     * @return string
     * @throws InvalidArgumentException
     */

    public function publish($topicArn, $message, $subject = '') {

        try {
            $response = $this->client->publish(array(
                // TopicArn is required
                'TopicArn' => $topicArn,
                //'TargetArn' => $targetArn,
                // Message is required
                'Message' => $message,
                //'Subject' => $subject,
                'MessageStructure' => 'json',
            ));
        }
        catch(SnsException $e) {
            return $e->getMessage();
        }
        return $response;
    }


}
