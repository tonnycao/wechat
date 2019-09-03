<?php


namespace Inesadt\Wechat;


class Fund
{
    const VERSION = 0.1;
    protected $appId = NULL;
    protected $mchId = NULL;
    protected $key = NULL;
    protected $timeout = 10;

    const BILL_TYPE = [
        'ALL','SUCCESS','REFUND','RECHARGE_REFUND'
    ];

    const ACCOUNT_TYPE = [
        'Basic','Operation','Fees'
    ];
    public function __construct($appId,$mchId, $key,$timeout=20)
    {
        $this->appId = $appId;
        $this->mchId = $mchId;
        $this->key = $key;
        $this->timeout = $timeout;
    }

    public function bill($bill_date, $bill_type,$tar_type='')
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->mchId,
            'timeout'=>$this->timeout,
            'key'=>$this->key
        ];
        $bill_type = strtoupper($bill_type);
        if(!in_array($bill_type,self::BILL_TYPE))
        {
            return false;
        }
        $raw_bill = Api::downloadBill($config,$bill_date,$bill_type,$tar_type);
        $raw_bill = str_replace('`','',trim($raw_bill));
        $bill_arr = explode(PHP_EOL,$raw_bill);
        $details =  $total = [];
        if(count($bill_arr)>3)
        {
            unset($bill_arr[count($bill_arr)-2]);
            unset($bill_arr[0]);
            foreach ($bill_arr as $id=>$item)
            {
                $values = explode(',',$item);

                if(count($values)>20)
                {
                    $fields = $this->bill_detail_fields();
                    $details[] = array_combine($fields,$values);
                }else{
                    $fields = $this->bill_total_fields();
                    $total = array_combine($fields,$values);
                }

            }
        }

        return $bills = ['details'=>$details,'total'=>$total];
    }

    public function fundFlow($ssl_cert_path,$ssl_key_path,$bill_date, $account_type, $tar_type='GZIP')
    {
        $config = [
            'appid'=>$this->appId,
            'mch_id'=>$this->mchId,
            'timeout'=>$this->timeout,
            'key'=>$this->key,
            'ssl_cert_path'=>$ssl_cert_path,
            'ssl_key_path'=>$ssl_key_path
        ];
        $account_type = ucfirst(strtolower($account_type));
        if(!in_array($account_type,self::ACCOUNT_TYPE))
        {
            return false;
        }
        return Api::downloadFundFlow($config,$bill_date,$account_type,$tar_type);
    }

    protected function bill_detail_fields()
    {
        //﻿交易时间,公众账号ID,商户号,特约商户号,设备号,微信订单号,商户订单号,用户标识,交易类型,交易状态,付款银行,货币种类,
        //应结订单金额,代金券金额,微信退款单号,商户退款单号,退款金额,充值券退款金额,退款类型,退款状态,商品名称,商户数据包,手续费,费率,订单金额,申请退款金额,费率备注
        $fields = [
            'trade_time','appid','mch_id','sub_mch_id','device','transaction_id','out_trade_no','openid','trade_type','trade_state','pay_bank','fee_type',
            'total_fee','coupon_fee','refund_id','out_refund_no','refund_fee','coupon_refund_fee','refund_type','refund_state',
            'goods_name','goods_desc','fee','rate','order_total_fee','refund_apply_fee','rate_note'
        ];
        return $fields;
    }

    public function bill_total_fields()
    {
        //总交易单数,应结订单总金额,退款总金额,充值券退款总金额,手续费总金额,订单总金额,申请退款总金额
        return $fields = [
            'order_total','should_order_total_money','refund_total_money','coupon_total_money','fee_total_money','order_total_money','refund_apply_total_money'
        ];
    }
}