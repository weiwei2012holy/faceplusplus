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
     * @var string 图像识别api
     */
    private $url = 'https://api-cn.faceplusplus.com/imagepp/v1/%s';


    /**
     * 人脸融合
     *
     * @param        $templateFile
     * @param        $templateRectangle
     * @param        $mergeFile
     * @param string $mergeRectangle
     * @param int    $mergeRate
     *
     * @return array
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mergeFace($templateFile, $templateRectangle, $mergeFile, $mergeRectangle = '', $mergeRate = 50)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'template_rectangle', 'contents' => $templateRectangle],
                ['name' => 'merge_rectangle', 'contents' => $mergeRectangle],
                ['name' => 'merge_rate', 'contents' => $mergeRate],
            ]];
        $params['multipart'][] = $this->buildPostMultipart($templateFile, 'template');
        $params['multipart'][] = $this->buildPostMultipart($mergeFile, 'merge');
        return $this->request(sprintf($this->url, 'mergeface'), $params, 'POST');
    }

}