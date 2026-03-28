<?php
declare (strict_types = 1);

namespace app\api\suanfa;

use think\Response;

/*
1、固定解析已加密的url (type=1)
2、解析接口，仅支持1080画质
/api/hqrr?url=xxx
*/

class Rrmj
{
    // 加密配置
    private const AES_KEY = 'a8f5c3e0b1a242e4b2a1c8d8e7f9b3a5'; //要修改，加密地址key，和caiji.php一致
    private const AES_IV = '1jCo58iLyb9OgJl4'; //要修改，加密地址iv，和caiji.php一致
    private const SOURCE_PREFIX = 'HQMJ-'; //要修改，加密地址前缀，和caiji.php一致

    // 加密密钥
    private const KEY1 = '3b744389882a4067';
    private const IV = 'b1da7878016e4e2b';

    // 调试日志（存储所有调试信息）
    private $debugLog = [];
    // 是否开启调试（可快速开关）
    private $debugMode = true;
    private $lastUpstreamError = [];
    private $resolvedAuth = ['umid' => '', 'cookie' => ''];

    /**
     * 新增：添加调试日志
     * @param string $step 步骤名称
     * @param string $desc 描述
     * @param mixed $data 调试数据
     */
    private function addDebugLog(string $step, string $desc, $data = null): void
    {
        if (!$this->debugMode) return;
        $this->debugLog[] = [
            'step' => $step,
            'desc' => $desc,
            'data' => $data,
            'time' => microtime(true), // 精确到微秒，便于看耗时
            'type' => is_null($data) ? 'null' : gettype($data)
        ];
    }

    /**
     * 新增：网页输出调试日志（合并到最终JSON）
     * @param array $output 原始输出数据
     * @return array 带调试日志的输出
     */
    private function wrapDebugOutput(array $output): array
    {
        if (!$this->debugMode) {
            $output['debug_version'] = '11.php-debug-20260324-2020';
            return $output;
        }
        // 计算每个步骤的耗时
        $logWithCost = [];
        $firstTime = $this->debugLog[0]['time'] ?? microtime(true);
        foreach ($this->debugLog as $k => $log) {
            $preTime = $k > 0 ? $this->debugLog[$k-1]['time'] : $firstTime;
            $log['cost_ms'] = round(($log['time'] - $preTime) * 1000, 2); // 毫秒
            $log['total_cost_ms'] = round(($log['time'] - $firstTime) * 1000, 2); // 累计耗时
            $logWithCost[] = $log;
        }
        return array_merge($output, [
            'debug_version' => '11.php-debug-20260324-2020',
            'debug' => [
                'log' => $logWithCost,
                'key1_info' => [
                    'value' => self::KEY1,
                    'length' => strlen(self::KEY1),
                    'is_valid_aes128' => strlen(self::KEY1) === 16 // KEY1是否符合AES-128长度
                ],
                'aes_iv_info' => [
                    'value' => self::IV,
                    'length' => strlen(self::IV),
                    'is_valid_aes128_iv' => strlen(self::IV) === 16 // IV是否符合AES-128-CBC长度
                ]
            ]
        ]);
    }

    private function getAESKey(): string
    {
        $key = md5('HuaQiNiuBi');
        $this->addDebugLog('getAESKey', 'md5(HuaQiNiuBi)生成密钥', [
            'key' => $key,
            'length' => strlen($key)
        ]); // DEBUG
        return $key;
    }

