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
     * @param        $templateFile
     * @param        $templateRectangle
     * @param        $mergeFile
     * @param string $mergeRectangle
     * @param int    $mergeRate
     * @return array
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mergeFace($templateFile, $templateRectangle, $mergeFile, $mergeRectangle = '', $mergeRate = 50)
    {
        $templateType = $this->checkImageType($templateFile);
        $mergeType = $this->checkImageType($mergeFile);
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'template_rectangle', 'contents' => $templateRectangle],
                ['name' => 'merge_rectangle', 'contents' => $mergeRectangle],
                ['name' => 'merge_rate', 'contents' => $mergeRate],
            ]];
        if ($templateType == 'file') {
            $params['multipart'][] = ['name' => 'template_' . $templateType, 'contents' => fopen($templateFile, 'r')];
        } else {
            $params['multipart'][] = ['name' => 'template_' . $templateType, 'contents' => $templateFile];
        }
        if ($mergeType == 'file') {
            $params['multipart'][] = ['name' => 'merge_' . $mergeType, 'contents' => fopen($mergeFile, 'r')];
        } else {
            $params['multipart'][] = ['name' => 'merge_' . $mergeType, 'contents' => $mergeFile];
        }
        return $this->request($this->mergeFaceUrl, $params, 'POST');
    }

}