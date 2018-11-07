<?php
/**
 * Desc: face++ 请求基类
 * Author: 余伟<weiwei2012holy@hotmail.com>
 * Date: 2018/11/6,下午5:11
 */

namespace weiwei2012holy;


abstract class FaceBase
{
    /**
     * @var
     */
    protected $apiKey;
    /**
     * @var
     */
    protected $apiSecret;

    /**
     * 图片来源格式,图片,文件,base64编码
     * @var array
     */
    protected $imageType = ['url', 'file', 'base64'];

    /**
     * 错误定义
     * @var array
     */
    protected $errors = [
        'AUTHENTICATION_ERROR' => 'api_key 和 api_secret 不匹配',
        'AUTHORIZATION_ERROR' => '没有调用本 API 的权限',
        'CONCURRENCY_LIMIT_EXCEEDED' => '并发数超过限制',
        'MISSING_ARGUMENTS' => '缺少参数',
        'BAD_ARGUMENTS' => '参数解析错误',
        'COEXISTENCE_ARGUMENTS' => '同时传入了要求是二选一或多选一的参数',
        'Request Entity Too Large' => '客户发送的请求大小超过了 2MB 限制',
        'API_NOT_FOUND' => '所调用的 API 不存在',
        'INTERNAL_ERROR' => '服务器内部错误',
        'IMAGE_FILE_TOO_LARGE' => '图片不能超过2MB',
        'INVALID_IMAGE_URL' => '图片无效或链接错误',
        'INVALID_IMAGE_SIZE' => '图片像素或尺寸有误',
        'IMAGE_ERROR_UNSUPPORTED_FORMAT' => '分析的文件不像图片诶',
        'IMAGE_DOWNLOAD_TIMEOUT' => '下载图片超时',
        'INSUFFICIENT_PERMISSION' => '试用API没这个功能',
        'BAD_FACE' => '上传的图片人脸不符合要求',
        'NO_FACE_FOUND' => '未检测到人脸',
        'INVALID_RECTANGLE' => '传入的人脸框格式不符合要求，或者人脸框位于图片外'
    ];

    /**
     * FaceBase constructor.
     * @param $apiKey
     * @param $apiSecret
     */
    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * @param $url
     * @param $params
     * @param $method
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request($url, $params, $method)
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request($method, $url, $params);
        if ($res->getStatusCode() != 200) {
            $logMsg = [
                'desc' => 'face++ detect api 调用失败',
                'url' => $url,
                'error_detail' => json_decode($res->getBody()->getContents(), 1)
            ];
            $msg = '人脸分析错误(' . $logMsg['error_detail']['error_message'] . ')';
            foreach ($this->errors as $k => $v) {
                if (stripos($logMsg['error_detail']['error_message'], $k) !== false) {
                    $msg = '人脸分析错误(' . $v . ')';
                }
            }
            throw new FacePlusPlusException($msg, $res->getStatusCode());
        }
        $response = $res->getBody();
        return json_decode($response->getContents(), 1);
    }

    /**
     * 校验数据文件类型
     * @param $imageData
     * @return string
     * @throws FacePlusPlusException
     */
    protected function checkImageType($imageData)
    {
        if (file_exists($imageData)) {
            //检验是否为本地文件
            $type = 'file';
        } elseif ($this->checkUrl($imageData)) {
            $type = 'url';
        } elseif ($this->checkBase64($imageData)) {
            $type = 'base64';
        } else {
            throw new FacePlusPlusException('图片类型限定:' . implode(',', $this->imageType));
        }
        return $type;
    }

    /**
     * 校验是否为合法的网页链接
     * @param $string
     * @return bool
     */
    protected function checkUrl($string)
    {
        $parse = parse_url($string);
        if ($parse['host'] && $parse['path']) {
            return $string;
        } else {
            return false;
        }
    }

    /**
     * 校验base64
     * @param $string
     * @return bool
     */
    protected function checkBase64($string)
    {
        return base64_decode($string, true) ? true : false;
    }

}