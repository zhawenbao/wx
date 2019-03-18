<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/9
 * Time: 11:37
 */

namespace app\wx\controller;


use think\Controller;
use think\Request;

/**
 * wechat php test
 */


class WX extends Controller
{
    // 公众号
    private $token = 'weixin';
//    private $appid = 'wx62c2f1f5d67a793b';
//    private $appsecret = '487cfdd54e591d26abad02e831f33c37';
      private  $appid = "wx108dc789f2f27af1";
      private  $appsecret = "b356ecc9a226948fc6d2521dfa584fee";

    // 图灵机器人
    private $apiKey = '73f8963d5881483496970d2cffaad1a6';
    private $userId = 'sunhongjun';

    //高德地圖
    private  $gdKey = '	065c29d619c53ae6ca30b6b09af4af39';
    //
    private $postObj;

    public function _initialize()
    {
        $postStr = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->postObj = $postObj;
//        $this->deleteMenu(); //刪除菜單
//        file_put_contents('a.txt', json_encode($postObj, 1));
    }

    public function index()
    {
        if (isset($_REQUEST['echostr'])) {
            // 校验签名
            $this->checkSignature();
        } else {
            $this->reply();
            $this->diyMenu();  //自定義菜單
        }
    }

    // 验证签名
    private function checkSignature()
    {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $echostr = $_GET['echostr'];

        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($signature == $tmpStr) {
            echo $echostr;
            exit;
        }
    }

    private function reply()
    {
        switch ($this->postObj->MsgType) {
            case 'event':
//                file_put_contents('ip.txt',json_encode($_SERVER,1));
                $resultStr = $this->event();
                break;
            case 'text':
                $resultStr = $this->keyword();
                break;
            case 'voice':
                $resultStr = $this->voice();
                break;
            case 'image':
                $resultStr = '';
                break;
            default:
                # code...
                break;
        }
        echo $resultStr;
    }

    // 事件回复
    private function event()
    {
        switch ($this->postObj->Event) {
            case 'subscribe':
                $resultStr = $this->responseText('谢谢你的关注！');
                break;
            case 'CLICK' && $this->postObj->EventKey == 'php框架':
                $resultStr = $this->responseText($this->content());
                break;
            case 'LOCATION':
                $resultStr = $this->responseText('您当前坐标为' . $this->postObj->Latitude . ',' .$this->postObj->Latitude .',精准度为'. $this->postObj->Precision);
                break;
            default:
                $resultStr = $this->responseText('無法識別内容');
                break;
        }

        return $resultStr;
    }

    // 关键词回复
    private function keyword()
    {
        $keyword = $this->postObj->Content;
//        $postion = strpos($keyword, '图片');
//        if ($postion) {
//            $type = substr($keyword, $postion-6, 6);
//            $file = $this->getRandomImage($type);
//            $resultStr = $this->responseImage($file);
//        } else {
//            $resultStr = $this->tulingText($keyword);
//            // $resultStr = $this->responseText('失败了兄得！');
//        }
        if ($keyword == 1) {
            $content = [
                [
                    'title'    =>  'tp3.2',
                    'desc'  =>  'thinkphp3.2框架手册',
                    'image' =>  '',
                    'url'   =>  'http://document.thinkphp.cn/manual_3_2.html'
                ],
                [
                    'title'    =>  'tp5.0',
                    'desc'  =>  'thinkphp5.0框架手册',
                    'image' =>  '',
                    'url'   =>  'https://www.kancloud.cn/manual/thinkphp5/118003'
                ],
                [
                    'title'    =>  'tp5.1',
                    'desc'  =>  'thinkphp5.1框架手册',
                    'image' =>  '',
                    'url'   =>  'https://www.kancloud.cn/manual/thinkphp5_1/353946'
                ]
            ];
            $resultStr = $this->news($content);
        }elseif($keyword == 2){
            $content = [
                [
                    'title'    =>  'laravel5.1',
                    'desc'  =>  'laravel5.1框架手册',
                    'image' =>  '',
                    'url'   =>  'https://learnku.com/docs/laravel/5.1'
                ],
                [
                    'title'    =>  'laravel5.2',
                    'desc'  =>  'laravel5.2框架手册',
                    'image' =>  '',
                    'url'   =>  'https://learnku.com/docs/laravel/5.2'
                ],
                [
                    'title'    =>  'laravel5.3',
                    'desc'  =>  'laravel5.3框架手册',
                    'image' =>  '',
                    'url'   =>  'https://learnku.com/docs/laravel/5.3'
                ],
                [
                    'title'    =>  'laravel5.4',
                    'desc'  =>  'laravel5.4框架手册',
                    'image' =>  '',
                    'url'   =>  'https://learnku.com/docs/laravel/5.4'
                ],
                [
                    'title'    =>  'laravel5.5',
                    'desc'  =>  'laravel5.5框架手册',
                    'image' =>  '',
                    'url'   =>  'https://learnku.com/docs/laravel/5.5'
                ],
                [
                    'title'    =>  'laravel5.6',
                    'desc'  =>  'laravel5.6框架手册',
                    'image' =>  '',
                    'url'   =>  'https://learnku.com/docs/laravel/5.6'
                ]
            ];
            $resultStr = $this->news($content);
        }elseif($keyword == 3){
            $content = [
                [
                    'title'    =>  'yii',
                    'desc'  =>  'yii1.0框架手册',
                    'image' =>  '',
                    'url'   =>  'https://www.yiiframework.com/doc/guide/1.1/zh-cn/index'
                ],
                [
                    'title'    =>  'yii2.0',
                    'desc'  =>  'yii2.0框架手册',
                    'image' =>  '',
                    'url'   =>  'https://www.kancloud.cn/manual/yii2-guide/69671'
                ],
            ];
            $resultStr = $this->news($content);
        }
        return $resultStr;
    }

