<?php
/**
 * Desc: face++ 人脸分析
 * Author: Jerry<weiwei2012holy@hotmail.com>
 * Date: 2018/11/6,下午4:42
 */

namespace weiwei2012holy;


class Face extends FaceBase
{

    /**
     * @var string 接口地址
     */
    private $url = 'https://api-cn.faceplusplus.com/facepp/v3/%s';

    /**
     * @var string bata接口
     */
    private $betaUrl = 'https://api-cn.faceplusplus.com/facepp/beta/%s';


    /**
     * 人脸分析
     *
     * @param string $file             图片内容
     * @param int    $returnLandMark   是否检测并返回人脸关键点(0=忽略,1=83关键点,2=106关键点)
     * @param null   $returnAttributes 是否检测并返回根据人脸特征判断出的年龄、性别、情绪等属性,
     *                                 可选(gender,age,smiling,headpose,facequality,blur,eyestatus,emotion,ethnicity,beauty,
     *                                 mouthstatus,eyegaze,skinstatus)
     * @param int    $calculateAll     是否检测并返回所有人脸的人脸关键点和人脸属性
     * @param string $faceRectangle    是否指定人脸框位置进行人脸检测,参数规格：四个正整数，用逗号分隔，依次代表人脸框左上角纵坐标（top），
     *                                 左上角横坐标（left），人脸框宽度（width），人脸框高度（height）。例如：70,80,100,100
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function detect($file, $returnAttributes = null, $returnLandMark = 0, $calculateAll = 0, $faceRectangle = '')
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'return_landmark', 'contents' => $returnLandMark],
                ['name' => 'return_attributes', 'contents' => $returnAttributes],
                ['name' => 'face_rectangle', 'contents' => $faceRectangle],
                ['name' => 'calculate_all', 'contents' => $calculateAll],
            ]];
        $params['multipart'][] = $this->buildPostMultipart($file);
        return $this->request(sprintf($this->url, 'detect'), $params, 'POST');
    }

    /**
     * 对图片进行美颜和美白。
     *
     * @param     $file
     * @param int $whitening 美白程度，取值范围[0,100],0不美白，100代表最高程度,本参数默认值为 100
     * @param int $smoothing 磨皮程度，取值范围 [0,100],0不磨皮，100代表最高程度,本参数默认值为 100
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function beautify($file, $whitening = 100, $smoothing = 100)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'whitening', 'contents' => $whitening],
                ['name' => 'smoothing', 'contents' => $smoothing],
            ]];
        $params['multipart'][] = $this->buildPostMultipart($file);
        return $this->request(sprintf($this->betaUrl, 'beautify'), $params, 'POST');
    }

    /**
     * 人脸对比
     *
     * @param        $file1
     * @param        $file2
     * @param string $faceRectangle1 当传入图片进行人脸检测时，是否指定人脸框位置进行检测。
     *                               如果此参数传入值为空，或不传入此参数，则不使用此功能。本 API 会自动检测图片内所有区域的所有人脸。
     *                               如果使用正式 API Key
     *                               对此参数传入符合格式要求的值，则使用此功能。需要传入一个字符串代表人脸框位置，系统会根据此坐标对框内的图像进行人脸检测，
     *                               以及人脸关键点和人脸属性等后续操作。系统返回的人脸矩形框位置会与传入的
     *                               face_rectangle 完全一致。对于此人脸框之外的区域，系统不会进行人脸检测，也不会返回任何其他的人脸信息。
     *                               参数规格：四个正整数，用逗号分隔，依次代表人脸框左上角纵坐标（top），左上角横坐标（left），人脸框宽度（width），人脸框高度（height）。例如：70,80,100,100
     *                               注：只有在传入 image_url1、image_file1 和 image_base64_1 三个参数中任意一个时，本参数才生效。
     * @param string $faceRectangle2
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function compare($file1, $file2, $faceRectangle1 = '', $faceRectangle2 = '')
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'face_rectangle1', 'contents' => $faceRectangle1],
                ['name' => 'face_rectangle2', 'contents' => $faceRectangle2],
            ]];
        $params['multipart'][] = $this->buildPostMultipart($file1, 'image', 1);
        $params['multipart'][] = $this->buildPostMultipart($file2, 'image', 2);
        return $this->request(sprintf($this->url, 'compare'), $params, 'POST');
    }

    /**
     * 在一个已有的 FaceSet 中找出与目标人脸最相似的一张或多张人脸，返回置信度和不同误识率下的阈值。
     * 支持传入图片或 face_token 进行人脸搜索。使用图片进行搜索时会选取图片中检测到人脸尺寸最大的一个人脸。
     *
     * @param String $file
     * @param String $faceSet           faceset_token,outer_id的值
     * @param bool   $useOuterId        FaceSet的标识是否为outer id
     * @param int    $returnResultCount 控制返回比对置信度最高的结果的数量。合法值为一个范围 [1,5] 的整数。默认值为 1
     * @param string $faceRectangle
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($file, $faceSet, $useOuterId = false, $returnResultCount = 1, $faceRectangle = '')
    {
        $faceSetFieldName = $this->getFaceSetFieldName($useOuterId);
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'face_rectangle', 'contents' => $faceRectangle],
                ['name' => 'return_result_count', 'contents' => $returnResultCount],
            ]];
        //设定传输的faceSet字段名称
        $params['multipart'][] = ['name' => $faceSetFieldName, 'contents' => $faceSet];
        $params['multipart'][] = $this->buildPostMultipart($file);
        return $this->request(sprintf($this->url, 'search'), $params, 'POST');
    }

    /**
     * 获取某一 API Key 下的 FaceSet 列表及其 faceset_token、outer_id、display_name 和 tags 等信息
     *
     * @param string $tags
     * @param int    $start
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetGetFaceSets($tags = '', $start = 1)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'tags', 'contents' => $tags],
                ['name' => 'start', 'contents' => $start],
            ]];
        return $this->request(sprintf($this->url, 'faceset/getfacesets'), $params, 'POST');
    }

    /**
     * 创建一个人脸的集合 FaceSet
     * 用于存储人脸标识 face_token。一个 FaceSet 能够存储 1,000 个 face_token。
     *
     * @param string $displayName 人脸集合的名字，最长256个字符，不能包括字符^@,&=*'"
     * @param string $outerId     账号下全局唯一的 FaceSet 自定义标识，可以用来管理 FaceSet 对象。最长255个字符，不能包括字符^@,&=*'"
     * @param string $tags        FaceSet 自定义标签组成的字符串，用来对 FaceSet 分组。最长255个字符，多个 tag 用逗号分隔，每个 tag 不能包括字符^@,&=*'"
     * @param string $faceToken   人脸标识 face_token，可以是一个或者多个，用逗号分隔。最多不超过5个 face_token
     * @param string $userData    自定义用户信息，不大于16 KB，不能包括字符^@,&=*'"
     * @param int    $forceMerge
     *
     * @return mixed
     *                            Array
     *                            (
     *                            [faceset_token] => f17b4dec9507927fa3dd5cc286c27923
     *                            [time_used] => 180
     *                            [face_count] => 0
     *                            [face_added] => 0
     *                            [request_id] => 1542262652,e9cb60e7-b354-4744-8fb3-8a9918265ced
     *                            [outer_id] => test2
     *                            [failure_detail] => Array
     *                            (
     *                            )
     * )
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetCreate($displayName = '', $outerId = '', $tags = '', $faceToken = '', $userData = '', $forceMerge = 0)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'tags', 'contents' => $tags],
                ['name' => 'display_name', 'contents' => $displayName],
                ['name' => 'outer_id', 'contents' => $outerId],
                ['name' => 'face_token', 'contents' => $faceToken],
                ['name' => 'user_data', 'contents' => $userData],
                ['name' => 'force_merge', 'contents' => $forceMerge],
            ]];
        return $this->request(sprintf($this->url, 'faceset/create'), $params, 'POST');
    }


    /**
     * 更新face set
     *
     * @param String $faceSet    face set 标记值
     * @param array  $updateData 需要更新可数据数组,可选'new_outer_id', 'display_name', 'user_data', 'tags',tags传中文貌似报错了
     * @param bool   $useOuterId FaceSet的标识是否为outer id
     *
     * @return array
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetUpdate($faceSet, $updateData, $useOuterId = false)
    {
        $faceSetFieldName = $this->getFaceSetFieldName($useOuterId);
        $allowed = ['new_outer_id', 'display_name', 'user_data', 'tags'];
        $updateData = array_intersect_key($updateData, array_flip($allowed));
        if (empty($updateData)) {
            throw new FacePlusPlusException('更新face set数据必须存在以下值:' . implode(',', $allowed));
        }
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => $faceSetFieldName, 'contents' => $faceSet],
            ]];
        foreach ($updateData as $name => $content) {
            $params['multipart'][] = ['name' => $name, 'contents' => $content];
        }
        return $this->request(sprintf($this->url, 'faceset/update'), $params, 'POST');
    }


    /**
     * 删除face set
     *
     * @param String $faceSet    FaceSet的标识
     * @param bool   $useOuterId FaceSet的标识是否为outer id
     * @param int    $checkEmpty 删除时是否检查FaceSet中是否存在face_token
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetDelete($faceSet, $useOuterId = false, $checkEmpty = 1)
    {
        $faceSetFieldName = $this->getFaceSetFieldName($useOuterId);
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => $faceSetFieldName, 'contents' => $faceSet],
                ['name' => 'check_empty', 'contents' => $checkEmpty],
            ]];
        return $this->request(sprintf($this->url, 'faceset/delete'), $params, 'POST');
    }


    /**
     * 获取一个 FaceSet 的所有信息
     * 包括此 FaceSet 的 faceset_token, outer_id, display_name 的信息，以及此 FaceSet 中存放的 face_token 数量与列表。
     *
     * @param string $faceSet          face set凭据
     * @param bool   $useOuterId       FaceSet的标识是否为outer id
     * @param int    $start            一个数字 n，表示开始返回的 face_token 在本 FaceSet 中的序号， n 是 [1,10000] 间的一个整数。
     *                                 通过传入数字 n，可以控制本 API 从第 n 个 face_token 开始返回。返回的 face_token 按照创建时间排序，每次返回 100 个
     *                                 face_token。 您可以输入上一次请求本 API 返回的 next 值，用以获得接下来的 100 个 face_token。
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetGetDetail($faceSet, $useOuterId = false, $start = 1)
    {
        $faceSetFieldName = $this->getFaceSetFieldName($useOuterId);
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => $faceSetFieldName, 'contents' => $faceSet],
                ['name' => 'start', 'contents' => $start],
            ]];
        return $this->request(sprintf($this->url, 'faceset/getdetail'), $params, 'POST');
    }

    /**
     * 向一个face set 添加一张脸
     *
     * @param String $faceSet    FaceSet 的标识
     * @param String $faceTokens 人脸标识 face_token 组成的字符串，可以是一个或者多个，用逗号分隔。最多不超过5个face_token
     * @param bool   $useOuterId FaceSet的标识是否为outer id
     * @param bool   $async
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetAddFace($faceSet, $faceTokens, $useOuterId = false, $async = false)
    {
        $faceSetFieldName = $this->getFaceSetFieldName($useOuterId);
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => $faceSetFieldName, 'contents' => $faceSet],
                ['name' => 'face_tokens', 'contents' => $faceTokens],
            ]];
        if ($async) {
            $url = sprintf($this->url, 'faceset/async/addface');
        } else {
            $url = sprintf($this->url, 'faceset/addface');
        }
        return $this->request($url, $params, 'POST');
    }

    /**
     * 删除face set里面的face token
     *
     * @param String $faceSet
     * @param String $faceTokens
     * @param bool   $useOuterId
     * @param bool   $async
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetRemoveFace($faceSet, $faceTokens, $useOuterId = false, $async = false)
    {
        $faceSetFieldName = $this->getFaceSetFieldName($useOuterId);
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => $faceSetFieldName, 'contents' => $faceSet],
                ['name' => 'face_tokens', 'contents' => $faceTokens],
            ]];
        if ($async) {
            $url = sprintf($this->url, 'faceset/async/removeface');
        } else {
            $url = sprintf($this->url, 'faceset/removeface');
        }
        return $this->request($url, $params, 'POST');
    }

    /**
     * 查询异步任务的情况
     *
     * @param String $taskId 异步任务的唯一标识
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetTaskStatus($taskId)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'task_id', 'contents' => $taskId],
            ]];
        return $this->request(sprintf($this->url, 'faceset/async/task_status'), $params, 'POST');
    }

    /**
     * 为检测出的某一个人脸添加标识信息，该信息会在Search接口结果中返回，用来确定用户身份。
     *
     * @param string $faceToken 人脸标识face_token
     * @param string $userId    用户自定义的user_id，不超过255个字符，不能包括^@,&=*'",建议将同一个人的多个face_token设置同样的user_id。
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceSetUserId($faceToken, $userId)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'face_token', 'contents' => $faceToken],
                ['name' => 'user_id', 'contents' => $userId],
            ]];
        return $this->request(sprintf($this->url, 'face/setuserid'), $params, 'POST');
    }

    /**
     * 通过传入在Detect API检测出的人脸标识face_token，获取一个人脸的关联信息，包括源图片ID、归属的FaceSet。
     *
     * @param string $faceToken
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceGetDetail($faceToken)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'face_token', 'contents' => $faceToken],
            ]];
        return $this->request(sprintf($this->url, 'face/getdetail'), $params, 'POST');
    }

    /**
     * 通过face token获取详情
     *
     * @param string $faceToken        一个字符串，由一个或多个人脸标识组成，用逗号分隔。最多支持 5 个 face_token。
     * @param string $returnAttributes 是否检测并返回人脸关键点
     * @param int    $returnLandmark   是否检测并返回根据人脸特征判断出的年龄、性别、情绪等属性
     *
     * @return mixed
     * @throws FacePlusPlusException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function faceAnalyze($faceToken, $returnAttributes = '', $returnLandmark = 0)
    {
        $params = [
            'http_errors' => false,
            'multipart' => [
                ['name' => 'api_key', 'contents' => $this->apiKey],
                ['name' => 'api_secret', 'contents' => $this->apiSecret],
                ['name' => 'face_tokens', 'contents' => $faceToken],
                ['name' => 'return_attributes', 'contents' => $returnAttributes],
                ['name' => 'return_landmark', 'contents' => $returnLandmark],
            ]];
        return $this->request(sprintf($this->url, 'face/analyze'), $params, 'POST');
    }


}