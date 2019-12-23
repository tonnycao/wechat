<?php


namespace Inesadt\Wechat\Notify;


use Inesadt\Wechat\Util;

/***
 * @todo 分账动态通知
 * Class AllocationNotify
 * @package Inesadt\Wechat\Notify
 */
class AllocationNotify extends NotifyBase
{
    public function handle(){
        $raw_post = file_get_contents("php://input");
        $this->raw_xml = $raw_post;
        $this->logger->debug($raw_post);
        $post = json_decode($raw_post,true);
        $flag = true;
        if($post['resource']['algorithm']=='AEAD_AES_256_GCM'){
            $resource = Util::sodiumAes256gcmDecrypt($this->key,$post['resource']['associated_data'],$post['resource']['ciphertext'],$post['resource']['nonce']);
            if(!$resource){
                $flag =  false;
            }
        }

        if($flag){
            $this->result = $resource;
            $this->notifyOK();
        }else{
            $this->notifyError();
        }
    }

    public function notifyOK(){
        $data = [
            'code'=>'SUCCESS',
            'msg'=>'OK'
        ];
        echo json_encode($data);
    }

    public function notifyError(){
        $data = [
            'code'=>'FAIL',
            'msg'=>'失败'
        ];
        echo json_encode($data);
    }
}