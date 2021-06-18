<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use App\User;
use LINE\LINEBot\RichMenuBuilder;
use LINE\LINEBot\Event\PostbackEvent;

class LineController extends Controller
{
    private $bot;
    private $channel_access_token;
    private $channel_secret;

    protected $client;

    public function __construct()
    {
        $this->channel_access_token = env('CHANNEL_ACCESS_TOKEN');
        $this->channel_secret       = env('CHANNEL_SECRET');

        $httpClient   = new CurlHTTPClient($this->channel_access_token);
        $this->bot    = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = new \GuzzleHttp\Client();

        //actions
        $this->locationTemplateActionBuilder = new \LINE\LINEBot\TemplateActionBuilder\LocationTemplateActionBuilder("Location");
        $this->buttonUriTemplateActionBuilder = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Button url action', "https://www.google.com/search?q=buttonurl");

        $this->confirmYesUriTemplateActionBuilder = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Yes', "https://www.google.com/search?q=confirm+yes");
        $this->confirmNoUriTemplateActionBuilder = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('No', "https://www.google.com/search?q=confirm+no");
        $this->imageCarouselUriTemplateActionBuilder1 = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('Yes', "https://www.google.com/search?q=imageconfirm+yes");
        $this->imageCarouselUriTemplateActionBuilder2 = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('No', "https://www.google.com/search?q=imageconfirm+no");
        $this->messageTemplateActionBuilder = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("Confirm action", "Confirm");
        $this->postbackTemplateActionBuilder1 = new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("Postback to Carousel", "callback/1");
        $this->postbackTemplateActionBuilder2 = new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("Postback to ImageMap", "callback/2");
        $this->postbackTemplateActionBuilder3 = new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("Postback to Button", "callback/3");
    }

