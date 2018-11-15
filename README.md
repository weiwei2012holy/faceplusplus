## Face++ 接口集合,待完善版本



#### 1.安装

```sh
composer require weiwei2012holy/faceplusplus
```

#### 2.使用



```php

$key = '8kG5rX3D1mbK*********VWQEuFuhr8L';
$secret = 'XJwx2rcuU2***********3ZK5rb-AGak';
//初始化人脸分析
$face = new \weiwei2012holy\Face($key, $secret);
//初始化图像分析
$image = new \weiwei2012holy\Image($key, $secret);
```

#### 2.1 人脸分析

**2.1.1 发起人脸分析**

```php
//图片可以是链接,文件路径或者base64编码数据,自动处理
$mUrl = 'https://wx-static.yidejia.com/fandom1541484919647';

//发起皮肤分析
$mUrlDetail = $face->detect($mUrl, 'skinstatus');
```
请求成功示例:
```php
Array
(
    [image_id] => pFVWo+CGnzn2eoEO8AW28w==
    [request_id] => 1542263847,2612d95b-8fef-4d3e-b10a-0b5ca110fe9c
    [time_used] => 537
    [faces] => Array
        (
            [0] => Array
                (
                    [attributes] => Array
                        (
                            [skinstatus] => Array
                                (
                                    [dark_circle] => 3.157
                                    [stain] => 10.996
                                    [acne] => 5.3
                                    [health] => 4.423
                                )

                        )

                    [face_rectangle] => Array
                        (
                            [width] => 377
                            [top] => 202
                            [left] => 19
                            [height] => 377
                        )

                    [face_token] => 6d265e22d06cbb9e62f853b22cf296c5
                )

        )

)

```
**2.1.2 人脸美颜美白**

```php
人脸美白
$url = 'https://wx-static.yidejia.com/fandom1536201951787';
$res = $face->beautify($url);
file_put_contents('美颜.png', base64_decode($res['result']));
```
请求成功响应格式:
```
{
    "time_used": 544,
    "result":……省略 base64 图片数据
}
```

**2.1.3 人脸对比**
```php
//人脸对比
$url1 = 'https://wx-static.yidejia.com/fandom1536201951787';
//$url2 = 'https://wx-static.yidejia.com/fandom1536201951787';
$url2 = '0337a0d38d5c89b4c15098a695572414';
//$url1 = '0337a0d38d5c89b4c15098a695572414';
$res = $face->compare($url1, $url2);
```
响应示例:
```sh
Array
(
    [confidence] => 97.389
    [request_id] => 1542265057,9658622d-61e8-4570-95b7-85a8ad4b415d
    [time_used] => 623
    [thresholds] => Array
        (
            [1e-3] => 62.327
            [1e-5] => 73.975
            [1e-4] => 69.101
        )

)
```
#### 2.2 face set 管理
**2.2.1 创建faceSet**
```php
$res = $face->faceSetCreate('测试2','test2');

```
返回数据:
```php
Array
(
    [faceset_token] => f17b4dec9507927fa3dd5cc286c27923
    [time_used] => 180
    [face_count] => 0
    [face_added] => 0
    [request_id] => 1542262652,e9cb60e7-b354-4744-8fb3-8a9918265ced
    [outer_id] => test2
    [failure_detail] => Array
        (
        )

)
```
**2.2.2 获取face set列表**
```php
$res = $face->faceSetGetFaceSets();
```
返回数据:
```sh
Array
(
    [time_used] => 80
    [next] => ...分页用的参数
    [facesets] => Array
        (
            [0] => Array
                (
                    [faceset_token] => 154a366740b29b5ab020c3714a8bb932
                    [outer_id] =>
                    [display_name] => 测试1
                    [tags] =>
                )

            [1] => Array
                (
                    [faceset_token] => f17b4dec9507927fa3dd5cc286c27923
                    [outer_id] => test2
                    [display_name] => 测试2
                    [tags] =>
                )

        )

    [request_id] => 1542264730,7229c308-452e-454e-88f4-c889ee13775e
)
```

**2.2.3 更新face set**

```php
//更新face set
$res = $face->faceSetUpdate('154a366740b29b5ab020c3714a8bb932', ['new_outer_id' => 'test1']);
```
响应示例:
```
Array
(
    [faceset_token] => 154a366740b29b5ab020c3714a8bb932
    [request_id] => 1542266624,6cc031b3-43a6-4c1c-9e83-85e25d6a3409
    [time_used] => 102
    [outer_id] => test1
)

```

