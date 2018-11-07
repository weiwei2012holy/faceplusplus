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

$angle = ['top', 'left', 'width', 'height'];

$mUrl = 'https://wx-static.yidejia.com/fandom1541484919647';
//发起人脸分析
$mUrlDetail = $face->detect($mUrl, 'url');
$mUrlFace = $mUrlDetail['faces'][0];
foreach ($angle as $item) {
    $mAngle[$item] = $mUrlFace['face_rectangle'][$item];
}
$mAngle = implode(',', $mAngle);
print_r($mUrlFace);

$tUrl = 'http://img2.jiemian.com/101/original/20160129/145404008426286800.jpg';
$tUrlDetail = $face->detect($tUrl, 'url');
$tUrlFace = $tUrlDetail['faces'][0];
foreach ($angle as $item) {
    $tAngle[$item] = $tUrlFace['face_rectangle'][$item];
}
$tAngle = implode(',', $tAngle);
print_r($tUrlFace);
//发起人脸融合
$res = $image->mergeFace($tUrl, 'url', $tAngle, $mUrl, 'url', $mAngle, 70);

print_r($res);

```