    public function webhook(Request $request)
    {

        $bot       = $this->bot;
        $signature = $request->header(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
        $body      = $request->getContent();
        // $postback = $request->events[0]['postback'];
        // Log::info([$events]);


        try {
            $events = $bot->parseEventRequest($body, $signature);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        // Log::info([$events[0]['postback']]);

        foreach ($events as $event) {
            if ($event->getType() == 'message' && $event->getMessageType() == 'text') {
                $this->replyMessage($event);
            }
            if ($event instanceof PostbackEvent) {
                Log::info("PostbackEvent");
                $data = $event->getPostbackData();

                if ($data == "callback/1") {
                    $response = $this->replyCarousel($event);
                    // try {
                    //     $response = $this->replyCarousel($event);
                    // } catch (\Exception $e) {
                    //     Log::error($e->getMessage());
                    // }
                    if ($response->getHTTPStatus() != 200) {
                        Log::info([$response]);
                    }
                }
                if ($data == "callback/2") {
                    $response = $this->replyImageMap($event);
                    // Log::info([$response]);
                }
            }
        }

        // $postback = $request->events[0]['postback'];
        // if ($postback["data"] == "callback/1") {
        //     Log::info(true);
        // }
    }
    //actions  


    public function replyMessage($event)
    {
        $message = $event->getText();
        $replyToken = $event->getReplyToken();


        //messageBuilder
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello ' . $message);
        $videoMessageBuilder = new \LINE\LINEBot\MessageBuilder\VideoMessageBuilder("https://youtu.be/s7wmiS2mSXY", "https://youtu.be/s7wmiS2mSXY");

        $messageTaiwan = preg_match('/台灣|臺灣|Taiwan|taiwan|linda/', $message);
        if ($messageTaiwan) {
            $response = $this->bot->replyMessage($replyToken, $videoMessageBuilder);
        }

        $messageMovie = preg_match('/電影|video/', $message);
        if ($messageMovie) {
            $response = $this->bot->replyMessage($replyToken, $videoMessageBuilder);
        }

        $replyConfirm = preg_match('/confirm|Confirm/', $message);
        if ($replyConfirm) {
            $this->replyConfirm($event);
        }

        $replyButton = preg_match('/button|Button/', $message);
        if ($replyButton) {
            $this->replyButton($event);
        }

        $replyCarousel = preg_match('/imagecarousel|image Carousel|Imagecarousel/', $message);
        if ($replyCarousel) {
            $this->replyImageCarousel($event);
        }

        $replyCarousel = preg_match('/carousel|Carousel/', $message);
        if ($replyCarousel) {
            $this->replyCarousel($event);
        }

        $replyImageMap = preg_match('/image map|imagemap|ImageMap|Imagemap|Image Map|Image map/', $message);
        if ($replyImageMap) {
            $this->replyImageMap($event);
        }


        $response = $this->bot->replyText($replyToken, "我只會回覆你說的話：" . $message);

        if ($response->isSucceeded()) {
            logger('reply successfully');
            return;
        }
    }

    public function replyImage($event)
    {

        $replyToken = $event->getReplyToken();
        $imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder('https://picsum.photos/400/300?grayscale', 'https://yt3.ggpht.com/ytc/AAUvwnj9oeVek52mTA9Z84qp9-x5SYK41SlY4eegGScouw=s900-c-k-c0x00ffffff-no-rj');
        // Log::info([$imageMessageBuilder]);
        $response = $this->bot->replyMessage($replyToken, $imageMessageBuilder);
        // DD($response);
        Log::info([$response]);
    }

    //templateMessageBuilder
    public function replyConfirm($event)
    {
        $replyToken = $event->getReplyToken();

        $confirmTemplateBuilder = new  \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder('Example confirm template', [$this->confirmYesUriTemplateActionBuilder, $this->confirmNoUriTemplateActionBuilder]);

        $templateMessageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('Example button template', $confirmTemplateBuilder);
        $response = $this->bot->replyMessage($replyToken, $templateMessageBuilder);
        // DD($response);
        Log::error([$response]);
    }

    public function replyButton($event)
    {
        $replyToken = $event->getReplyToken();

        $buttonTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder('This is Title', 'this is button', "https://picsum.photos/400/300?grayscale", [$this->messageTemplateActionBuilder, $this->buttonUriTemplateActionBuilder, $this->postbackTemplateActionBuilder1, $this->postbackTemplateActionBuilder2], null, "cover", null, null);
        $templateMessageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('Example button template', $buttonTemplateBuilder);

        $response = $this->bot->replyMessage($replyToken, $templateMessageBuilder);
        // Log::info([$response]);
    }

    public function replyCarousel($event)
    {
        $replyToken = $event->getReplyToken();
        // Log::info([$replyToken]);
        // DD();
        $carouselColumnTemplateBuilder1 = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("This is Carousel", "this is first carousel", "https://cdnb.artstation.com/p/assets/images/images/008/364/543/large/arthit-assaranurak-caro-art-0001.jpg?1512300289", [$this->messageTemplateActionBuilder]);
        $carouselColumnTemplateBuilder2 = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder("This is Carousel", "this is second carousel", "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJHfhTeGfHU6K2SI2mwjJLn_-of5429FIpuA&usqp=CAU", [$this->locationTemplateActionBuilder]);

        $carouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder([$carouselColumnTemplateBuilder1, $carouselColumnTemplateBuilder2], 'rectangle', "cover");
        $templateMessageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('Example button template', $carouselTemplateBuilder);

        $response = $this->bot->replyMessage($replyToken, $templateMessageBuilder);
        return $response;
    }

    public function replyImageCarousel($event)
    {
        $replyToken = $event->getReplyToken();
        $ImageCarouselColumnTemplateBuilder1 = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder("https://cdnb.artstation.com/p/assets/images/images/008/364/543/large/arthit-assaranurak-caro-art-0001.jpg?1512300289", $this->imageCarouselUriTemplateActionBuilder1);
        $ImageCarouselColumnTemplateBuilder2 = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder("https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJHfhTeGfHU6K2SI2mwjJLn_-of5429FIpuA&usqp=CAU", $this->imageCarouselUriTemplateActionBuilder2);

        $imageCarouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder([$ImageCarouselColumnTemplateBuilder1, $ImageCarouselColumnTemplateBuilder2]);
        $templateMessageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('Example', $imageCarouselTemplateBuilder);

        $response = $this->bot->replyMessage($replyToken, $templateMessageBuilder);
        // DD($response);
        Log::error([$response]);
    }

    public function replyImageMap($event)
    {
        $replyToken = $event->getReplyToken();

        $areaBuilder = new \LINE\LINEBot\ImagemapActionBuilder\AreaBuilder(520, 0, 520, 1040);
        $imagemapUriActionBuilder = new \LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder("https://www.google.com/", $areaBuilder);
        $imagemapMessageActionBuilder = new \LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder("Button", $areaBuilder);

        $baseSizeBuilder = new \LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder(1040, 1040);
        $imagemapMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder("https://cdn.icon-icons.com/icons2/426/PNG/512/Map_1135px_1195280_42272.png", "this is imagemap", $baseSizeBuilder, [$imagemapMessageActionBuilder]);

        $response = $this->bot->replyMessage($replyToken, $imagemapMessageBuilder);
        Log::error([$response]);
    }






    public function sendMessage(User $user)
    {

        $bot = $this->bot;
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('驗證成功');
        $response = $bot->pushMessage($user->user_id, $textMessageBuilder);

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
    }

    public function verifiedFailed(User $user)
    {
        $bot = $this->bot;
        $user->update([
            "isVerified" => 0,
        ]);
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('驗證失敗，請重新填寫表單:' . "https://liff.line.me/1656014340-n0dVloyD");
        $response = $bot->pushMessage($user->user_id, $textMessageBuilder);

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
        return redirect('/admin');
    }



    public function linkRichMenuToUser(User $user)
    {
        $richMenuId = "richmenu-c91fc3ade7b2d286b94f588edf5686fa";

        $this->bot->linkRichMenu($user->user_id, $richMenuId);

        return response('finish');
    }

    public function notify()
    {
        DD('123');
        $headers = array(
            'Content-Type: multipart/form-data',
            'Authorization: Bearer YghFPP5PWUx6bDn22pXHWJOtd4pXSliPiCdut5KvQgb'
        );
        $message = array(
            'message' => 'Hello,Brian~'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
