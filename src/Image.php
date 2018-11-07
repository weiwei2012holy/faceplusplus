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
     * @param        $templateType
     * @param        $templateRectangle
     * @param        $mergeFile
     * @param        $mergeType
     * @param string $mergeRectangle
     * @param int    $mergeRate
     * @return array
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mergeFace($templateFile, $templateType, $templateRectangle, $mergeFile, $mergeType, $mergeRectangle = '', $mergeRate = 50)
    {
        if (!in_array($templateType, $this->imageType)) {
            throw new FacePlusPlusException('模板类型限定:' . implode(',', $this->imageType));
        }
        if (!in_array($mergeType, $this->imageType)) {
            throw new FacePlusPlusException('素材类型限定:' . implode(',', $this->imageType));
        }
        $params = [
            'http_errors' => false,
            'form_params' => [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'template_' . $templateType => $templateFile,
                'template_rectangle' => $templateRectangle,
                'merge_' . $mergeType => $mergeFile,
                'merge_rectangle' => $mergeRectangle,
                'merge_rate' => $mergeRate,
            ]];
        return $this->request($this->mergeFaceUrl, $params, 'POST');
    }

}