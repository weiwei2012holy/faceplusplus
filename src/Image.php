<?php
/**
 * Desc: 图像识别
 * Author: 余伟<weiwei2012holy@hotmail.com>
 * Date: 2018/11/6,下午5:25
 */

namespace weiwei2012holy;


class Image extends FaceBase
{
    /**
     * @var string 人脸融合api
     */
    private $mergeFaceUrl = 'https://api-cn.faceplusplus.com/imagepp/v1/mergeface';


    /**
     * 人脸融合
     * @param        $templateUrl
     * @param        $templateRectangle
     * @param        $mergeUrl
     * @param int    $mergeRate
     * @param string $merge_rectangle
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mergeFace($templateUrl, $templateRectangle, $mergeUrl, $merge_rectangle = '', $mergeRate = 50)
    {
        $params = [
            'http_errors' => false,
            'form_params' => [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'template_url' => $templateUrl,
                'template_rectangle' => $templateRectangle,
                'merge_url' => $mergeUrl,
                'merge_rectangle' => $merge_rectangle,
                'merge_rate' => $mergeRate,
            ]];
        return $this->request($this->mergeFaceUrl, $params, 'POST');
    }

}