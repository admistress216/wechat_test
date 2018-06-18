<?php
ini_set('display_errors','on');
error_reporting(E_ALL);

class Voice {
    const APP_ID = '5add9b1c';
    const APP_KEY_TTS = 'dc4d93f59ab4dbf97d4fffee270ba812';

    public function voiceTts($content){
        $param = [
            'engine_type' => 'intp65',
            'auf' => 'audio/L16;rate=16000',
            'aue' => 'lame',
            'voice_name' => 'x_xiaoyuan',
//            'voice_name' => 'xiaoyan',
            'speed' => '50'
        ];
        $cur_time = (string)time();
        $x_param = base64_encode(json_encode($param));
        $header_data = [
            'X-Appid:'.self::APP_ID,
            'X-CurTime:'.$cur_time,
            'X-Param:'.$x_param,
            'X-CheckSum:'.md5(self::APP_KEY_TTS.$cur_time.$x_param),
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
        foreach($contentArr as $content) {
            $url[] = $this->getUrl($content, $header_data, $dir);
        }
        if(count($url) === 1) {
            return 'http://'.$_SERVER['HTTP_HOST'].ltrim($dir, '.').substr($url[0],strrpos($url[0], '/'));
        }
        $source = $complex = '';
        $i = 0;
        foreach($url as $u) {
            $source .= '-i '.$u.' ';
            $complex .= "[$i:0] ";
            $i++;
        }
        $newtime = time();
        $command = "/usr/local/bin/ffmpeg ".$source."-filter_complex '".$complex."concat=n=".$i.":v=0:a=1 [a]' -map [a] ".realpath($dir)."/".$newtime.".mp3";

        exec($command);
        return 'http://'.$_SERVER['HTTP_HOST'].ltrim($dir, '.').$newtime.'.mp3';
    }

    private function getUrl($content, $header_data, $dir) {
        $addr = $dir.strval(time()).'.mp3';

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
            file_put_contents($addr, substr($result, $res_header_size));
            return realpath($addr);
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
$voice = new Voice();
$path = $voice->voiceTts($_POST['content']);
echo json_encode(['path' => $path]);
