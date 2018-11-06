<?php
/**
 * Desc: face++ 人脸分析
 * Author: Jerry<weiwei2012holy@hotmail.com>
 * Date: 2018/11/6,下午4:42
 */

namespace weiwei2012holy;


class Face extends FaceBase
{

    private $analyzeUrl = 'https://api-cn.faceplusplus.com/facepp/v3/face/analyze';
    /**
     * @var string 人脸检测和人脸分析api地址
     */
    private $detectUrl = 'https://api-cn.faceplusplus.com/facepp/v3/detect';


    /**
     * 人脸分析
     * @param string $file             图片内容
     * @param string $type             图片类型
     * @param int    $returnLandMark   是否检测并返回人脸关键点(0=忽略,1=83关键点,2=106关键点)
     * @param null   $returnAttributes 是否检测并返回根据人脸特征判断出的年龄、性别、情绪等属性
     * @param int    $calculateAll     是否检测并返回所有人脸的人脸关键点和人脸属性
     * @param string $faceRectangle    是否指定人脸框位置进行人脸检测,参数规格：四个正整数，用逗号分隔，依次代表人脸框左上角纵坐标（top），
     *                                 左上角横坐标（left），人脸框宽度（width），人脸框高度（height）。例如：70,80,100,100
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function detect($file, $type, $returnLandMark = 0, $returnAttributes = null, $calculateAll = 0, $faceRectangle = '')
    {
        if (!in_array($type, $this->imageType)) {
            throw new FacePlusPlusException('图片类型限定:' . implode(',', $this->imageType));
        }
        $params = [
            'http_errors' => false,
            'form_params' => [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'image_' . $type => $file,
                'return_landmark' => $returnLandMark,
                'return_attributes' => $returnAttributes,
                'face_rectangle' => $faceRectangle,
                'calculate_all' => $calculateAll,
            ]];
        return $this->request($this->detectUrl, $params, 'POST');
    }


}