    public function index(): Response
    {
        // 初始化调试
        $this->debugLog = [];
        $this->addDebugLog('index', '接口开始执行', [
            'request_get' => $_GET,
            'request_post' => $_POST,
            'timestamp' => date('Y-m-d H:i:s')
        ]); // DEBUG

        // 固定type=1，解析加密URL
        $input = $this->request->param('url', '', 'trim');
        $this->addDebugLog('index', '接收的url参数', [
            'raw' => $input,
            'trimmed' => trim($input),
            'is_empty' => empty(trim($input))
        ]); // DEBUG

        try {
            $this->parseRmj($input, microtime(true));
        } catch (\Exception $e) {
            $this->addDebugLog('index', '执行异常', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]); // DEBUG
            $output = [
                'code' => 500,
                'msg' => '解析失败: ' . $e->getMessage(),
                'url' => '',
                'type' => '',
                'time' => round(microtime(true) - microtime(true), 2) . 's'
            ];
            $this->jx_msg($output);
        }
    }

    /**
     * 提供给Index控制器调用的公共方法
     */
    public function parseRmj($url, $startTime, $umid = '', $cookie = ''): void
    {
        $this->addDebugLog('parseRmj', '开始解析RMJ链接', [
            '原始URL' => $url,
            '开始时间' => $startTime,
            '传入umid' => $umid,
            '传入cookie' => $cookie
        ]); // DEBUG

        try {
            // 固定使用type=1，解析加密URL
            //$decrypted_url = $this->decrypt_fxrr_url($url);
            $source_url = $url; // 解密后的地址
            $this->addDebugLog('parseRmj', '确定源URL', $source_url); // DEBUG

            // 解析URL获取dramaId和episodeNo
            preg_match('/drama\/(\d+)/', $source_url, $m);
            $dramaId = $m[1] ?? '';
            $this->addDebugLog('parseRmj', '解析dramaId', [
                'match_result' => $m,
                'dramaId' => $dramaId,
                'is_empty' => empty($dramaId)
            ]); // DEBUG

            $q = [];
            $query = parse_url($source_url, PHP_URL_QUERY);
            if (is_string($query) && $query !== '') parse_str($query, $q);
            $episodeNo = isset($q['episodeNo']) ? (int)$q['episodeNo'] : 1;
            $this->addDebugLog('parseRmj', '解析episodeNo', [
                'url_query' => $query,
                'parse_result' => $q,
                'episodeNo' => $episodeNo
            ]); // DEBUG

            if (empty($dramaId)) {
                $time_used = round(microtime(true) - $startTime, 2) . 's';
                $output = [
                    'code' => 400,
                    'url' => '',
                    'type' => '',
                    'source_url' => $source_url,
                    'time' => $time_used
                ];
                $this->jx_msg($output);
            }

            // 获取页面数据
            $this->addDebugLog('parseRmj', '开始调用get_page_data', ['dramaId' => $dramaId]); // DEBUG
            $pageData = $this->get_page_data($dramaId, $umid, $cookie);
            $this->addDebugLog('parseRmj', 'get_page_data返回结果', [
                'is_array' => is_array($pageData),
                'data' => $pageData,
                'error' => !is_array($pageData) ? '返回非数组' : ''
            ]); // DEBUG

            if (!is_array($pageData)) {
                $time_used = round(microtime(true) - $startTime, 2) . 's';
                $output = [
                    'code' => $this->lastUpstreamError['http_code'] ?? 500,
                    'msg' => (($this->lastUpstreamError['http_code'] ?? 0) === 429) ? '上游接口限流' : ($this->lastUpstreamError['msg'] ?? '获取剧集信息失败'),
                    'url' => '',
                    'type' => '',
                    'source_url' => $source_url,
                    'time' => $time_used,
                    'upstream' => $this->lastUpstreamError
                ];
                $this->jx_msg($output);
            }

            // 查找episodeSid
            $episodeSid = null;
            $episodeList = $pageData['data']['episodeList'] ?? [];
            $this->addDebugLog('parseRmj', '遍历episodeList找episodeSid', [
                'episodeList' => $episodeList,
                'target_episodeNo' => $episodeNo
            ]); // DEBUG

            foreach ($episodeList as $ep) {
                if ((int)($ep['episodeNo'] ?? 0) === $episodeNo) {
                    $episodeSid = $ep['sid'] ?? null;
                    break;
                }
            }
            $this->addDebugLog('parseRmj', '找到的episodeSid', [
                'episodeSid' => $episodeSid,
                'is_empty' => empty($episodeSid)
            ]); // DEBUG

            if (empty($episodeSid)) {
                $time_used = round(microtime(true) - $startTime, 2) . 's';
                $output = [
                    'code' => 404,
                    'url' => '',
                    'type' => '',
                    'source_url' => $source_url,
                    'time' => $time_used
                ];
                $this->jx_msg($output);
            }

            // 只支持1080画质，使用AI_OD
            $this->addDebugLog('parseRmj', '开始调用get_1080_url', [
                'dramaId' => $dramaId,
                'episodeSid' => $episodeSid
            ]); // DEBUG
            $playUmid = $this->resolvedAuth['umid'] ?: $umid;
            $playCookie = $this->resolvedAuth['cookie'] ?: $cookie;
            $final_url = $this->get_1080_url($dramaId, $episodeSid, $playUmid, $playCookie);
            $this->addDebugLog('parseRmj', 'get_1080_url返回最终URL', [
                'final_url' => $final_url,
                'is_empty' => empty($final_url)
            ]); // DEBUG

            if (!empty($final_url)) {
                $time_used = round(microtime(true) - $startTime, 2) . 's';
                $output = [
                    'code' => 200,
                    'url' => $final_url,
                    'type' => 'mp4',
                    'source_url' => $source_url,
                    'time' => $time_used
                ];
                $this->jx_msg($output);
            } else {
                $time_used = round(microtime(true) - $startTime, 2) . 's';
                $output = [
                    'code' => $this->lastUpstreamError['http_code'] ?? 500,
                    'msg' => (($this->lastUpstreamError['http_code'] ?? 0) === 429) ? '上游接口限流' : ($this->lastUpstreamError['msg'] ?? '获取播放地址失败'),
                    'url' => '',
                    'type' => '',
                    'source_url' => $source_url,
                    'time' => $time_used,
                    'upstream' => $this->lastUpstreamError
                ];
                $this->jx_msg($output);
            }
        } catch (\Exception $e) {
            $this->addDebugLog('parseRmj', '解析异常', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]); // DEBUG
            $output = [
                'code' => 500,
                'msg' => '解析失败: ' . $e->getMessage(),
                'url' => '',
                'type' => '',
                'time' => round(microtime(true) - $startTime, 2) . 's'
            ];
            $this->jx_msg($output);
        }
    }

    /**
     * 统一的JSON输出方法（兼容Index控制器的输出格式）- 新增调试日志合并
     */
    private function jx_msg($json): void
    {
        // 合并调试日志到输出
        $jsonWithDebug = $this->wrapDebugOutput($json);
        
        header('Content-Type: application/json; charset=utf-8');
        // JSON_PRETTY_PRINT 格式化输出，便于网页查看
        echo json_encode($jsonWithDebug, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * 从数据库获取rrmj的cookie数据
     */
    private function getRmjCookieData(): array
    {
        $this->addDebugLog('getRmjCookieData', '开始获取RMJ Cookie数据'); // DEBUG
        try {
            $cookieList = \think\facade\Db::name('cookie')->where('act', 'rrmj')->where('state', 1)->select();
            $this->addDebugLog('getRmjCookieData', '查询cookie表结果', [
                'cookieList_count' => count($cookieList),
                'cookieList' => $cookieList
            ]); // DEBUG

            if (empty($cookieList)) {
                $this->addDebugLog('getRmjCookieData', 'cookieList为空，返回空数据'); // DEBUG
                return ['umid' => '', 'cookie' => ''];
            }

            // 使用轮询逻辑获取cookie
            $cookieData = $this->dataPollingInterval($cookieList);
            $this->addDebugLog('getRmjCookieData', '轮询后获取的cookieData', $cookieData); // DEBUG

            $cookieJson = json_decode($cookieData[0]['cookie'], true);
            $this->addDebugLog('getRmjCookieData', '解析cookie JSON', [
                'raw_cookie' => $cookieData[0]['cookie'],
                'decode_result' => $cookieJson,
                'is_array' => is_array($cookieJson)
            ]); // DEBUG

            if (is_array($cookieJson)) {
                $result = [
                    'umid' => $cookieJson['umid'] ?? $cookieJson['deviceId'] ?? '',
                    'cookie' => $cookieJson['cookie'] ?? ''
                ];
                $this->addDebugLog('getRmjCookieData', '最终返回的cookie数据', $result); // DEBUG
                return $result;
            }
        } catch (\Exception $e) {
            $this->addDebugLog('getRmjCookieData', '获取Cookie异常', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]); // DEBUG
        }
        
        $this->addDebugLog('getRmjCookieData', '默认返回空数据'); // DEBUG
        return ['umid' => '', 'cookie' => ''];
    }

    private function getAlternateRmjCookieData(string $excludeUmid = '', string $excludeCookie = ''): array
    {
        try {
            $cookieList = \think\facade\Db::name('cookie')->where('act', 'rrmj')->where('state', 1)->select();
            if (empty($cookieList)) {
                return ['umid' => '', 'cookie' => ''];
            }

            $cookieList = $this->dataPollingInterval($cookieList);
            foreach ($cookieList as $item) {
                $cookieJson = json_decode($item['cookie'] ?? '', true);
                if (!is_array($cookieJson)) {
                    continue;
                }

                $umid = $cookieJson['umid'] ?? $cookieJson['deviceId'] ?? '';
                $cookie = $cookieJson['cookie'] ?? '';
                if ($umid === '' || $cookie === '') {
                    continue;
                }

                if ($umid === $excludeUmid && $cookie === $excludeCookie) {
                    continue;
                }

                return ['umid' => $umid, 'cookie' => $cookie];
            }
        } catch (\Exception $e) {
            $this->addDebugLog('getAlternateRmjCookieData', '获取备用Cookie异常', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }

        return ['umid' => '', 'cookie' => ''];
    }

    private function setLastUpstreamError(string $stage, int $httpCode = 0, string $msg = '', array $extra = []): void
    {
        $this->lastUpstreamError = array_merge([
            'stage' => $stage,
            'http_code' => $httpCode,
            'msg' => $msg,
        ], $extra);
    }

    /**
     * Cookie轮询逻辑（参考Index控制器的实现）
     */
    private function dataPollingInterval($list): array
    {
        $this->addDebugLog('dataPollingInterval', '开始Cookie轮询', ['原始列表长度' => count($list)]); // DEBUG

        if (empty($list[0])) {
            $this->addDebugLog('dataPollingInterval', '列表为空，返回空cookie'); // DEBUG
            return [['cookie' => '']];
        }

        try {
            $polling_time = \think\facade\Db::name('set')->where('act', 'common')->where('a', 'cklxtime')->find()['b'] ?? '900 sec';
            $this->addDebugLog('dataPollingInterval', '查询轮询时间配置', ['polling_time' => $polling_time]); // DEBUG
        } catch (\Exception $e) {
            $polling_time = '900 sec';
            $this->addDebugLog('dataPollingInterval', '查询轮询时间异常，使用默认值', [
                'msg' => $e->getMessage(),
                'default_polling_time' => $polling_time
            ]); // DEBUG
        }

        if ($polling_time == 0) {
            $polling_time = '900 sec';
        }
        $interval = false;
        $arg = array(
            's' => 1,          // 秒
            'm' => 60,         // 分= 60 sec
            'h' => 3600,       // 时= 3600 sec
            'd' => 86400,      // 天= 86400 sec
        );
        foreach ($arg as $k => $v) {
            if (false !== stripos($polling_time, $k)) {
                $interval = intval($polling_time) * $v;
                break;
            }
        }
        if (!is_int($interval)) {
            $interval = 900;
        }
        $this->addDebugLog('dataPollingInterval', '计算轮询间隔', [
            'polling_time' => $polling_time,
            'interval_sec' => $interval
        ]); // DEBUG

        $this_year_begin_second = strtotime(date('Y-01-01 01:00:01', time()));
        $polling_time = time() - $this_year_begin_second;
        $len = count($list);
        $start_index = intval($polling_time / $interval);
        $start_index = 1 * $start_index % $len;
        $res = array();
        for ($i = 0; $i < $len; $i++) {
            $index = $i + $start_index;
            if ($index >= $len) {
                $index = $index - $len;
            }
            $res[] = $list[$index];
        }

        $this->addDebugLog('dataPollingInterval', '轮询结果', [
            'start_index' => $start_index,
            '轮询后列表' => $res
        ]); // DEBUG
        return $res;
    }

    private function decrypt_fxrr_url(string $input): string
    {
        $this->addDebugLog('decrypt_fxrr_url', '开始解密FXRR URL', ['原始输入' => $input]); // DEBUG
        $processed = str_replace([" ", "+", self::SOURCE_PREFIX], "", $input);
        $this->addDebugLog('decrypt_fxrr_url', '替换特殊字符后', $processed); // DEBUG

        $raw = hex2bin($processed);
        $this->addDebugLog('decrypt_fxrr_url', 'hex2bin转换结果', [
            'raw' => $raw,
            'is_false' => $raw === false
        ]); // DEBUG

        if ($raw === false) {
            throw new \InvalidArgumentException('HEX decode failed');
        }
        $plain = openssl_decrypt($raw, 'aes-128-cbc', $this->getAESKey(), OPENSSL_RAW_DATA, self::AES_IV);
        $this->addDebugLog('decrypt_fxrr_url', 'AES-128-CBC解密结果', [
            'plain' => $plain,
            'is_false' => $plain === false
        ]); // DEBUG

        if ($plain === false) {
            throw new \RuntimeException('AES-128-CBC decrypt failed');
        }
        return $plain;
    }

    private function generate_get_sign($method, $headers, $params, $sign_secret): array
    {
        $this->addDebugLog('generate_get_sign', '开始生成签名', [
            'method' => $method,
            'headers' => $headers,
            'params' => $params,
            'sign_secret' => $sign_secret
        ]); // DEBUG

        $timestamp = strval(intval(microtime(true) * 1000));
        $sign_string = sprintf(
            "%s\n" .
            "aliId:%s\n" .
            "ct:%s\n" .
            "cv:%s\n" .
            "t:%s\n" .
            "%s",
            strtoupper($method),
            $headers['aliId'],
            $headers['clientType'] ?? $headers['ct'],
            $headers['clientVersion'] ?? $headers['cv'],
            $timestamp,
            $params
        );
        $this->addDebugLog('generate_get_sign', '签名原始字符串', $sign_string); // DEBUG

        $sign = base64_encode(hash_hmac('sha256', $sign_string, $sign_secret, true));
        $result = [
            'x-ca-sign' => $sign,
            't' => $timestamp
        ];
        $this->addDebugLog('generate_get_sign', '生成的签名', $result); // DEBUG

        return $result;
    }

    private function aes_ecb_decrypt($ciphertext, $key1): string
    {
        $this->addDebugLog('aes_ecb_decrypt', '开始AES-ECB解密', [
            'ciphertext_type' => gettype($ciphertext),
            'ciphertext_length' => strlen((string)$ciphertext),
            'key1' => $key1,
            'key1_length' => strlen($key1)
        ]); // DEBUG

        // 强制转换为字符串并清理
        $ciphertextStr = trim((string)$ciphertext);
        $this->addDebugLog('aes_ecb_decrypt', '清理后的密文', [
            'length' => strlen($ciphertextStr),
            'first_20_chars' => substr($ciphertextStr, 0, 20), // 只显示前20字符，避免过长
            'last_20_chars' => substr($ciphertextStr, -20)
        ]); // DEBUG

        $ciphertext_bytes = base64_decode($ciphertextStr);
        $this->addDebugLog('aes_ecb_decrypt', 'Base64解码结果', [
            'is_false' => $ciphertext_bytes === false,
            'bytes_length' => $ciphertext_bytes !== false ? strlen($ciphertext_bytes) : 0
        ]); // DEBUG

        if ($ciphertext_bytes === false) {
            $this->addDebugLog('aes_ecb_decrypt', 'Base64解码失败', '密文格式错误'); // DEBUG
            throw new \RuntimeException('Base64 decode failed in aes_ecb_decrypt');
        }

        $key_bytes = $key1;
        $decrypted_bytes = openssl_decrypt($ciphertext_bytes, 'AES-128-ECB', $key_bytes, OPENSSL_RAW_DATA);
        $this->addDebugLog('aes_ecb_decrypt', 'AES-128-ECB解密结果', [
            'is_false' => $decrypted_bytes === false,
            'decrypted_length' => $decrypted_bytes !== false ? strlen($decrypted_bytes) : 0
        ]); // DEBUG

        if ($decrypted_bytes === false) {
            $this->addDebugLog('aes_ecb_decrypt', 'AES解密失败', '密钥或密文错误'); // DEBUG
            throw new \RuntimeException('AES-128-ECB decrypt failed');
        }

        $result = rtrim((string)$decrypted_bytes, "\0");
        $this->addDebugLog('aes_ecb_decrypt', '最终解密结果（去空字符）', [
            'length' => strlen($result),
            'first_50_chars' => substr($result, 0, 50)
        ]); // DEBUG

        return $result;
    }

    private function aes_decrypt_cbc($ciphertext, $key, $iv): string
    {
        $this->addDebugLog('aes_decrypt_cbc', '开始AES-CBC解密', [
            'ciphertext_type' => gettype($ciphertext),
            'key' => $key,
            'key_length' => strlen($key),
            'iv' => $iv,
            'iv_length' => strlen($iv)
        ]); // DEBUG

        $ciphertext_bytes = base64_decode($ciphertext);
        $this->addDebugLog('aes_decrypt_cbc', 'Base64解码结果', [
            'is_false' => $ciphertext_bytes === false,
            'bytes_length' => $ciphertext_bytes !== false ? strlen($ciphertext_bytes) : 0
        ]); // DEBUG

        if ($ciphertext_bytes === false) {
            throw new \RuntimeException('Base64 decode failed in aes_decrypt_cbc');
        }

        $key_bytes = $key;
        $iv_bytes = $iv;
        $decrypted_bytes = openssl_decrypt($ciphertext_bytes, 'AES-128-CBC', $key_bytes, OPENSSL_RAW_DATA, $iv_bytes);
        $this->addDebugLog('aes_decrypt_cbc', 'AES-128-CBC解密结果', [
            'is_false' => $decrypted_bytes === false,
            'decrypted_length' => $decrypted_bytes !== false ? strlen($decrypted_bytes) : 0
        ]); // DEBUG

        if ($decrypted_bytes === false) {
            throw new \RuntimeException('AES-128-CBC decrypt failed');
        }

        $result = rtrim((string)$decrypted_bytes, "\0");
        $this->addDebugLog('aes_decrypt_cbc', '最终解密结果', [
            'length' => strlen($result),
            'result' => $result
        ]); // DEBUG

        return $result;
    }

    private function get_request_data_by_ids(string $dramaId, string $episodeSid, string $qualityCode = 'AI_OD', $umid = '', $token = ''): array
    {
        $this->addDebugLog('get_request_data_by_ids', '开始构建请求数据', [
            'dramaId' => $dramaId,
            'episodeSid' => $episodeSid,
            'qualityCode' => $qualityCode,
            'umid' => $umid,
            'token' => $token
        ]); // DEBUG

        // 如果没有传递参数，则从数据库获取
        if (empty($umid) || empty($token)) {
            $cookieData = $this->getRmjCookieData();
            $umid = $cookieData['umid'];
            $token = $cookieData['cookie'];
            $this->addDebugLog('get_request_data_by_ids', '从数据库获取cookie补充参数', [
                'umid' => $umid,
                'token' => $token
            ]); // DEBUG
        }
        
        $method = 'GET';
        $param_s = '/m-station/drama/play?dramaId='.$dramaId.'&episodeSid='.$episodeSid.'&hevcOpen=0&quality='.$qualityCode;
        $url     = 'https://api.rrmj.plus'.$param_s;
        $this->addDebugLog('get_request_data_by_ids', '构建请求URL', [
            'param_s' => $param_s,
            'full_url' => $url
        ]); // DEBUG

        $baseHeaders = [
            'aliId' => $umid,
            'clientType' => 'web_applet',
            'clientVersion' => '1.0.0',
            'ct' => 'web_applet',
            'cv' => '1.0.0',
        ];
        $sign_secret = 'ES513W0B1CsdUrR13Qk5EgDAKPeeKZY';
        $headers_with_sign = $this->generate_get_sign($method, $baseHeaders, $param_s, $sign_secret);

        $headers = array_merge($baseHeaders, $headers_with_sign, [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:131.0) Gecko/20100101 Firefox/131.0',
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2',
            'deviceId' => $umid,
            'umid' => $umid,
            'uet' => '9',
            'Access-Control-Request-Method' => 'GET',
            'token' => $token,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-cache',
            'Referer' => 'https://m.rrmj.plus/'
        ]);
        $this->addDebugLog('get_request_data_by_ids', '最终请求头', $headers); // DEBUG

        return [$url, $headers];
    }

    private function get_page_data($dramaId, $umid = '', $token = '', bool $retried = false): ?array
    {
        $this->lastUpstreamError = [];
        $this->addDebugLog('get_page_data', '开始获取页面数据', [
            'dramaId' => $dramaId,
            '传入umid' => $umid,
            '传入token' => $token
        ]); // DEBUG

        // 如果没有传递参数，则从数据库获取
        if (empty($umid) || empty($token)) {
            $cookieData = $this->getRmjCookieData();
            $umid = $cookieData['umid'];
            $token = $cookieData['cookie'];
            $this->addDebugLog('get_page_data', '从数据库补充cookie', [
                'umid' => $umid,
                'token' => $token
            ]); // DEBUG
        }
        
        $method = 'GET';
        $pagePath = '/m-station/drama/page?dramaId=' . $dramaId . '&hsdrOpen=0&isAgeLimit=0&quality=AI4K';
        $pageUrl  = 'https://api.rrmj.plus' . $pagePath;
        $this->addDebugLog('get_page_data', '构建页面请求URL', [
            'pagePath' => $pagePath,
            'pageUrl' => $pageUrl
        ]); // DEBUG
        
        $baseHeaders = [
            'aliId' => $umid,
            'clientType' => 'web_applet',
            'clientVersion' => '1.0.0',
            'ct' => 'web_applet',
            'cv' => '1.0.0',
        ];

        $sign = $this->generate_get_sign($method, $baseHeaders, $pagePath, 'ES513W0B1CsdUrR13Qk5EgDAKPeeKZY');
        $reqHeaders = array_merge($baseHeaders, $sign, [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:131.0) Gecko/20100101 Firefox/131.0',
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2',
            'deviceId' => $umid,
            'umid' => $umid,
            'uet' => '9',
            'token' => $token,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-cache',
            'Referer' => 'https://m.rrmj.plus/',
        ]);
        $this->addDebugLog('get_page_data', '页面请求头', $reqHeaders); // DEBUG

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pageUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function($k,$v){return "$k: $v";}, array_keys($reqHeaders), $reqHeaders));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 新增：捕获curl错误
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 超时时间

        $pageResp = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->addDebugLog('get_page_data', 'CURL请求结果', [
            'http_code' => $httpCode,
            'curl_errno' => $curlErrno,
            'curl_error' => $curlError,
            'pageResp_type' => gettype($pageResp),
            'pageResp_length' => strlen((string)$pageResp),
            'pageResp_first_50' => substr((string)$pageResp, 0, 50),
            'pageResp_last_50' => substr((string)$pageResp, -50)
        ]); // DEBUG

        // 调试用：保留原var_dump，但注释掉（如需临时看原始值可取消注释）
        // var_dump($pageResp);

        if ($httpCode === 429) {
            $this->setLastUpstreamError('get_page_data', 429, '上游接口限流', [
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError,
                'body_preview' => substr((string)$pageResp, 0, 200),
            ]);
            $this->addDebugLog('get_page_data', '上游429限流', $this->lastUpstreamError); // DEBUG
            if (!$retried) {
                $alternate = $this->getAlternateRmjCookieData($umid, $token);
                $this->addDebugLog('get_page_data', '尝试切换备用Cookie', $alternate); // DEBUG
                if (!empty($alternate['umid']) && !empty($alternate['cookie'])) {
                    return $this->get_page_data($dramaId, $alternate['umid'], $alternate['cookie'], true);
                }
            }
            $this->setLastUpstreamError('get_page_data', (int)$httpCode, '页面数据解密失败', [
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError,
            ]);
            return null;
        }

        if ($pageResp === false || $httpCode >= 400) {
            $this->setLastUpstreamError('get_page_data', (int)$httpCode, '上游接口请求失败', [
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError,
                'body_preview' => substr((string)$pageResp, 0, 200),
            ]);
            return null;
        }

        try {
            $pageJson = $this->aes_ecb_decrypt((string)$pageResp, self::KEY1);
            $this->addDebugLog('get_page_data', 'AES解密后的pageJson', [
                'length' => strlen($pageJson),
                'first_100' => substr($pageJson, 0, 100)
            ]); // DEBUG
        } catch (\Exception $e) {
            $this->addDebugLog('get_page_data', 'AES解密异常', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]); // DEBUG
            return null;
        }

        $pageData = json_decode((string)$pageJson, true);
        $this->addDebugLog('get_page_data', 'JSON解析结果', [
            'json_last_error' => json_last_error_msg(),
            'is_array' => is_array($pageData),
            'data' => $pageData
        ]); // DEBUG

        if (is_array($pageData)) {
            $this->resolvedAuth = ['umid' => $umid, 'cookie' => $token];
            return $pageData;
        }

        $this->setLastUpstreamError('get_page_data', (int)$httpCode, '页面数据JSON解析失败', [
            'json_error' => json_last_error_msg(),
        ]);
        return null;
    }

    private function get_1080_url($dramaId, $episodeSid, $umid = '', $token = ''): string
    {
        $this->addDebugLog('get_1080_url', '开始获取1080P URL', [
            'dramaId' => $dramaId,
            'episodeSid' => $episodeSid,
            'umid' => $umid,
            'token' => $token
        ]); // DEBUG

        // 1080p画质，使用AI_OD
        list($url, $headers) = $this->get_request_data_by_ids($dramaId, $episodeSid, 'AI_OD', $umid, $token);
        $this->addDebugLog('get_1080_url', '请求播放地址的URL和头', [
            'url' => $url,
            'headers' => $headers
        ]); // DEBUG
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function($key, $value) {
            return "$key: $value";
        }, array_keys($headers), $headers));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);

        // 捕获curl错误
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->addDebugLog('get_1080_url', '播放地址CURL请求结果', [
            'http_code' => $httpCode,
            'curl_errno' => $curlErrno,
            'curl_error' => $curlError,
            'response_type' => gettype($response),
            'response_length' => strlen((string)$response)
        ]); // DEBUG
        
        try {
            $decrypted_text = $this->aes_ecb_decrypt($response, self::KEY1);
            $this->addDebugLog('get_1080_url', '解密播放地址响应', [
                'decrypted_text_length' => strlen($decrypted_text),
                'first_100' => substr($decrypted_text, 0, 100)
            ]); // DEBUG
        } catch (\Exception $e) {
            $this->addDebugLog('get_1080_url', '解密播放地址异常', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]); // DEBUG
            return '';
        }

        $urlData = json_decode($decrypted_text, true);
        $this->addDebugLog('get_1080_url', '解析播放地址JSON', [
            'json_error' => json_last_error_msg(),
            'urlData' => $urlData
        ]); // DEBUG
        
        $en_url = $urlData['data']['m3u8']['url'] ?? '';
        $key = substr($urlData['data']['newSign'] ?? '', 4, 16);
        $this->addDebugLog('get_1080_url', '提取加密URL和密钥', [
            'en_url' => $en_url,
            'newSign' => $urlData['data']['newSign'] ?? '',
            'key' => $key,
            'key_length' => strlen($key)
        ]); // DEBUG
        
        try {
            $decrypted_text = $this->aes_decrypt_cbc($en_url, $key, self::IV);
            $this->addDebugLog('get_1080_url', '解密最终播放URL', [
                'decrypted_text' => $decrypted_text,
                'is_empty' => empty($decrypted_text)
            ]); // DEBUG

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $decrypted_text);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_exec($ch);
            $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $curlErrno = curl_errno($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            $this->addDebugLog('get_1080_url', '获取最终跳转URL', [
                'decrypted_text' => $decrypted_text,
                'final_url' => $final_url,
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError
            ]); // DEBUG

            return $final_url ?: '';
        } catch (\Exception $e) {
            $this->addDebugLog('get_1080_url', '获取最终URL异常', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]); // DEBUG
            return '';
        }
    }
}
