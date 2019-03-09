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
    private $appid = 'wx62c2f1f5d67a793b';
    private $appsecret = 'b0298320f25897ebb7d25ce9916e9a19';

    // 图灵机器人
    private $apiKey = '73f8963d5881483496970d2cffaad1a6';
    private $userId = 'sunhongjun';

    //
    private $postObj;

    public function _initialize()
    {
        $postStr = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->postObj = $postObj;
//        file_put_contents('a.txt', json_encode($postObj, 1));
    }

    public function index()
    {
//        if ($_REQUEST['echostr']) {
//            // 校验签名
//            $this->checkSignature();
//        } else {
//            $this->diyMenu();
            $this->reply();
//        }
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
                $resultStr = $this->event();
                break;
            case 'text':
                $resultStr = $this->keyword();
                break;
            case 'voice':
                $resultStr = $this->voice();
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
            default:
                $resultStr = $this->responseText('welcome！');
                break;
        }

        return $resultStr;
    }

    // 关键词回复
    private function keyword()
    {
        $keyword = $this->postObj->Content;
        $postion = strpos($keyword, '图片');
        if ($postion) {
            $type = substr($keyword, $postion-6, 6);
            $file = $this->getRandomImage($type);
            $resultStr = $this->responseImage($file);
        } else {
            $resultStr = $this->tulingText($keyword);
            // $resultStr = $this->responseText('失败了兄得！');
        }
        return $resultStr;
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
		            "button":
		            [
		                {    
		                    "type":"click",
		                    "name":"今日歌曲",
		                    "key":"music"
		                },
		                {
		                    "name":"新闻",
		                    "sub_button":
		                    [
		                        {    
		                            "type":"view",
		                            "name":"搜索",
		                            "url":"http://www.soso.com/"
		                        },
		                        {
		                            "type":"view",
		                            "name":"百度一下",
		                            "url": "https://www.baidu.com"
		                        },
		                        {
		                            "type":"click",
		                            "name":"赞一下我们",
		                            "key":"praise"
		                        }
		                    ]
		                },
		                {    
		                    "type":"click",
		                    "name":"点我惊喜",
		                    "key":"surprise"
		                }
		            ]
		        }';
        $res = $this->post($api, $menuData);
        var_dump($res);
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
        $imageTpl = "<xml>
						  <ToUserName><![CDATA[%s]]></ToUserName>
						  <FromUserName><![CDATA[%s]]></FromUserName>
						  <CreateTime>%s</CreateTime>
						  <MsgType><![CDATA[image]]></MsgType>
						  <Image>
						    <MediaId><![CDATA[%s]]></MediaId>
						  </Image>
						</xml>";
        $resultStr = sprintf($imageTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $media_id);
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