<?php
namespace reserver\mf_dcim\controller\home;

use app\admin\model\PluginModel;
use think\facade\Cache;
use think\facade\View;
use reserver\mf_dcim\validate\HostValidate;
use reserver\mf_dcim\logic\RouteLogic;
use app\common\model\OrderModel;
use app\common\model\UpstreamOrderModel;
use app\common\model\UpstreamProductModel;
use app\common\model\HostModel;
use app\common\model\MenuModel;
use app\common\model\SystemLogModel;
use app\common\model\SelfDefinedFieldModel;

/**
 * @title 魔方DCIM代理(自定义配置)-前台
 * @desc 魔方DCIM代理(自定义配置)-前台
 * @use reserver\mf_dcim\controller\home\CloudController
 */
class CloudController
{
	/**
	 * 时间 2023-02-06
	 * @title 获取订购页面配置
	 * @desc 获取订购页面配置
	 * @url /console/v1/product/:id/remf_dcim/order_page
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @return  int data_center[].id - 国家ID
	 * @return  string data_center[].iso - 图标
	 * @return  string data_center[].name - 名称
	 * @return  string data_center[].city[].name - 城市
	 * @return  int data_center[].city[].area[].id - 数据中心ID
	 * @return  string data_center[].city[].area[].name - 区域
	 * @return  int data_center[].city[].area[].line[].id - 线路ID
	 * @return  string data_center[].city[].area[].line[].name - 线路名称
	 * @return  int data_center[].city[].area[].line[].data_center_id - 数据中心ID
	 * @return  string data_center[].city[].area[].line[].bill_type - 计费类型(bw=带宽计费,flow=流量计费)
	 * @return  int model_config[].id - 型号配置ID
	 * @return  string model_config[].name - 型号配置名称
	 * @return  string model_config[].cpu - 处理器
	 * @return  string model_config[].cpu_param - 处理器参数
	 * @return  string model_config[].memory - 内存
	 * @return  string model_config[].disk - 硬盘
	 * @return  int model_config[].support_optional - 允许增值选配(0=不允许,1=允许)
	 * @return  int model_config[].optional_only_for_upgrade - 增值仅用于升降级(0=关闭,1=开启)
	 * @return  int model_config[].leave_memory - 剩余内存
	 * @return  int model_config[].max_memory_num - 可增加内存数量
	 * @return  int model_config[].max_disk_num - 可增加硬盘数量
	 * @return  string model_config[].gpu - 显卡
	 * @return  int model_config[].max_gpu_num - 可增加显卡数量
	 * @return  int model_config[].optional_memory[].id - 选配内存配置ID
	 * @return  string model_config[].optional_memory[].value - 选配内存配置名称
	 * @return  int model_config[].optional_memory[].other_config.memory - 选配内存大小
	 * @return  int model_config[].optional_memory[].other_config.memory_slot - 选配内存插槽
	 * @return  int model_config[].optional_disk[].id - 选配硬盘配置ID
	 * @return  string model_config[].optional_disk[].value - 选配硬盘配置名称
	 * @return  int model_config[].optional_gpu[].id - 选配显卡配置ID
	 * @return  string model_config[].optional_gpu[].value - 选配显卡配置名称
	 * @return  int config_limit[].data_center_id - 数据中心ID
	 * @return  int config_limit[].line_id - 线路ID
	 * @return  string config_limit[].min_bw - 带宽最小值
	 * @return  string config_limit[].max_bw - 带宽最大值
	 * @return  string config_limit[].min_flow - 流量最小值
	 * @return  string config_limit[].max_flow - 流量最大值
	 * @return  array config_limit[].model_config_id - 型号配置ID
	 */
	public function orderPage()
	{
		$param = request()->param();

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_dcim/order_page', $RouteLogic->upstream_product_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->orderPage();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-06
	 * @title 获取操作系统列表
	 * @desc 获取操作系统列表
	 * @url /console/v1/product/:id/remf_dcim/image
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @return  int list[].id - 操作系统分类ID
	 * @return  string list[].name - 操作系统分类名称
	 * @return  string list[].icon - 操作系统分类图标
	 * @return  int list[].image[].id - 操作系统ID
	 * @return  int list[].image[].image_group_id - 操作系统分类ID
	 * @return  string list[].image[].name - 操作系统名称
	 * @return  int list[].image[].charge - 是否收费(0=否,1=是)
	 * @return  string list[].image[].price - 价格
	 * @return  string list[].image[].price_client_level_discount - 价格等级折扣
	 */
	public function imageList()
	{
		$param = request()->param();
        $productId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%s/remf_dcim/image', $RouteLogic->upstream_product_id), $param, 'GET');
			if($result['status'] == 200){
                // 处理多级代理问题
                $PluginModel = new PluginModel();
                $plugin = $PluginModel->where('status',1)->where('name','IdcsmartClientLevel')->find();
				// 计算价格倍率
				foreach($result['data']['list'] as $k=>$v){
					foreach($v['image'] as $kk=>$vv){
                        if (isset($vv['price_client_level_discount'])){
                            $vv['price'] = bcsub($vv['price'],$vv['price_client_level_discount'],2);
                        }
						if($vv['charge'] == 1){
							$result['data']['list'][$k]['image'][$kk]['price'] = $RouteLogic->profit_type==1?bcadd($vv['price'],$RouteLogic->getProfitPercent()*100):bcmul($vv['price'], $RouteLogic->price_multiple);
						}
                        if (!empty($plugin)){
                            $IdcsmartClientLevelModel = new \addon\idcsmart_client_level\model\IdcsmartClientLevelModel();
                            // 获取商品折扣金额
                            $clientLevelDiscount = $IdcsmartClientLevelModel->productDiscount([
                                'id' => $productId,
                                'amount' => $result['data']['list'][$k]['image'][$kk]['price']
                            ]);
                            // 二级代理及以下给下游的客户等级折扣数据
                            $result['data']['list'][$k]['image'][$kk]['price_client_level_discount'] = $clientLevelDiscount??0;
                        }
					}
				}
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->imageList();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-06
	 * @title 获取商品配置所有周期价格
	 * @desc 获取商品配置所有周期价格
	 * @url /console/v1/product/:id/remf_dcim/duration
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   int model_config_id - 型号配置ID
	 * @param   object optional_memory - 选配内存(如{"5":"12"},5是选配内存配置ID,12是数量)
	 * @param   object optional_disk - 选配硬盘(如{"5":"12"},5是选配硬盘配置ID,12是数量)
	 * @param   object optional_gpu - 选配显卡(如{"5":"12"},5是选配显卡配置ID,12是数量)
     * @param   int image_id - 镜像ID
     * @param   int line_id - 线路ID
     * @param   int bw - 带宽(带宽线路)
     * @param   int flow - 流量(流量线路)
     * @param   int ip_num - 公网IP数量
     * @param   int peak_defence - 防御峰值
     * @return  int [].id - 周期ID
     * @return  string [].name - 周期名称
     * @return  string [].price - 周期总价
     * @return  float [].discount - 折扣(0=没有折扣)
     * @return  int [].num - 周期时长
     * @return  string [].unit - 单位(hour=小时,day=天,month=月)
     * @return  string [].client_level_discount - 用户等级折扣
     * @return  string [].price_client_level_discount - 价格用户等级折扣
	 */
	public function getAllDurationPrice()
	{
		$param = request()->param();
        $productId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_dcim/duration', $RouteLogic->upstream_product_id), $param, 'POST');
			if($result['status'] == 200){
                // 处理多级代理问题
                $PluginModel = new PluginModel();
                $plugin = $PluginModel->where('status',1)->where('name','IdcsmartClientLevel')->find();
				// 计算价格倍率
				foreach($result['data'] as $k=>$v){
                    if (isset($v['price_client_level_discount'])){
                        $v['price'] = bcsub($v['price'],$v['price_client_level_discount'],2);
                    }
					if($v['price'] > 0){
						$result['data'][$k]['price'] = $RouteLogic->profit_type==1?bcadd($v['price'],$RouteLogic->getProfitPercent()*100):bcmul($v['price'], $RouteLogic->price_multiple);
					}
                    if (!empty($plugin)){
                        $IdcsmartClientLevelModel = new \addon\idcsmart_client_level\model\IdcsmartClientLevelModel();
                        // 获取商品折扣金额
                        $clientLevelDiscount = $IdcsmartClientLevelModel->productDiscount([
                            'id' => $productId,
                            'amount' => $result['data'][$k]['price']
                        ]);
                        // 二级代理及以下给下游的客户等级折扣数据
                        $result['data'][$k]['price_client_level_discount'] = $clientLevelDiscount??0;
                    }
				}
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->getAllDurationPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-14
	 * @title 获取线路配置
	 * @desc 获取线路配置
	 * @url /console/v1/product/:id/remf_dcim/line/:line_id
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   int line_id - 线路ID require
	 * @return  string bill_type - 计费类型(bw=带宽,flow=流量)
	 * @return  string bw[].type - 配置方式(radio=单选,step=阶梯,total=总量)
	 * @return  string bw[].value - 带宽
	 * @return  int bw[].min_value - 最小值
	 * @return  int bw[].max_value - 最大值
	 * @return  int bw[].step - 步长
	 * @return  string flow[].value - 流量(流量线路)
	 * @return  string defence[].value - 防御
	 * @return  string ip[].value - 公网IP值
	 * @return  string ip[].desc - 公网IP显示
	 */
	public function lineConfig()
	{
		$param = request()->param();

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_dcim/line/%d', $RouteLogic->upstream_product_id, $param['line_id']), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->lineConfig();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	* 时间 2023-02-09
	* @title 产品列表
	* @desc 产品列表
	* @url /console/v1/remf_dcim
	* @method  GET
	* @author hh
	* @version v1
    * @param   int page 1 页数
    * @param   int limit - 每页条数
    * @param   string orderby - 排序(id,due_time,status)
    * @param   string sort - 升/降序
    * @param   string keywords - 关键字搜索,搜索套餐名称/主机名/IP
    * @param   string status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
    * @param   int m - 菜单ID
    * @return  int list[].id - 产品ID
    * @return  int list[].product_id - 商品ID
    * @return  string list[].name - 产品标识
    * @return  string list[].status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
    * @return  int list[].active_time - 开通时间
    * @return  int list[].due_time - 到期时间
    * @return  string list[].product_name - 商品名称
    * @return  string list[].client_notes - 备注
    * @return  object list[].self_defined_field - 自定义字段值(键是自定义字段ID,值是填的内容)
    * @return  int count - 总条数
    * @return  int self_defined_field[].id - 自定义字段ID
    * @return  string self_defined_field[].field_name - 自定义字段名称
    * @return  string self_defined_field[].field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,tickbox=勾选框,textarea=文本区)
	*/
	public function list()
	{
		$param = request()->param();

		$result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => [
                'list'  => [],
                'count' => 0,
                'self_defined_field' => [],
            ]
        ];

        $clientId = get_client_id();
        if(empty($clientId)){
            return json($result);
        }

        $where = [];
        if(isset($param['m']) && !empty($param['m'])){
        	// 菜单,菜单里面必须是下游商品
            $MenuModel = MenuModel::where('menu_type', 'res_module')
            			->where('module', 'mf_dcim')
                        ->where('id', $param['m'])
                        ->find();
            if(!empty($MenuModel) && !empty($MenuModel['product_id'])){
                $MenuModel['product_id'] = json_decode($MenuModel['product_id'], true);
                if(!empty($MenuModel['product_id'])){
                	$upstreamProduct = UpstreamProductModel::whereIn('product_id', $MenuModel['product_id'])->where('res_module', 'mf_dcim')->find();
                	if(!empty($upstreamProduct)){
                		$where[] = ['h.product_id', 'IN', $MenuModel['product_id'] ];
                	}
                }
            }
        }else{
        	//return json($result);
        }

        $param['page'] = isset($param['page']) ? ($param['page'] ? (int)$param['page'] : 1) : 1;
        $param['limit'] = isset($param['limit']) ? ($param['limit'] ? (int)$param['limit'] : config('idcsmart.limit')) : config('idcsmart.limit');
        $param['sort'] = isset($param['sort']) ? ($param['sort'] ?: config('idcsmart.sort')) : config('idcsmart.sort');
        $param['orderby'] = isset($param['orderby']) && in_array($param['orderby'], ['id','due_time','status']) ? $param['orderby'] : 'id';
        $param['orderby'] = 'h.'.$param['orderby'];

        $where[] = ['h.client_id', '=', $clientId];
        $where[] = ['h.status', '<>', 'Cancelled'];
        if(isset($param['status']) && !empty($param['status'])){
            if($param['status'] == 'Pending'){
                $where[] = ['h.status', 'IN', ['Pending','Failed']];
            }else if(in_array($param['status'], ['Unpaid','Active','Suspended','Deleted'])){
                $where[] = ['h.status', '=', $param['status']];
            }
        }
        if(isset($param['keywords']) && $param['keywords'] !== ''){
        	$where[] = ['h.name', 'LIKE', '%'.$param['keywords'].'%'];
        }

        // 获取子账户可见产品
        $res = hook('get_client_host_id', ['client_id' => get_client_id(false)]);
        $res = array_values(array_filter($res ?? []));
        foreach ($res as $key => $value) {
            if(isset($value['status']) && $value['status']==200){
                $hostId = $value['data']['host'];
            }
        }
        if(isset($hostId) && !empty($hostId)){
            $where[] = ['h.id', 'IN', $hostId];
        }

        // hh 20240319 先做兼容处理,后续稳定后不用判断
        $supportOrderRecycleBin = is_numeric(configuration('order_recycle_bin'));
        if($supportOrderRecycleBin){
            $where[] = ['h.is_delete', '=', 0];
        }

        $count = HostModel::alias('h')
            ->leftJoin('product p', 'h.product_id=p.id')
            ->join('upstream_product up', 'p.id=up.product_id AND up.res_module="mf_dcim"')
            ->where($where)
            ->count();

        $host = HostModel::alias('h')
            ->field('h.id,h.product_id,h.name,h.status,h.active_time,h.due_time,p.name product_name,h.client_notes')
            ->leftJoin('product p', 'h.product_id=p.id')
            ->join('upstream_product up', 'p.id=up.product_id AND up.res_module="mf_dcim"')
            ->where($where)
            ->withAttr('status', function($val){
                return $val == 'Failed' ? 'Pending' : $val;
            })
            ->limit($param['limit'])
            ->page($param['page'])
            ->order($param['orderby'], $param['sort'])
            ->group('h.id')
            ->select()
            ->toArray();
        if(!empty($host) && class_exists('app\common\model\SelfDefinedFieldModel')){
            $hostId = array_column($host, 'id');
            $productId = array_column($host, 'product_id');

            $SelfDefinedFieldModel = new SelfDefinedFieldModel();
            $selfDefinedField = $SelfDefinedFieldModel->getHostListSelfDefinedFieldValue([
                'product_id' => $productId,
                'host_id'    => $hostId,
            ]);
        }
        foreach($host as $k=>$v){
            $host[$k]['self_defined_field'] = $selfDefinedField['self_defined_field_value'][ $v['id'] ] ?? (object)[];
        }

        $result['data']['list']  = $host;
        $result['data']['count'] = $count;
        $result['data']['self_defined_field'] = $selfDefinedField['self_defined_field'] ?? [];
        return json($result);
	}

	/**
	 * 时间 2022-06-29
	 * @title 获取DCIM产品详情
	 * @desc 获取DCIM产品详情
	 * @url /console/v1/remf_dcim/:id
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @return  int order_id - 订单ID
     * @return  string ip - IP地址
     * @return  string additional_ip - 附加IP(英文分号分割)
     * @return  string power_status - 电源状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  int model_config.id - 型号配置ID
     * @return  string model_config.name - 型号配置名称
     * @return  string model_config.cpu - 处理器
     * @return  string model_config.cpu_param - 处理器参数
     * @return  string model_config.memory - 内存
     * @return  string model_config.disk - 硬盘
     * @return  string model_config.gpu - 显卡
     * @return  int model_config.optional_memory[].id - 可选配内存配置ID
     * @return  string model_config.optional_memory[].value - 名称
     * @return  int model_config.optional_memory[].other_config.memory_slot - 槽位
     * @return  int model_config.optional_memory[].other_config.memory - 内存大小(GB)
     * @return  int model_config.optional_disk[].id - 可选配硬盘配置ID
     * @return  string model_config.optional_disk[].value - 名称
     * @return  int model_config.optional_gpu[].id - 可选配显卡配置ID
     * @return  string model_config.optional_gpu[].value - 名称
     * @return  int model_config.leave_memory - 当前机型剩余内存大小(GB)
     * @return  int model_config.max_memory_num - 当前机型可增加内存数量
     * @return  int model_config.max_disk_num - 当前机型可增加硬盘数量
     * @return  int model_config.max_gpu_num - 当前机型可增加显卡数量
     * @return  int line.id - 线路
     * @return  string line.name - 线路名称
     * @return  string line.bill_type - 计费类型(bw=带宽,flow=流量)
     * @return  string bw - 带宽(0表示没有)
     * @return  string ip_num - IP数量
     * @return  string peak_defence - 防御峰值
     * @return  string username - 用户名
     * @return  string password - 密码
     * @return  int data_center.id - 数据中心ID
     * @return  string data_center.city - 城市
     * @return  string data_center.area - 区域
     * @return  string data_center.country - 国家
     * @return  string data_center.iso - 国家代码
     * @return  int image.id - 镜像ID
     * @return  string image.name - 镜像名称
     * @return  string image.image_group_name - 镜像分类
     * @return  string image.icon - 图标
     * @return  int config.reinstall_sms_verify - 重装短信验证(0=不启用,1=启用)
     * @return  int config.reset_password_sms_verify - 重置密码短信验证(0=不启用,1=启用)
     * @return  int config.manual_resource - 是否手动资源(0=不启用,1=启用)
     * @return  object optional_memory - 当前机器已添加内存配置({"5":1},5是ID,1是数量)
     * @return  object optional_disk - 当前机器已添加硬盘配置({"5":1},5是ID,1是数量)
     * @return  object optional_gpu - 当前机器已添加显卡配置({"5":1},5是ID,1是数量)
	 */
	public function detail()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->detail();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-27
	 * @title 获取部分详情
	 * @desc 获取部分详情
	 * @url /console/v1/remf_dcim/:id/part
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  int data_center.id - 数据中心ID
     * @return  string data_center.city - 城市
     * @return  string data_center.area - 区域
     * @return  string data_center.country - 国家
     * @return  string data_center.iso - 图标
     * @return  string ip - IP地址
     * @return  string power_status - 电源状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  int image.id - 镜像ID
     * @return  string image.name - 镜像名称
     * @return  string image.image_group_name - 镜像分类
     * @return  string image.icon - 图标
	 */
	public function detailPart()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/part', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->detailPart();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 开机
	 * @desc 开机
	 * @url /console/v1/remf_dcim/:id/on
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 */
	public function on()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/on', $RouteLogic->upstream_host_id), [], 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_dcim_log_host_start_boot_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_dcim_log_host_start_boot_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->on();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 关机
	 * @desc 关机
	 * @url /console/v1/remf_dcim/:id/off
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 */
	public function off()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete'] ){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/off', $RouteLogic->upstream_host_id), [], 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_dcim_log_host_start_off_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_dcim_log_host_start_off_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->off();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 重启
	 * @desc 重启
	 * @url /console/v1/remf_dcim/:id/reboot
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 */
	public function reboot()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/reboot', $RouteLogic->upstream_host_id), [], 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_dcim_log_host_start_reboot_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_dcim_log_host_start_reboot_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->reboot();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-29
	 * @title 获取控制台地址
	 * @desc 获取控制台地址
	 * @url /console/v1/remf_dcim/:id/vnc
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int more 0 获取更多信息(0=否,1=是)
	 * @return  string url - 控制台地址
	 * @return  string vnc_url - 控制台websocket地址(more=1返回)
	 * @return  string vnc_pass - vnc密码(more=1返回)
	 * @return  string password - 机器密码(more=1返回)
	 * @return  string token - 控制台页面令牌(more=1返回)
	 */
	public function vnc()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/vnc?more=1', $RouteLogic->upstream_host_id), [], 'POST');

			if($result['status'] == 200){
				$cache = $result['data'];
				unset($cache['url']);
 
				if(isset($cache['token'])){
					Cache::set('remf_dcim_vnc_'.$param['id'], $cache, 30*60);
					if(!isset($param['more']) || $param['more'] != 1){
						// 不获取更多信息
						$result['data'] = [];
					}
					// 转到当前res模块
					$result['data']['url'] = request()->domain().'/console/v1/remf_dcim/'.$param['id'].'/vnc?tmp_token='.$cache['token'];
				}
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->vnc();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-01
	 * @title 控制台页面
	 * @desc 控制台页面
	 * @url /console/v1/remf_dcim/:id/vnc
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string tmp_token - 控制台页面令牌 require
	 */
	public function vncPage()
	{
		$param = request()->param();

		$cache = Cache::get('remf_dcim_vnc_'.$param['id']);
		if(!empty($cache) && isset($param['tmp_token']) && $param['tmp_token'] === $cache['token']){
			View::assign($cache);
		}else{
			return lang_plugins('res_mf_dcim_vnc_token_expired_please_reopen');
		}
		return View::fetch(WEB_ROOT . 'plugins/reserver/mf_dcim/view/vnc_page.html');
	}

	/**
	 * 时间 2022-06-24
	 * @title 获取实例状态
	 * @desc 获取实例状态
	 * @url /console/v1/remf_dcim/:id/status
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string data.status - 实例状态(on=开机,off=关机,operating=操作中,fault=故障)
	 * @return  string data.desc - 实例状态描述
	 */
	public function status()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/status', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->status();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-24
	 * @title 重置密码
	 * @desc 重置密码
	 * @url /console/v1/remf_dcim/:id/reset_password
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string password - 新密码 require
	 */
	public function resetPassword()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/reset_password', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_dcim_log_host_start_reset_password_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_dcim_log_host_start_reset_password_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->resetPassword();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-24
	 * @title 救援模式
	 * @desc 救援模式
	 * @url /console/v1/remf_dcim/:id/rescue
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int type - 指定救援系统类型(1=windows,2=linux) require
	 */
	public function rescue()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/rescue', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_dcim_log_host_start_rescue_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_dcim_log_host_start_rescue_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->rescue();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-30
	 * @title 重装系统
 	 * @desc 重装系统
	 * @url /console/v1/remf_dcim/:id/reinstall
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @param   string password - 密码 require
	 * @param   int port - 端口 require
	 */
	public function reinstall()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/reinstall', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_dcim_log_host_start_reinstall_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_dcim_log_host_start_reinstall_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->reinstall();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 获取图表数据
	 * @desc 获取图表数据
	 * @url /console/v1/remf_dcim/:id/chart
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int start_time - 开始秒级时间
	 * @return  array list - 图表数据
	 * @return  int list[].time - 时间(秒级时间戳)
	 * @return  float list[].in_bw - 进带宽
	 * @return  float list[].out_bw - 出带宽
	 * @return  string unit - 当前单位
	 */
	public function chart()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/chart', $RouteLogic->upstream_host_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->chart();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-30
	 * @title 获取网络流量
	 * @desc 获取网络流量
	 * @url /console/v1/remf_dcim/:id/flow
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string total -总流量
	 * @return  string used -已用流量
	 * @return  string leave - 剩余流量
	 * @return  string reset_flow_date - 流量归零时间
	 */
	public function flowDetail()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/flow', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->flowDetail();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-01
	 * @title 日志
	 * @desc 日志
	 * @url /console/v1/remf_dcim/:id/log
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param int id - 产品ID
     * @param string keywords - 关键字
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id,description,create_time,ip
     * @param string sort - 升/降序 asc,desc
     * @return array list - 系统日志
     * @return int list[].id - 系统日志ID
     * @return string list[].description - 描述
     * @return string list[].create_time - 时间
     * @return int list[].ip - IP
     * @return int count - 系统日志总数
	 */
	public function log()
	{
		$param = request()->param();
		$param['type'] = 'host';
		$param['rel_id'] = $param['id'];

		$SystemLogModel = new SystemLogModel();
	 	$data = $SystemLogModel->systemLogList($param);

	 	$result = [
	 		'status' => 200,
	 		'msg'	 => lang_plugins('success_message'),
	 		'data'	 => $data,
	 	];
	 	return json($result);
	}

	/**
	 * 时间 2022-09-14
	 * @title 获取DCIM远程信息
	 * @desc 获取DCIM远程信息
	 * @url console/v1/remf_dcim/:id/remote_info
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string username - 远程用户名
	 * @return  string password - 远程密码
	 * @return  string port - 远程端口
	 * @return  int ip_num - IP数量
	 */
	public function remoteInfo()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/remote_info', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->remoteInfo();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-01
	 * @title 获取IP列表
	 * @desc 获取IP列表
	 * @url /console/v1/remf_dcim/:id/ip
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param int id - 产品ID
     * @param int page 1 页数
     * @param int limit - 每页条数
     * @return int list[].ip - IP
     * @return string list[].subnet_mask - 掩码
     * @return string list[].gateway - 网关
     * @return int count - 总数
	 */
	public function ipList()
	{
		$param = array_merge(request()->param(), ['page' => request()->page, 'limit' => request()->limit, 'sort' => request()->sort]);

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/ip', $RouteLogic->upstream_host_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->ipList();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 检查产品是够购买过镜像
	 * @desc 检查产品是够购买过镜像
	 * @url /console/v1/remf_dcim/:id/image/check
	 * @method GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @return  string price - 需要支付的金额(0.00表示镜像免费或已购买)
	 * @return  string description - 描述
	 * @return  string price_client_level_discount - 价格等级折扣
	 */
	public function checkHostImage()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_image')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }
        $hostId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/image/check', $RouteLogic->upstream_host_id), $param, 'GET');
			if($result['status'] == 200){
                if (isset($result['data']['price_client_level_discount'])){
                    $result['data']['price'] = bcsub($result['data']['price'],$result['data']['price_client_level_discount'],2);
                }
                if ($RouteLogic->profit_type==1){
                    $profit = bcadd(0, $RouteLogic->getProfitPercent()*100);
                }else{
                    $profit = bcmul($result['data']['price'], $RouteLogic->getProfitPercent());
                }
				$result['data']['price'] = bcadd($result['data']['price'], $profit);

                // 处理多级代理问题
                $HostModel = new HostModel();
                $host = $HostModel->find($hostId);
                $PluginModel = new PluginModel();
                $plugin = $PluginModel->where('status',1)->where('name','IdcsmartClientLevel')->find();
                if (!empty($plugin)){
                    $IdcsmartClientLevelModel = new \addon\idcsmart_client_level\model\IdcsmartClientLevelModel();
                    // 获取商品折扣金额
                    $clientLevelDiscount = $IdcsmartClientLevelModel->productDiscount([
                        'id' => $host['product_id'],
                        'amount' => $result['data']['price']
                    ]);
                    // 二级代理及以下给下游的客户等级折扣数据
                    $result['data']['price_client_level_discount'] = $clientLevelDiscount??0;
                }
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->checkHostImage();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 生成购买镜像订单
	 * @desc 生成购买镜像订单
	 * @url /console/v1/remf_dcim/:id/image/order
	 * @method POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @return  string id - 订单ID
	 */
	public function createImageOrder()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_image')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

        $hostId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/image/check', $RouteLogic->upstream_host_id), $param, 'GET');
			if($result['status'] == 200){
                if (isset($result['data']['price_client_level_discount'])){
                    $result['data']['price'] = bcsub($result['data']['price'],$result['data']['price_client_level_discount'],2);
                }
                if ($RouteLogic->profit_type==1){
                    $profit = bcadd(0, $RouteLogic->getProfitPercent()*100);
                }else{
                    $profit = bcmul($result['data']['price'], $RouteLogic->getProfitPercent());
                }
				$result['data']['price'] = bcadd($result['data']['price'], $profit);

				$OrderModel = new OrderModel();

		        $data = [
		            'host_id'     => $hostId,
		            'client_id'   => get_client_id(),
		            'type'        => 'upgrade_config',
		            'amount'      => $result['data']['price'],
		            'description' => $result['data']['description'],
		            'price_difference' => $result['data']['price'],
		            'renew_price_difference' => 0,
		            'upgrade_refund' => 0,
		            'config_options' => [
		                'type'       => 'buy_image',
		                'param'		 => $param,
		            ],
		            'customfield' => $param['customfield'] ?? [],
		        ];
				$result = $OrderModel->createOrder($data);
				if($result['status'] == 200){
					UpstreamOrderModel::create([
						'supplier_id' 	=> $RouteLogic->supplier_id,
						'order_id' 		=> $result['data']['id'],
						'host_id' 		=> $hostId,
						'amount' 		=> $data['amount'],
						'profit' 		=> $profit,
						'create_time' 	=> time(),
					]);
				}
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->createImageOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 计算产品配置升级价格
	 * @desc 计算产品配置升级价格
	 * @url /console/v1/remf_dcim/:id/common_config
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int ip_num - 公网IP数量
     * @param   int bw - 带宽
     * @param   int flow - 流量包
     * @param   int peak_defence - 防御峰值
     * @param   object optional_memory - 变更后的内存({"5":1},5是ID,1是数量)
     * @param   object optional_disk - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   object optional_gpu - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @return  string price - 价格
     * @return  string description - 生成的订单描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string discount - 用户等级折扣
     * @return  string price_client_level_discount - 价格等级折扣
     * @return  string price_difference_client_level_discount - 差价等级折扣
     * @return  string renew_price_difference_client_level_discount - 续费差价等级折扣
	 */
	public function calCommonConfigPrice()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

        $hostId = $param['id'];
 		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/common_config', $RouteLogic->upstream_host_id), $param, 'GET');
			if($result['status'] == 200){

                if (isset($result['data']['downgrade']) && $result['data']['downgrade']){

                }else{
                    if (isset($result['data']['price_client_level_discount'])){
                        $result['data']['price'] = bcsub($result['data']['price'],$result['data']['price_client_level_discount'],2);
                    }
                    if (isset($result['data']['price_difference_client_level_discount'])){
                        $result['data']['price_difference'] = bcsub($result['data']['price_difference'],$result['data']['price_difference_client_level_discount'],2);
                    }
                    if (isset($result['data']['renew_price_difference_client_level_discount'])){
                        $result['data']['renew_price_difference'] = bcsub($result['data']['renew_price_difference'],$result['data']['renew_price_difference_client_level_discount'],2);
                    }
                }

				if(isset($result['data']['discount']) && !empty($result['data']['discount'])){
                    // 加上等级折扣
                    unset($result['data']['discount']);
                }
                if(isset($result['data']['order_item'])){
                    unset($result['data']['order_item']);
                }
                if ($RouteLogic->profit_type==1){
                    $profit = bcadd(0, $RouteLogic->getProfitPercent()*100);

                    $result['data']['price'] = bcadd($result['data']['price'], $profit);
                    $result['data']['price_difference'] = bcadd($result['data']['price_difference'], $RouteLogic->getProfitPercent());
                    $result['data']['renew_price_difference'] = bcadd($result['data']['renew_price_difference'], $RouteLogic->getProfitPercent());
                }else{
                    $profit = bcmul($result['data']['price'], $RouteLogic->getProfitPercent());

                    $result['data']['price'] = bcadd($result['data']['price'], $profit);
                    $result['data']['price_difference'] = bcmul($result['data']['price_difference'], $RouteLogic->getPriceMultiple());
                    $result['data']['renew_price_difference'] = bcmul($result['data']['renew_price_difference'], $RouteLogic->getPriceMultiple());
                }

                if ($result['data']['price']<0){
                    $result['data']['downgrade'] = true;
                }

                // 处理多级代理问题
                $HostModel = new HostModel();
                $host = $HostModel->find($hostId);
                $PluginModel = new PluginModel();
                $plugin = $PluginModel->where('status',1)->where('name','IdcsmartClientLevel')->find();
                if (!empty($plugin)){
                    $IdcsmartClientLevelModel = new \addon\idcsmart_client_level\model\IdcsmartClientLevelModel();
                    // 获取商品折扣金额
                    $clientLevelDiscount = $IdcsmartClientLevelModel->productDiscount([
                        'id' => $host['product_id'],
                        'amount' => $result['data']['price']
                    ]);
                    $clientLevelDiscountPriceDifference = $IdcsmartClientLevelModel->productDiscount([
                        'id' => $host['product_id'],
                        'amount' => $result['data']['price_difference']
                    ]);
                    $clientLevelDiscountRenewPriceDifference = $IdcsmartClientLevelModel->productDiscount([
                        'id' => $host['product_id'],
                        'amount' => $result['data']['renew_price_difference']
                    ]);
                    // 二级代理及以下给下游的客户等级折扣数据
                    $result['data']['price_client_level_discount'] = $clientLevelDiscount??0;
                    $result['data']['price_difference_client_level_discount'] = $clientLevelDiscountPriceDifference??0;
                    $result['data']['renew_price_difference_client_level_discount'] = $clientLevelDiscountRenewPriceDifference??0;
                }
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->calCommonConfigPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 生成产品配置升级订单
	 * @desc 生成产品配置升级订单
	 * @url /console/v1/remf_dcim/:id/common_config/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int ip_num - 公网IP数量
     * @param   int bw - 带宽
     * @param   int flow - 流量包
     * @param   int peak_defence - 防御峰值
     * @param   object optional_memory - 变更后的内存({"5":1},5是ID,1是数量)
     * @param   object optional_disk - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   object optional_gpu - 变更后的硬盘({"5":1},5是ID,1是数量)
	 * @return  string id - 订单ID
	 */
	public function createCommonConfigOrder()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

        $hostId = $param['id'];
 		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_dcim/%d/common_config', $RouteLogic->upstream_host_id), $param, 'GET');
			if($result['status'] == 200){
                if (isset($result['data']['downgrade']) && $result['data']['downgrade']){

                }else{
                    if (isset($result['data']['price_client_level_discount'])){
                        $result['data']['price'] = bcsub($result['data']['price'],$result['data']['price_client_level_discount'],2);
                    }
                    if (isset($result['data']['price_difference_client_level_discount'])){
                        $result['data']['price_difference'] = bcsub($result['data']['price_difference'],$result['data']['price_difference_client_level_discount'],2);
                    }
                    if (isset($result['data']['renew_price_difference_client_level_discount'])){
                        $result['data']['renew_price_difference'] = bcsub($result['data']['renew_price_difference'],$result['data']['renew_price_difference_client_level_discount'],2);
                    }
                }

				if(isset($result['data']['discount']) && !empty($result['data']['discount'])){
                    // 加上等级折扣
                    unset($result['data']['discount']);
                }
                if(isset($result['data']['order_item'])){
                    unset($result['data']['order_item']);
                }
                if ($RouteLogic->profit_type==1){
                    $profit = bcadd(0, $RouteLogic->getProfitPercent()*100);

                    $result['data']['price'] = bcadd($result['data']['price'], $profit);
                    $result['data']['price_difference'] = bcadd($result['data']['price_difference'], $RouteLogic->getProfitPercent());
                    $result['data']['renew_price_difference'] = bcadd($result['data']['renew_price_difference'], $RouteLogic->getProfitPercent());
                }else{
                    $profit = bcmul($result['data']['price'], $RouteLogic->getProfitPercent());

                    $result['data']['price'] = bcadd($result['data']['price'], $profit);
                    $result['data']['price_difference'] = bcmul($result['data']['price_difference'], $RouteLogic->getPriceMultiple());
                    $result['data']['renew_price_difference'] = bcmul($result['data']['renew_price_difference'], $RouteLogic->getPriceMultiple());
                }

                if ($result['data']['price']<0){
                    $result['data']['downgrade'] = true;
                }

				$OrderModel = new OrderModel();

		        $data = [
		            'host_id'     => $hostId,
		            'client_id'   => get_client_id(),
		            'type'        => 'upgrade_config',
		            'amount'      => $result['data']['price'],
		            'description' => $result['data']['description'],
		            'price_difference' => $result['data']['price_difference'],
		            'renew_price_difference' => $result['data']['renew_price_difference'],
		            'upgrade_refund' => 0,
		            'config_options' => [
		                'type'       => 'upgrade_common_config',
		                'param'		 => $param,
		            ],
		            'customfield' => $param['customfield'] ?? [],
		        ];
				$result = $OrderModel->createOrder($data);
				if($result['status'] == 200){
					UpstreamOrderModel::create([
						'supplier_id' 	=> $RouteLogic->supplier_id,
						'order_id' 		=> $result['data']['id'],
						'host_id' 		=> $hostId,
						'amount' 		=> $data['amount'],
						'profit' 		=> $profit,
						'create_time' 	=> time(),
					]);
				}
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->createCommonConfigOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-03-01
	 * @title 验证下单
	 * @desc 验证下单
	 * @url /console/v1/product/:id/remf_dcim/validate_settle
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
     * @param   int custom.duration_id - 周期ID require
     * @param   int custom.data_center_id - 数据中心ID require
     * @param   int custom.model_config_id - 型号配置ID require
     * @param   object custom.optional_memory - 变更后的内存({"5":1},5是ID,1是数量)
     * @param   object custom.optional_disk - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   object custom.optional_gpu - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   int custom.image_id - 镜像ID
     * @param   string custom.bw - 带宽
     * @param   string custom.flow - 流量
     * @param   string custom.ip_num - 公网IP数量
     * @param   int custom.peak_defence - 防御峰值(G)
     * @return  string price - 价格 
     * @return  string renew_price - 续费价格 
     * @return  string billing_cycle - 周期 
     * @return  int duration - 周期时长
     * @return  string description - 订单子项描述
     * @return  string base_price - 基础价格
     * @return  string preview[].name - 配置项名称
     * @return  string preview[].value - 配置项值
     * @return  string preview[].price - 配置项价格
     * @return  string discount - 用户等级折扣
     * @return  string order_item[].type - 订单子项类型(addon_idcsmart_client_level=用户等级)
     * @return  int order_item[].rel_id - 关联ID
     * @return  float order_item[].amount - 子项金额
     * @return  string order_item[].description - 子项描述
	 */
	public function validateSettle()
	{
		$param = request()->param();

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_dcim/validate_settle', $RouteLogic->upstream_product_id), $param, 'POST');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_dcim\controller\home\CloudController')){
					return (new \server\mf_dcim\controller\home\CloudController())->validateSettle();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_dcim_act_exception')];
			}
		}
		return json($result);
	}

}