    //詳情介紹
    private function content()
    {
//        return "此功能暂未开放";
        return "请输入数字，查看手册：\n1.thinkphp框架手册\n2.laravel框架手册\n3.yii框架手册";
    }

    // 语音消息回复
    private function voice()
    {
        $content = $this->postObj->Recognition;
        $resultStr = $this->tulingText($content);
        return $resultStr;
    }

    // 自定义菜单
    private function diyMenu()
    {
        $appid = $this->appid;
        $access_token = $this->getAccessToken();
        $api = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $menuData = '
            {
                "button": [{
                        "name": "php教程",
                        "sub_button":[{
                            "type": "view",
                                "name": "php手册",
                                "url": "http://php.net"
                            },
                            {
                            "type": "view",
                                "name": "菜鸟教程",
                                "url": "http://www.runoob.com/"
                            },
                            {
                            "type": "view",
                                "name": "w3cschool",
                                "url": "https://www.w3cschool.cn"
                            },
                            {
                                "type": "click",
                                "name": "php框架",
                                "key": "php框架"
                            }
                        ]
                    },
                    {
                        "name": "博客",
                        "sub_button":[{
                                "type": "view",
                                "name": "cnds",
                                "url": "https://www.csdn.net/"
                            },
                            {
                                "type": "view",
                                "name": "博客园",
                                "url": "https://www.cnblogs.com/"
                            },
                            {
                                "type": "view",
                                "name": "码农网",
                                "url": "http://www.codeceo.com/"
                            }
                        ]
                    },
                    {
                        "name": "视频教学",
                        "sub_button": [{
                                "type": "view",
                                "name": "慕课网",
                                "url": "https://www.imooc.com/"
                            },
                            {
                                "type": "view",
                                "name": "腾讯视频",
                                "url": "https://ke.qq.com/"
                            },
                            {
                                "type": "view",
                                "name": "siki学院",
                                "url": "http://www.sikiedu.com/"
                            }
                        ]
                    }
                ]
            }';
//        file_put_contents('menu.text',$menuData );
        $res = $this->post($api, $menuData);
    }

    //刪除菜單
    private function deleteMenu()
    {
        $api = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $this->getAccessToken();
        $res = $this->post($api);
//        file_put_contents('delMenu.txt', json_decode($res, 1));
    }

