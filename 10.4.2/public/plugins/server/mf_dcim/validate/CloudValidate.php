<?php
namespace server\mf_dcim\validate;

use think\Validate;
use server\mf_dcim\logic\ToolLogic;

/**
 * @title 参数验证
 * @use   server\mf_dcim\validate\CloudValidate
 */
class CloudValidate extends Validate
{
    protected $rule = [
        'id'            => 'require|integer',
        'password'      => 'require|checkPassword:thinkphp',
        'type'          => 'require|in:1,2',
        'image_id'      => 'integer|require',
        'port'          => 'require|between:1,65535',
    ];

    protected $message  =   [
        'id.require'                => 'id_error',
        'id.integer'                => 'id_error',
        'password.require'          => 'please_input_password',
        'password.checkPassword'    => 'mf_dcim_password_format_error',
        'type.require'              => 'mf_dcim_please_select_rescue_type',
        'type.in'                   => 'mf_dcim_please_select_rescue_type',
        'image_id.require'          => 'mf_dcim_please_select_image',
        'image_id.requireWithout'   => 'mf_dcim_please_select_image',
        'image_id.integer'          => 'mf_dcim_please_select_image',
        'port.require'              => 'mf_dcim_please_input_port',
        'port.between'              => 'mf_dcim_port_format_error',   
    ];

    protected $scene = [
        'reset_password'  => ['id','password'],
        'rescue'          => ['id','type'],
        'reinstall'       => ['id','password','image_id','port'],
    ];

    // 验证密码
    public function checkPassword($value){
        if(is_null($value)){
            return true;
        }
        return ToolLogic::checkPassword($value);
    }


}