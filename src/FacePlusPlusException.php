<?php
/**
 * Desc: FacePlusPlusException
 * Author: 余伟<weiwei2012holy@hotmail.com>
 * Date: 2018/11/6,下午4:48
 */

namespace weiwei2012holy;


use Throwable;

class FacePlusPlusException extends \Exception
{
    protected $data;

    /**
     * FacePlusPlusException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param null           $data
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null, $data = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 扩展返回原始信息
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

}