    // 图灵机器人自动回复
    private function tulingText($keyword)
    {
        $api = 'http://openapi.tuling123.com/openapi/api/v2';
        $apiKey = $this->apiKey;
        $userId = $this->userId;
        $param = '{
					    "perception": {
					        "inputText": {
					            "text": "'.$keyword.'"
					        }
					    },
					    "userInfo": {
					        "apiKey": "'.$apiKey.'",
					        "userId": "'.$userId.'"
					    }
					}';
        $res = $this->post($api, $param);
        $content = json_decode($res, 1)['results'][0]['values']['text'];
        return $this->responseText($content);
    }

    // 回复文本
    private function responseText($content)
    {
        $textTpl = "<xml>
						  <ToUserName><![CDATA[%s]]></ToUserName>
						  <FromUserName><![CDATA[%s]]></FromUserName>
						  <CreateTime>%s</CreateTime>
						  <MsgType><![CDATA[text]]></MsgType>
						  <Content><![CDATA[%s]]></Content>
						</xml>";
        $resultStr = sprintf($textTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $content);
        return $resultStr;
    }

    // 回复图片
    private function responseImage($file)
    {
        file_put_contents('1.jpg', file_get_contents($file));
        $media_id = $this->getMediaId('1.jpg', 'image');
//        file_put_contents('1.jpg', file_get_contents($file));
        $imageTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        <Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                        </Image>
                    </xml>";
        $resultStr = sprintf($imageTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(),$media_id);
        return $resultStr;
    }

    //回复图文消息
    private function news($content)
    {
        $newsTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount></ArticleCount>
                        <Articles>";
                foreach($content as $value){
                    $newsTpl .="<item>
                          <Title><![{$value['title']}]></Title>
                          <Description><![{$value['desc']}]></Description>
                          <PicUrl><![{$value['image']}]></PicUrl>
                          <Url><![{$value['url']}]></Url>
                        </item>
                        ";
                }
            $newsTpl .= "</Articles>
                    </xml>";
        $resultStr = sprintf($newsTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(),count($content));
        return $resultStr;
    }
    //查詢地址
    private function location()
    {

    }
    // 回复地理信息
    private function responseLocation($file)
    {
        $media_id = $this->getMediaId($file, 'location');
        $imageTpl = "<xml>
                        <ToUserName><![CDATA[toUser]]></ToUserName>
                        <FromUserName><![CDATA[fromUser]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[location]]></MsgType>
                        <Location_X>%s</Location_X>
                        <Location_Y>%s</Location_Y>
                        <Scale>20</Scale>
                        <Label><![CDATA[s%]]></Label>
                        <MsgId>%s</MsgId>
                    </xml>";
        $resultStr = sprintf($imageTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $dimension,$longitude, $address, $media_id);
        return $resultStr;
    }


    // 获取access_token
    private function getAccessToken()
    {
        $api = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";
        $res = $this->post($api);
        $access_token = json_decode($res)->access_token;
        return $access_token;
    }

    // 获取media_id
    private function getMediaId($file, $type)
    {
        $access_token = $this->getAccessToken();
        $file = realpath($file);
        $fileInfo = array(
            'meida' => new \CURLFile($file)
        );
        $api = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$type}";
        $res = $this->post($api, $fileInfo);
        $media_id = json_decode($res)->media_id;
        return $media_id;
    }

    // 随机获取一张图片链接
    private function getRandomImage($type = '')
    {
        $api = 'http://cdn.apc.360.cn/index.php?c=WallPaper&a=getAllCategoriesV2&from=360chrome';
        $cate = $this->post($api);
        $cid = 6;	// 默认美女图片
        if ($type) {
            foreach (json_decode($cate, 1)['data'] as $v) {
                if (strstr($this->unicodeDecode($v['name']), $type)) {
                    $cid = $v['id'];break;
                }
            }
        }
        $start = rand(1, 1000);
        $api = 'http://wallpaper.apc.360.cn/index.php?c=WallPaper&a=getAppsByCategory&cid='.$cid.'&start='.$start.'&count=1&from=360chrome';
        $res = $this->post($api);
        $file = json_decode($res, 1)['data'][0]['url'];
        return $file;
    }

    // http请求
    public function post($url, $postData = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // post 请求
        if(!empty($postData)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        //curl注意事项，如果发送的请求是https，必须要禁止服务器端校检SSL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 设置http请求头信息
        $header = ['Accept-Charset: utf-8'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // 设置请求的结果以字符串的形式返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }

    function unicodeDecode($unicode_str){
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }


}

?>