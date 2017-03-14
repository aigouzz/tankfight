<?php
    $num = 28;
    $time = 2017;
    $times = 0;
    $link = '';
    while($num){
        if($num < 10){
            $link = $time.'00'.$num;
        }else if($num < 100 && $num >= 10){
            $link = $time.'0'.$num;
        }else{
            $link = $time.''.$num;
        }
        $num ++;
        if($num > 153){
            $num = 1;
            $time ++;
        }
        if($time == 2017 && $num > 29){
            break;
        }
        $url = 'http://baidu.lecai.com/lottery/draw/ajax_get_detail.php?lottery_type=50&phase='.$link;

        $useragent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";

        $header = array('Accept-Language: zh-cn','Connection: Keep-Alive','Cache-Control: no-cache');

        $ch = curl_init(); //初始化 curl

        curl_setopt($ch, CURLOPT_REFERER, $url);

        curl_setopt($ch,CURLOPT_HTTPHEADER,$header); //模拟浏览器的头信息

        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); //模拟浏览器的信息

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //是否保存采集内容

        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //curl允许执行的最大时间，单位是秒

        curl_setopt($ch, CURLOPT_URL, $url); //要采集的网址

        curl_setopt($ch, CURLOPT_HEADER, 1); //是否要保存头信息

        $con = curl_exec($ch); //采集到的内容将存储到$con里面

        $match1 = array();
        $match2 = array();
        $match3 = array();
        $match4 = array();
        $match5 = array();
        preg_match_all("/(\"\d{2}\",){5}\"\d{2}\"/",$con,$match1);
        preg_match_all("/\[\"\d{2}\"\]/",$con,$match2);
        preg_match_all("/\d{2}/",$match1[0][0],$match3);
        preg_match_all("/\d{2}/",$match2[0][0],$match4);
        preg_match_all("/\"time_endsale\":\".{19}\"/",$con,$match5);
        $timestring = substr($match5[0][0],15);
        if($timestring == '"0000-00-00 00:00:00"'){
            $timestring = $link;
        }

        $dbhost = 'localhost';  //mysql服务器主机地址
        $dbuser = 'root';      //mysql用户名
        $dbpass = 'root';//mysql用户名密码
        $dbname = 'calculate';//mysql
        $conn = new mysqli($dbhost, $dbuser, $dbpass,$dbname);
        if($conn->connect_errno)
        {
            echo('Could not connect: ' . $conn ->connect_error);
        }
        if(!preg_match("/\"data\":\[\]/",$con)){
            $conn->query("INSERT INTO caipiao (blue1, blue2, blue3, blue4 , blue5, blue6, red,time) VALUES (".intval($match3[0][0]).",".intval($match3[0][1]).",".intval($match3[0][2]).",".intval($match3[0][3]).",".intval($match3[0][4]).",".intval($match3[0][5]).",".intval($match4[0][0]).",".$timestring.")");
        }
        $conn->close();

        echo(!preg_match("/\"data\":\[\]/",$con));
        var_dump($timestring);
    }
?>