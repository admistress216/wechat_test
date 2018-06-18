<?php
ini_set('display_errors','on');
error_reporting(E_ALL);

class Voice {
    private static $appid = '';
    private static $appkey = '';

    public function __construct($appid, $appkey)
    {
        self::$appid = $appid;
        self::$appkey = $appkey;
    }

    public function voiceTts($content, $voiceName){
        $param = [
            'engine_type' => 'intp65',
            'auf' => 'audio/L16;rate=16000',
            'aue' => 'lame',
            'voice_name' => $voiceName,
//            'voice_name' => 'xiaoyan',
            'speed' => '50'
        ];
        $cur_time = (string)time();
        $x_param = base64_encode(json_encode($param));
        $header_data = [
            'X-Appid:'.self::$appid,
            'X-CurTime:'.$cur_time,
            'X-Param:'.$x_param,
            'X-CheckSum:'.md5(self::$appkey.$cur_time.$x_param),
            'Content-Type:application/x-www-form-urlencoded; charset=utf-8'
        ];    //Body

        $contentArr = $this->strSplitUnicode($content, 300);
        if (empty($contentArr)) {
            return 'error';
        }
        $dir = './voice/'.date('Y/m/d').'/';
        if (!is_dir($dir)) {
            mkdir($dir,0777,true);
        }

        $url = [];
        $i = 1;
        foreach($contentArr as $content) {
            $url[] = $this->getUrl($content, $header_data, $i);
            $i++;
        }
        $len = count($url);
        if($len === 1) {
            exec('mv '.$url[0].' '.realpath($dir));
            return 'http://'.$_SERVER['HTTP_HOST'].ltrim($dir, '.').$url[0];
        }
        $str = $newStr = '';
        foreach($url as $u) {
            $str .= "file '".$u."'\r\n";
            $newStr .= $u.' ';
        }
        $file = './list_'.uniqid().'.txt';
        file_put_contents($file, $str);
        $newtime = strval(time()+5);
        $command = "/usr/local/bin/ffmpeg -f concat -i ".$file."  -c copy ".realpath($dir)."/".$newtime.".mp3";
        $newStr .= $file;

        exec($command);
        exec("rm -rf ".$newStr);
        return 'http://'.$_SERVER['HTTP_HOST'].ltrim($dir, '.').$newtime.'.mp3';
    }

    private function getUrl($content, $header_data, $i) {
        $addr = strval(time() + $i).'.mp3';

        $body_data = 'text='.urlencode($content);    //Request
        $url = "http://api.xfyun.cn/v1/service/v1/tts";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body_data);
        $result = curl_exec($ch);
        $res_header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $res_header = substr($result, 0, $res_header_size);
        curl_close($ch);

        if(stripos($res_header, 'Content-Type: audio/mpeg') === FALSE){ //合成错误
            return substr($result, $res_header_size);
        }else{
            file_put_contents('./'.$addr, substr($result, $res_header_size));
            return $addr;
        }
    }

    /**
     * 字符切割
     *
     * @param $str
     * @param int $l
     * @return array
     */
    private function strSplitUnicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
$voice = new Voice('5add9b1c', 'dc4d93f59ab4dbf97d4fffee270ba812');
$path = $voice->voiceTts($_POST['content'], 'x_xiaoyuan');
echo json_encode(['path' => $path]);
