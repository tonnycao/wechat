<?php


namespace Inesadt\Wechat\Tools;


/***
 * @todo 红包
 * Class Redpack
 * @package Inesadt\Wechat\Tools
 */
class Redpack
{

    public function send()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
    }

    public function sendGroup()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack";
    }

    public function getHbInfo()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
    }


    public function sendMiniProgram()
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendminiprogramhb";
    }


}