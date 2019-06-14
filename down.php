<?php
/**
 * 启用方式:命令行# php down.php base64_encode(json_encode([课程列表])) 标题 资源名称 开始数
 * 例:php php down.php WyLorqnns7vnu5/mm7Tnvo7lpb3nmoTpmJ/liJciLCLkvb/nlKjmlbDmja7lupPpq
bHliqjlrp7njrDpmJ/liJfns7vnu58iLCLlpoLkvZXlpITnkIbpmJ/liJfkuK3miafooYzlpLHotKXnmoQgSm9icyIsIuWmguS9lemHjeaWsOaJp+ihjOmYn
+WIl+S4reeahOWksei0peeahCBKb2Jz77yM5aaC5L2V5Yig6ZmkIEpvYiIsIuS9v+eUqCBSZWRpcyDkvZzkuLrpqbHliqjlrp7njrDpmJ/liJflip/og70iL
CJSZWRpcyDpqbHliqjpmJ/liJfnmoTnm5HmjqfpnaLmnb8gSG9yaXpvbiIsInBocCBhcnRpc2FuIHF1ZXVlOndvcmsg54mI5pys5beu5byCIl0= Laravel_
队列 video-laravel-queue-system 1
 */
require('config.php');
foreach($data as $argv){
	//$argv = array_values($argv);
	//$file_names = json_decode(base64_decode($argv[0], true)); //课程列表
	$file_names = $argv['file_names']; //课程列表
	$folder_name = $argv['folder_name']; //文件夹名称(课程名称)
	$resources = $argv['resources']; //资源名称
	$start_num = $argv['start_num'] ?? 1; //开始数

	$down_domain = 'http://media.coding10.com'; //资源域名
	$ext = '.mp4'; //后缀名
	$count = count($file_names); //总数目
	$dir = __DIR__; //根目录
	$save_path = $dir . '/download/' . $folder_name . '/'; //保存目录
	$min_exec_num = 100; //最小执行次数(最小文件取名范围)
	$max_exec_num = 999; //最大执行次数(最大文件取名范围)
	$total_exec_num = (($count - $start_num) + 1) * $max_exec_num; //总执行次数
	$current_exec_num = 0; //当前执行次数
	$current_progress = 0; //当前进度
	$previous_progress = 0; //上次进度
	$resources_data = [
		'title' => $folder_name,
		'data' 	=> []
	]; //资源信息
	echo '程序加载中...' . PHP_EOL;
	for($i = $start_num; $i <= $count; $i++){
		for($j = 100; $j <= 999; $j++){
			$current_exec_num += 1;
			$current_progress = round(($current_exec_num / $total_exec_num) * 100); //计算当前进度
			if($previous_progress != $current_progress){
				//echo $current_progress . '%' . '   ';
			}
			$resources_name = $resources . '-' . $i . '-' . $j . $ext; //资源名称
			$file_name = $file_names[$i - 1]; //文件名称
			$file_link = $down_domain . '/' . $resources_name; //文件资源地址
			
			$previous_progress = $current_progress;
			$file = curl($file_link, 'GET'); //下载文件
			$file_size = getFileSize($file); //获取文件大小
			if($file_size > 1){
				echo '正在下载:'. $file_name . $ext . PHP_EOL;
				$file_name_num = $i < 10 ? '0' . $i : $i;
				saveFile($file, $save_path, $file_name_num . '-' . $file_name . $ext); //保存文件
				$resources_data['data'][$i] = [
					'resources_name' => $file_name,
					'file_url' => $file_link
				];
				echo $file_name . $ext . ' 下载完成' . PHP_EOL;
				$total_exec_num = ($total_exec_num - ($max_exec_num - $j)); //总执行次数扣掉跳过次数
				$$current_exec_num = ($current_exec_num - ($max_exec_num - $j)); //当前执行次数扣掉跳过次数
				continue;
			}
		}
	}
}
//保存资源信息
if(!empty($resources_data['data'])){
	saveFile(json_encode($resources_data, JSON_UNESCAPED_UNICODE), $save_path, $folder_name . '.txt');
}
exit('下载完成,程序结束');

/**
 * 获取文件大小
 * @param string $file 文件流
 * @return int 文件大小，单位(MB)
 */
function getFileSize($file = ''){
	$file_len = strlen($file);
	$file_size = number_format((($file_len / 1024) / 1024), 2);
	return $file_size;
}

/**
 * 保存文件
 * @param string $file 文件流
 * @param string $save_path 保存位置
 * @param string $filename 文件名
 * @return bool
 */
function saveFile($file = '', $save_path = './', $filename = ''){
	//创建保存目录
	if (!file_exists($save_path) && !mkdir($save_path, 0777, true)) {
	  return false;
	}
	if (trim($filename) == '') {
	  $filename = time();
	}
	// 保存文件到制定路径
	file_put_contents($save_path . $filename, $file);
	unset($file);
	return true;
}

/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $post_data post数据数组
 * @param bool json 是否发送json数据 false:否 true:是
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
function curl($url, $method = "GET", $post_data = null, $json = false, $headers = array(), $debug = false) {
	$method = strtoupper($method); //转大写
	$ci = curl_init();
	/* Curl settings */
	curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
	curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
	curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
	switch ($method) {
		case "POST":
			curl_setopt($ci, CURLOPT_POST, true);
			if (!empty($post_data)) {
				$tmpdatastr = is_array($post_data) ? http_build_query($post_data) : $post_data;
				curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
			}
			break;
		case 'GET':
			curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
			break;
		default:
			curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
			break;
	}
	$ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
	curl_setopt($ci, CURLOPT_URL, $url);
	if($ssl){
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
		curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
	}
	//curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
	curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
	curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ci, CURLINFO_HEADER_OUT, true);
	if($json){ //发送JSON数据
		curl_setopt($ci, CURLOPT_HEADER, 0);
		curl_setopt($ci, CURLOPT_HTTPHEADER,
			array(
				'Content-Type: application/json; charset=utf-8',
				'Content-Length:' . strlen($post_data))
		);
	}
	/*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
	$response = curl_exec($ci);
	$requestinfo = curl_getinfo($ci);
	$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
	if ($debug) {
		echo "=====post data======\r\n";
		var_dump($post_data);
		echo "=====info===== \r\n";
		print_r($requestinfo);
		echo "=====response=====\r\n";
		print_r($response);
	}
	curl_close($ci);
	return $response;
	//return array($http_code, $response,$requestinfo);
}