**2.2.4 获取face set 详情**
```php
$res = $face->faceSetGetDetail('154a366740b29b5ab020c3714a8bb932');
```
响应示例:
```
Array
(
    [faceset_token] => f17b4dec9507927fa3dd5cc286c27923
    [tags] =>
    [time_used] => 444
    [user_data] =>
    [display_name] => 测试2
    [face_tokens] => Array
        (
            [0] => 7b84a3cb51caab1a02b48a60d677e260
        )

    [face_count] => 1
    [request_id] => 1542273001,2293b317-8089-4d66-934c-f290254b01ca
    [outer_id] => test2
)
```

**2.2.5 删除face set**
```
$res = $face->faceSetDelete('154a366740b29b5ab020c3714a8bb932');
```
响应示例:
```
Array
(
    [faceset_token] => 154a366740b29b5ab020c3714a8bb932
    [request_id] => 1542269667,9b46e488-3e5d-4b14-b36b-158370f1b6d1
    [time_used] => 325
    [outer_id] => test1
)
```
**2.2.6 往face set 添加face_token**
```php
$res = $face->faceSetAddFace($faceSetToken,'7b84a3cb51caab1a02b48a60d677e260');
```
响应示例:
```php
同步请求:
Array
(
    [faceset_token] => f17b4dec9507927fa3dd5cc286c27923
    [time_used] => 96
    [face_count] => 1
    [face_added] => 0
    [request_id] => 1542272991,0ac07155-12ed-4456-a410-46780be72e3d
    [outer_id] => test2
    [failure_detail] => Array
        (
        )

)

如果为异步请求,则返回数据为:
Array
(
    [time_used] => 154
    [task_id] => 9e108e86-f47c-46b6-9ada-cdc7c2c7a648
    [request_id] => 1542273307,9832ced0-ee67-49f7-83db-5c274dd5e25e
)

```
**2.2.7 往face set 删除face token**
```php
$res = $face->faceSetRemoveFace($faceSetToken, '55ad5a72d902af5e582a56769cbcbb05', false, true);
```

**2.2.8 获取face token的详情**
```php
$res = $face->faceAnalyze($faceToken,'emotion');
```
**2.2.9 查询异步任务情况**
```php
$res = $face->faceSetTaskStatus($taskId);
```
响应示例:
```
Array
(
    [status] => 1
    [faceset_token] => f17b4dec9507927fa3dd5cc286c27923
    [time_used] => 31
    [task_id] => fa6a190b-d061-4bed-90b5-7ac394b7c962
    [face_count] => 5
    [face_added] => 0
    [request_id] => 1542273533,0c2cdb55-4bb0-4278-b4c5-5a2c0c1667b4
    [outer_id] => test2
    [failure_detail] => Array
        (
        )

)
```


** 呵呵**
```php

```
返回数据:
```php

```

#### 2.3 图像分析
**2.3.1 发起人脸融合**

```php

$angle = ['top', 'left', 'width', 'height'];

$mUrl = 'https://wx-static.yidejia.com/fandom1541484919647';
//发起人脸分析
$mUrlDetail = $face->detect($mUrl);
$mUrlFace = $mUrlDetail['faces'][0];
foreach ($angle as $item) {
    $mAngle[$item] = $mUrlFace['face_rectangle'][$item];
}
$mAngle = implode(',', $mAngle);
print_r($mUrlFace);

$tUrl = 'http://img2.jiemian.com/101/original/20160129/145404008426286800.jpg';
$tUrlDetail = $face->detect($tUrl);
$tUrlFace = $tUrlDetail['faces'][0];
foreach ($angle as $item) {
    $tAngle[$item] = $tUrlFace['face_rectangle'][$item];
}
$tAngle = implode(',', $tAngle);
print_r($tUrlFace);
//发起人脸融合
$res = $image->mergeFace($tUrl,  $tAngle, $mUrl,  $mAngle, 70);

print_r($res);

```
请求成功示例:
```
{
    "time_used": 544,
    "result":……省略 base64 图片数据,
    "request_id": "1510906671,53ca1f2b-c3d8-473a-858c-3b4fd99ef07a"
}
```
