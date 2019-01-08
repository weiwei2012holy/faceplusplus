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
    protected $imageType = ['url', 'file', 'base64', 'face_token'];

    /**
     * @var array 可选的face set 字段名称
     */
    protected $faceSetFieldName = ['outer_id', 'faceset_token'];

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
        'INVALID_RECTANGLE' => '传入的人脸框格式不符合要求，或者人脸框位于图片外',
        'FACESET_EXIST' => 'FaceSet 已经存在',
        'INVALID_FACE_TOKENS_SIZE' => 'face_tokens 数组长度不符合要求',
        'FACESET_QUOTA_EXCEEDED' => 'FaceSet 数量达到上限，不能继续创建 FaceSet',
        'NEW_OUTER_ID_EXIST' => '提供的new_outer_id与已有outer_id重复',
        'INVALID_FACESET_TOKEN' => '无效的faceset_token',
        'INVALID_OUTER_ID' => '无效的outer_id',
        'VOID_REQUEST' => '传入 return_landmark=0 且 return_attributes=none 导致不进行任何人脸分析操作'
    ];

    /**
     * FaceBase constructor.
     *
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
     *
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
                'return_data' => json_decode($res->getBody()->getContents(), 1)
            ];
            $msg = 'Face++调用错误(' . $logMsg['return_data']['error_message'] . ')';
            foreach ($this->errors as $k => $v) {
                if (stripos($logMsg['return_data']['error_message'], $k) !== false) {
                    $msg = $v . '(' . $logMsg['return_data']['error_message'] . ')';
                }
            }
            throw new FacePlusPlusException($msg, $res->getStatusCode(), null, $logMsg);
        }
        return json_decode($res->getBody()->getContents(), 1);
    }

    /**
     * 校验数据文件类型
     *
     * @param $imageData
     *
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
        } elseif (mb_strlen($imageData) == 32) {
            $type = 'face_token';
        } elseif ($this->checkBase64($imageData)) {
            $type = 'base64';
        } else {
            throw new FacePlusPlusException('图片类型限定:' . implode(',', $this->imageType));
        }
        return $type;
    }

    /**
     * 校验face set 字段名称是否合法
     *
     * @param $field
     *
     * @return bool
     * @throws FacePlusPlusException
     */
    protected function checkFaceSetFieldName($field)
    {
        if (!in_array($field, $this->faceSetFieldName)) {
            throw new FacePlusPlusException('face set 字段名称限定:' . implode(',', $this->faceSetFieldName));
        }
        return true;
    }

    /**
     * 获取faceset 字段名称
     *
     * @param $useOuterId
     *
     * @return string
     */
    protected function getFaceSetFieldName($useOuterId)
    {
        return $useOuterId ? 'outer_id' : 'faceset_token';
    }

    /**
     * 构造图片请求字段(图片字段格式 image_url image_url1 merge_url)
     *
     * @param        $imageData
     * @param string $fieldName
     * @param null   $suffix
     *
     * @return array
     * @throws FacePlusPlusException
     */
    protected function buildPostMultipart($imageData, $fieldName = 'image', $suffix = null)
    {
        $type = $this->checkImageType($imageData);
        if ($type == 'file') {
            $multipart = ['name' => $type, 'contents' => fopen($imageData, 'r')];
        } elseif (in_array($type, ['url', 'base64'])) {
            $multipart = ['name' => $type, 'contents' => $imageData];
        } elseif ($type == 'face_token') {
            $multipart = ['name' => 'face_token', 'contents' => $imageData];
        } else {
            throw new FacePlusPlusException('图片类型限定:' . implode(',', $this->imageType));
        }
        //有些接口要传参数1 参数2这种,做了个后缀区别
        if ($suffix) {
            if ($type == 'base64') {
                $multipart['name'] .= '_' . $suffix;
            } else {
                $multipart['name'] .= $suffix;
            }
        }
        //构造自定义字段名称
        if ($fieldName) {
            if ($type == 'face_token') {
                //todo  名称不用动
            } else {
                $multipart['name'] = $fieldName . '_' . $multipart['name'];
            }
        }
        return $multipart;
    }

    /**
     * 校验是否为合法的网页链接
     *
     * @param $string
     *
     * @return bool
     */
    protected function checkUrl($string)
    {
        $parse = parse_url($string);
        if (isset($parse['host']) && isset($parse['path'])) {
            return $string;
        } else {
            return false;
        }
    }

    /**
     * 校验base64
     *
     * @param $string
     *
     * @return bool
     */
    protected function checkBase64($string)
    {
        return base64_decode($string, true) ? true : false;
    }

}