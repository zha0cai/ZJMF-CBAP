<?php
namespace reserver\mf_cloud\controller\home;

use app\admin\model\PluginModel;
use think\facade\Cache;
use think\facade\View;
use reserver\mf_cloud\validate\HostValidate;
use reserver\mf_cloud\validate\CartValidate;
use reserver\mf_cloud\logic\RouteLogic;
use app\common\model\UpstreamHostModel;
use app\common\model\OrderModel;
use app\common\model\UpstreamOrderModel;
use app\common\model\UpstreamProductModel;
use app\common\model\HostModel;
use app\common\model\MenuModel;
use app\common\model\SystemLogModel;
use app\common\model\SelfDefinedFieldModel;

/**
 * @title 魔方云代理(自定义配置)-前台
 * @desc 魔方云代理(自定义配置)-前台
 * @use reserver\mf_cloud\controller\home\CloudController
 */
class CloudController
{
	/**
	 * 时间 2023-02-06
	 * @title 获取订购页面配置
	 * @desc 获取订购页面配置
	 * @url /console/v1/product/:id/remf_cloud/order_page
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   string scene custom 场景(recommend=套餐,custom=自定义)
	* @return  int data_center[].id - 国家ID
	* @return  string data_center[].iso - 图标
	* @return  string data_center[].name - 名称
	* @return  string data_center[].city[].name - 城市
	* @return  int data_center[].city[].area[].id - 数据中心ID
	* @return  string data_center[].city[].area[].name - 区域
	* @return  int data_center[].city[].area[].reommend_config[].id - 推荐配置ID
	* @return  string data_center[].city[].area[].reommend_config[].name - 推荐配置名称
	* @return  string data_center[].city[].area[].reommend_config[].description - 推荐配置描述
	* @return  int data_center[].city[].area[].reommend_config[].line_id - 线路ID
	* @return  int data_center[].city[].area[].reommend_config[].cpu - CPU
	* @return  int data_center[].city[].area[].reommend_config[].memory - 内存
	* @return  int data_center[].city[].area[].reommend_config[].system_disk_size - 系统盘
	* @return  string data_center[].city[].area[].reommend_config[].system_disk_type - 系统盘类型
	* @return  int data_center[].city[].area[].reommend_config[].data_disk_size - 数据盘
	* @return  string data_center[].city[].area[].reommend_config[].data_disk_type - 数据盘类型
	* @return  string data_center[].city[].area[].reommend_config[].network_type - 网络类型(normal=经典网络,vpc=vpc网络)
	* @return  int data_center[].city[].area[].reommend_config[].bw - 带宽
	* @return  int data_center[].city[].area[].reommend_config[].flow - 流量
	* @return  int data_center[].city[].area[].reommend_config[].peak_defence - 防护峰值
	* @return  int data_center[].city[].area[].reommend_config[].ip_num - IP数量
	* @return  int data_center[].city[].area[].reommend_config[].upgrade_range - 升降级范围(0=不可升降级,1=全部,2=自选)
	* @return  int data_center[].city[].area[].reommend_config[].gpu_num - GPU数量
	* @return  int data_center[].city[].area[].reommend_config[].gpu_name - GPU型号
	* @return  int data_center[].city[].area[].line[].id - 线路ID
	* @return  string data_center[].city[].area[].line[].name - 线路名称
	* @return  int data_center[].city[].area[].line[].data_center_id - 数据中心ID
	* @return  string data_center[].city[].area[].line[].bill_type - 计费类型(bw=带宽计费,flow=流量计费)
	* @return  array cpu - CPU配置
	* @return  int cpu[].id - 配置ID
	* @return  int cpu[].value - 核心数
	* @return  int memory[].id - 配置ID
	* @return  array memory- 内存配置
	* @return  string memory[].type - 配置类型(radio=单选,step=阶梯,total=完整)
	* @return  int memory[].value - 配置值
	* @return  int memory[].min_value - 最小值
	* @return  int memory[].max_value - 最大值
     * @return  int memory[].step - 最小变化值
     * @return  array system_disk - 系统盘配置
     * @return  int system_disk[].id - 配置ID
     * @return  string system_disk[].type - 配置类型(radio=单选,step=阶梯,total=完整)
     * @return  int system_disk[].value - 配置值
     * @return  int system_disk[].min_value - 最小值
     * @return  int system_disk[].max_value - 最大值
     * @return  int system_disk[].step - 最小变化值
     * @return  string system_disk[].other_config.disk_type - 磁盘类型
     * @return  string system_disk[].other_config.store_id - 储存ID
     * @return  string system_disk[].customfield.multi_language.other_config.disk_type - 多语言磁盘类型(有就替换)
     * @return  array data_disk - 数据盘配置
     * @return  int data_disk[].id - 配置ID
     * @return  string data_disk[].type - 配置类型(radio=单选,step=阶梯,total=完整)
     * @return  int data_disk[].value - 配置值
     * @return  int data_disk[].min_value - 最小值
     * @return  int data_disk[].max_value - 最大值
     * @return  int data_disk[].step - 最小变化值
     * @return  string data_disk[].other_config.disk_type - 磁盘类型
     * @return  string data_disk[].other_config.store_id - 储存ID
     * @return  string data_disk[].customfield.multi_language.other_config.disk_type - 多语言磁盘类型(有就替换)
     * @return  string config.type - 实例类型(host=KVM加强版,lightHost=KVM轻量版,hyperv=Hyper-V)
     * @return  int config.support_ssh_key - 是否支持SSH密钥(0=不支持,1=支持)
     * @return  int config.support_normal_network - 是否支持经典网络(0=不支持,1=支持)
     * @return  int config.support_vpc_network - 是否支持VPC网络(0=不支持,1=支持)
     * @return  int config.support_public_ip - 是否允许公网IP(0=不支持,1=支持)
     * @return  int config.backup_enable - 是否启用备份(0=不支持,1=支持)
     * @return  int config.snap_enable - 是否启用快照(0=不支持,1=支持)
     * @return  string config.memory_unit - 内存单位(GB,MB)
     * @return  int config.disk_limit_num - 数据盘数量限制
     * @return  int config.free_disk_switch - 免费数据盘开关(0=关闭,1=开启)
     * @return  int config.free_disk_size - 免费数据盘大小(GB)
     * @return  int config.only_sale_recommend_config - 仅售卖套餐(0=关闭,1=开启)
     * @return  int config.no_upgrade_tip_show - 不可升降级时订购页提示(0=关闭,1=开启)
     * @return  int config.default_nat_acl - 默认NAT转发(0=关闭,1=开启)
     * @return  int config.default_nat_web - 默认NAT建站(0=关闭,1=开启)
     * @return  int config.ip_mac_bind_enable - 是否启用嵌套虚拟化(0=关闭,1=开启)
     * @return  int config.nat_acl_limit_enable - 是否启用NAT转发(0=关闭,1=开启)
     * @return  int config.nat_web_limit_enable - 是否启用NAT建站(0=关闭,1=开启)
     * @return  int config.ipv6_num_enable - 是否启用IPv6(0=关闭,1=开启)
     * @return  int backup_config[].id - 备份配置ID
     * @return  int backup_config[].num - 备份数量
     * @return  string backup_config[].price - 备份价格
     * @return  string backup_config[].price_client_level_discount - 用户等级折扣
     * @return  int snap_config[].id - 快照ID
     * @return  int snap_config[].num - 快照数量
     * @return  string snap_config[].price - 快照价格
     * @return  string snap_config[].price_client_level_discount - 用户等级折扣
     * @return  string config_limit[].type - 配置限制类型(cpu=CPU与内存限制,data_center=数据中心与计算限制,line=带宽与计算限制)
     * @return  int config_limit[].data_center_id - 数据中心ID
     * @return  int config_limit[].line_id - 线路ID
     * @return  int config_limit[].min_bw - 最小带宽
     * @return  int config_limit[].max_bw - 最大带宽
     * @return  string config_limit[].cpu - cpu(英文逗号分隔)
     * @return  string config_limit[].memory - 内存(英文逗号分隔)
     * @return  int config_limit[].min_memory - 最小内存
     * @return  int config_limit[].max_memory - 最大内存
     * @return  int resource_package[].id - 资源包ID
     * @return  string resource_package[].name - 资源包名称
	 */
	public function orderPage()
	{
		$param = request()->param();

		$productId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_cloud/order_page', $RouteLogic->upstream_product_id), $param, 'GET');
			if($result['status'] == 200){
                // 处理多级代理问题
                $PluginModel = new PluginModel();
                $plugin = $PluginModel->where('status',1)->where('name','IdcsmartClientLevel')->find();
				// 计算价格倍率
				if(isset($result['data']['snap_config'])){
					foreach($result['data']['snap_config'] as $k=>$v){
                        if (isset($v['price_client_level_discount'])){
                            $v['price'] = bcsub($v['price'],$v['price_client_level_discount'],2);
                        }
						if($v['price']>0){
							$result['data']['snap_config'][$k]['price'] = $RouteLogic->profit_type==1?bcadd($v['price'],$RouteLogic->getProfitPercent()*100):bcmul($v['price'], $RouteLogic->price_multiple);
						}
                        if (!empty($plugin)){
                            $IdcsmartClientLevelModel = new \addon\idcsmart_client_level\model\IdcsmartClientLevelModel();
                            // 获取商品折扣金额
                            $clientLevelDiscount = $IdcsmartClientLevelModel->productDiscount([
                                'id' => $productId,
                                'amount' => $result['data']['snap_config'][$k]['price']
                            ]);
                            // 二级代理及以下给下游的客户等级折扣数据
                            $result['data']['snap_config'][$k]['price_client_level_discount'] = $clientLevelDiscount??0;
                        }
					}
				}
				if(isset($result['data']['backup_config'])){
					foreach($result['data']['backup_config'] as $k=>$v){
                        if (isset($v['price_client_level_discount'])){
                            $v['price'] = bcsub($v['price'],$v['price_client_level_discount'],2);
                        }
						if($v['price']>0){
							$result['data']['backup_config'][$k]['price'] = $RouteLogic->profit_type==1?bcadd($v['price'],$RouteLogic->getProfitPercent()*100):bcmul($v['price'], $RouteLogic->price_multiple);
						}
                        if (!empty($plugin)){
                            $IdcsmartClientLevelModel = new \addon\idcsmart_client_level\model\IdcsmartClientLevelModel();
                            // 获取商品折扣金额
                            $clientLevelDiscount = $IdcsmartClientLevelModel->productDiscount([
                                'id' => $productId,
                                'amount' => $result['data']['backup_config'][$k]['price']
                            ]);
                            // 二级代理及以下给下游的客户等级折扣数据
                            $result['data']['backup_config'][$k]['price_client_level_discount'] = $clientLevelDiscount??0;
                        }
					}
				}
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->orderPage();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-06
	 * @title 获取操作系统列表
	 * @desc 获取操作系统列表
	 * @url /console/v1/product/:id/remf_cloud/image
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
     * @return  string list[].image[].price_client_level_discount - 用户等级折扣
	 */
	public function imageList()
	{
		$param = request()->param();
        $productId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%s/remf_cloud/image', $RouteLogic->upstream_product_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->imageList();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-06
	 * @title 获取商品配置所有周期价格
	 * @desc 获取商品配置所有周期价格
	 * @url /console/v1/product/:id/remf_cloud/duration
	 * @method  POST
	 * @author hh
	 * @version v1
     * @param   int id - 商品ID require
     * @param   int recommend_config_id - 套餐ID
     * @param   int cpu - CPU
     * @param   int memory - 内存
     * @param   int system_disk.size - 系统盘大小
     * @param   string system_disk.disk_type - 系统盘类型
     * @param   int data_disk[].size - 数据盘大小
     * @param   string data_disk[].disk_type - 系统盘类型
     * @param   int line_id - 线路ID
     * @param   int bw - 带宽
     * @param   int flow - 流量
     * @param   int peak_defence - 防御峰值(G)
     * @param   int ip_num - 附加IP数量
     * @param   int gpu_num - 显卡数量
     * @param   int image_id 0 镜像ID
     * @param   int backup_num 0 备份数量
     * @param   int snap_num 0 快照数量
     * @return  int [].id - 周期ID
     * @return  string [].name - 周期名称
     * @return  string [].name_show - 周期名称多语言替换
     * @return  string [].price - 周期总价
     * @return  float [].discount - 折扣(0=没有折扣)
     * @return  int [].num - 周期时长
     * @return  string [].unit - 单位(hour=小时,day=天,month=月)
     * @return  string [].price_client_level_discount - 用户等级折扣
	 */
	public function getAllDurationPrice()
	{
		$param = request()->param();
        $productId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_cloud/duration', $RouteLogic->upstream_product_id), $param, 'POST');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->getAllDurationPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-09
	 * @title 获取配置限制规则
	 * @desc 获取配置限制规则
	 * @url /console/v1/product/:id/remf_cloud/config_limit
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
     * @return  string [].type - 类型(cpu=CPU与内存限制,data_center=数据中心与计算限制)
     * @return  int [].data_center_id - 数据中心ID
     * @return  array [].cpu - CPU
     * @return  array [].memory - 内存
     * @return  int [].min_memory - 最小内存
     * @return  int [].max_memory - 最大内存
	 */
	public function getAllConfigLimit()
	{
		$param = request()->param();

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/product/%s/remf_cloud/config_limit', $RouteLogic->upstream_product_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->getAllConfigLimit();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 获取可用VPC网络
	 * @desc 获取可用VPC网络
	 * @url /console/v1/product/:id/remf_cloud/vpc_network/search
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
     * @param  int data_center_id - 数据中心ID
     * @return int list[].id - VPC网络ID
     * @return string list[].name - VPC网络名称
	 */
	public function vpcNetworkSearch()
	{
		$param = request()->param();

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/product/%s/remf_cloud/vpc_network/search', $RouteLogic->upstream_product_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->vpcNetworkSearch();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	* 时间 2023-02-09
	* @title 产品列表
	* @desc 产品列表
	* @url /console/v1/remf_cloud
	* @method  GET
	* @author hh
	* @version v1
    * @param   int page 1 页数
    * @param   int limit - 每页条数
    * @param   string orderby - 排序(id,due_time,status)
    * @param   string sort - 升/降序
    * @param   string keywords - 关键字搜索主机名
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
            			->where('module', 'mf_cloud')
                        ->where('id', $param['m'])
                        ->find();
            if(!empty($MenuModel) && !empty($MenuModel['product_id'])){
                $MenuModel['product_id'] = json_decode($MenuModel['product_id'], true);
                if(!empty($MenuModel['product_id'])){
                	$upstreamProduct = UpstreamProductModel::whereIn('product_id', $MenuModel['product_id'])->where('res_module', 'mf_cloud')->find();
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
            ->join('upstream_product up', 'p.id=up.product_id AND up.res_module="mf_cloud"')
            ->where($where)
            ->count();

        $host = HostModel::alias('h')
            ->field('h.id,h.product_id,h.name,h.status,h.active_time,h.due_time,p.name product_name,h.client_notes')
            ->leftJoin('product p', 'h.product_id=p.id')
            ->join('upstream_product up', 'p.id=up.product_id AND up.res_module="mf_cloud"')
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
	 * @title 获取实例详情
	 * @desc 获取实例详情
	 * @url /console/v1/remf_cloud/:id
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID
     * @return  string type - 类型(host=KVM加强版,lightHost=KVM轻量版,hyperv=Hyper-V)
     * @return  int order_id - 订单ID
     * @return  string ip - IP地址
     * @return  int ip_num - 附加IP数量
     * @return  int backup_num - 允许备份数量
     * @return  int snap_num - 允许快照数量
     * @return  string power_status - 电源状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  string cpu - CPU
     * @return  int memory - 内存
     * @return  int system_disk.size - 系统盘大小(G)
     * @return  string system_disk.type - 系统盘类型
     * @return  int line.id - 线路ID
     * @return  string line.name - 线路名称
     * @return  string line.bill_type - 计费类型(bw=带宽计费,flow=流量计费)
     * @return  int bw - 带宽
     * @return  int peak_defence - 防御峰值(G)
     * @return  string network_type - 网络类型(normal=经典网络,vpc=VPC网络)
     * @return  string gpu - 显卡
     * @return  string username - 用户名
     * @return  string password - 密码
     * @return  int data_center.id - 数据中心ID
     * @return  string data_center.city - 城市
     * @return  string data_center.area - 区域
     * @return  string data_center.country - 国家
     * @return  string data_center.iso - 图标
     * @return  int image.id - 镜像ID
     * @return  string image.name - 镜像名称
     * @return  string image.image_group_name - 镜像分组
     * @return  string image.icon - 图标
     * @return  int ssh_key.id - SSH密钥ID
     * @return  string ssh_key.name - SSH密钥名称
     * @return  int nat_acl_limit - NAT转发数量
     * @return  int nat_web_limit - NAT建站数量
     * @return  int config.reinstall_sms_verify - 重装短信验证(0=不启用,1=启用)
     * @return  int config.reset_password_sms_verify - 重置密码短信验证(0=不启用,1=启用)
     * @return  int security_group.id - 关联的安全组ID(0=没关联)
     * @return  string security_group.name - 关联的安全组名称
     * @return  int recommend_config.id - 套餐ID(有表示是套餐)
     * @return  int recommend_config.product_id - 商品ID
     * @return  string recommend_config.name - 套餐名称
     * @return  string recommend_config.description - 套餐描述
     * @return  int recommend_config.order - 排序
     * @return  int recommend_config.data_center_id - 数据中心ID
     * @return  int recommend_config.cpu - CPU
     * @return  int recommend_config.memory - 内存(GB)
     * @return  int recommend_config.system_disk_size - 系统盘大小(G)
     * @return  int recommend_config.data_disk_size - 数据盘大小(G)
     * @return  int recommend_config.bw - 带宽
     * @return  int recommend_config.peak_defence - 防御峰值(G)
     * @return  string recommend_config.system_disk_type - 系统盘类型
     * @return  string recommend_config.data_disk_type - 数据盘类型
     * @return  int recommend_config.flow - 流量
     * @return  int recommend_config.line_id - 线路ID
     * @return  int recommend_config.create_time - 创建时间
     * @return  int recommend_config.ip_num - IP数量
     * @return  int recommend_config.upgrade_range - 升降级范围(0=不可升降级,1=全部,2=自选)
     * @return  int recommend_config.hidden - 是否隐藏(0=否,1=是)
     * @return  int recommend_config.gpu_num - 显卡数量
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

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->detail();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-27
	 * @title 获取部分详情
	 * @desc 获取部分详情
	 * @url /console/v1/remf_cloud/:id/part
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

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/part', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->detailPart();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 开机
	 * @desc 开机
	 * @url /console/v1/remf_cloud/:id/on
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
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/on', $RouteLogic->upstream_host_id), [], 'POST');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_boot_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_boot_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->on();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 关机
	 * @desc 关机
	 * @url /console/v1/remf_cloud/:id/off
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 */
	public function off()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/off', $RouteLogic->upstream_host_id), [], 'POST');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_off_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_off_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $param['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->off();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 重启
	 * @desc 重启
	 * @url /console/v1/remf_cloud/:id/reboot
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
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/reboot', $RouteLogic->upstream_host_id), [], 'POST');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_reboot_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_reboot_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $param['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->reboot();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 强制关机
	 * @desc 强制关机
	 * @url /console/v1/remf_cloud/:id/hard_off
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 */
	public function hardOff()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/hard_off', $RouteLogic->upstream_host_id), [], 'POST');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_hard_off_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_hard_off_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $param['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->hardOff();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 强制重启
	 * @desc 强制重启
	 * @url /console/v1/remf_cloud/:id/hard_reboot
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 */
	public function hardReboot()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/hard_reboot', $RouteLogic->upstream_host_id), [], 'POST');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_hard_reboot_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_hard_reboot_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $param['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->hardReboot();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-29
	 * @title 获取控制台地址
	 * @desc 获取控制台地址
	 * @url /console/v1/remf_cloud/:id/vnc
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int more 0 是否获取更多返回(0=否,1=是)
	 * @return  string url - 控制台地址
	 * @return  string vnc_url - vncwebsocket地址(more=1返回)
	 * @return  string vnc_pass - VNC密码(more=1返回)
	 * @return  string password - 实例密码(more=1返回)
	 * @return  string token - 临时令牌(more=1返回)
	 */
	public function vnc()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vnc?more=1', $RouteLogic->upstream_host_id), [], 'POST');
			if($result['status'] == 200){
				$cache = $result['data'];
				unset($cache['url']);

				Cache::set('idcsmart_cloud_vnc_'.$param['id'], $cache, 30*60);
				if(!isset($param['more']) || $param['more'] != 1){
					// 不获取更多信息
					$result['data'] = [];
				}
				// 转到当前res模块
				$result['data']['url'] = request()->domain().'/console/v1/remf_cloud/'.$param['id'].'/vnc?tmp_token='.$cache['token'];
			}

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_vnc_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_vnc_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $param['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->vnc();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-01
	 * @title 控制台页面
	 * @desc 控制台页面
	 * @url /console/v1/remf_cloud/:id/vnc
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string temp_token - 临时令牌 require
	 */
	public function vncPage()
	{
		$param = request()->param();

		$cache = Cache::get('idcsmart_cloud_vnc_'.$param['id']);
		if(!empty($cache) && isset($param['tmp_token']) && $param['tmp_token'] === $cache['token']){
			View::assign($cache);
		}else{
			return lang_plugins('res_mf_cloud_vnc_token_expired_please_reopen');
		}
		return View::fetch(WEB_ROOT . 'plugins/reserver/mf_cloud/view/vnc_page.html');
	}

	/**
	 * 时间 2022-06-24
	 * @title 获取实例状态
	 * @desc 获取实例状态
	 * @url /console/v1/remf_cloud/:id/status
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string data.status - 实例状态(on=开机,off=关机,suspend=暂停,operating=操作中,fault=故障)
	 * @return  string data.desc - 实例状态描述
	 */
	public function status()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/status', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->status();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-24
	 * @title 重置密码
	 * @desc 重置密码
	 * @url /console/v1/remf_cloud/:id/reset_password
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
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/reset_password', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_reset_password_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_reset_password_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->resetPassword();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-24
	 * @title 救援模式
	 * @desc 救援模式
	 * @url /console/v1/remf_cloud/:id/rescue
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int type - 指定救援系统类型(1=windows,2=linux) require
	 * @param   string password - 救援系统临时密码 require
	 */
	public function rescue()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/rescue', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_rescue_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_rescue_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->rescue();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-24
	 * @title 退出救援模式
	 * @desc 退出救援模式
	 * @url /console/v1/remf_cloud/:id/rescue/exit
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 */
	public function exitRescue()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/rescue/exit', $RouteLogic->upstream_host_id), [], 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_exit_rescue_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_exit_rescue_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->exitRescue();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-30
	 * @title 重装系统
 	 * @desc 重装系统
	 * @url /console/v1/remf_cloud/:id/reinstall
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @param   int password - 密码 require
	 * @param   int port - 端口 require
	 */
	public function reinstall()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/reinstall', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_reinstall_success', [
					'{hostname}' => $HostModel['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_reinstall_fail', [
					'{hostname}' => $HostModel['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->reinstall();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 获取图表数据
	 * @desc 获取图表数据
	 * @url /console/v1/remf_cloud/:id/chart
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int start_time - 开始秒级时间
	 * @param   string type - 图表类型(cpu=CPU,memory=内存,disk_io=硬盘IO,bw=带宽) require
	 * @return  int list[].time - 时间(秒级时间戳)
	 * @return  float list[].value - CPU使用率
	 * @return  int list[].total - 总内存(单位:B)
	 * @return  int list[].used - 内存使用量(单位:B)
	 * @return  float list[].read_bytes - 读取速度(B/s)
	 * @return  float list[].write_bytes - 写入速度(B/s)
	 * @return  float list[].read_iops - 读取IOPS
	 * @return  float list[].write_iops - 写入IOPS
	 * @return  float list[].in_bw - 进带宽(bps)
	 * @return  float list[].out_bw - 出带宽(bps)
	 */
	public function chart()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/chart', $RouteLogic->upstream_host_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->chart();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-30
	 * @title 获取网络流量
	 * @desc 获取网络流量
	 * @url /console/v1/remf_cloud/:id/flow
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

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/flow', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->flowDetail();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-11
	 * @title 获取实例磁盘
	 * @desc 获取实例磁盘
	 * @url /console/v1/remf_cloud/:id/disk
	 * @method  GET
	 * @author theworld
	 * @version v1
	 * @param   int id - 产品ID require
     * @return  int list[].id - 魔方云磁盘ID
     * @return  string list[].name - 名称
     * @return  int list[].size - 磁盘大小(GB)
     * @return  int list[].create_time - 创建时间
     * @return  string list[].type - 磁盘类型
     * @return  string list[].type2 - 类型(system=系统盘,data=数据盘)
     * @return  int list[].is_free - 是否免费盘(0=否,1=是),免费盘不能扩容
	 */
	public function disk()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/disk', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->disk();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-10
	 * @title 卸载磁盘
	 * @desc 卸载磁盘
	 * @url /console/v1/remf_cloud/:id/disk/:disk_id/unmount
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int disk_id - 磁盘ID require
	 * @return  string name - 磁盘名称
	 */
	public function diskUnmount()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/disk/%d/unmount', $RouteLogic->upstream_host_id, $param['disk_id']), [], 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_unmount_disk_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['disk_id'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_unmount_disk_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['disk_id'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->diskUnmount();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-10
	 * @title 挂载磁盘
	 * @desc 挂载磁盘
	 * @url /console/v1/remf_cloud/:id/disk/:disk_id/mount
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int disk_id - 磁盘ID require
	 * @return  string name - 磁盘名称
	 */
	public function diskMount()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/disk/%d/mount', $RouteLogic->upstream_host_id, $param['disk_id']), [], 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_mount_disk_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['disk_id'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_mount_disk_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['disk_id'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->diskMount();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 快照列表
	 * @desc 快照列表
	 * @url /console/v1/remf_cloud/:id/snapshot
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int page - 页数
	 * @param   int limit - 每页条数
	 * @return  int list[].id - 快照ID
	 * @return  string list[].name - 快照名称
	 * @return  int list[].create_time - 创建时间
	 * @return  string list[].notes - 备注
	 * @return  int list[].status - 状态(0=创建中,1=创建完成)
	 * @return  int count - 总条数
	 */
	public function snapshot()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/snapshot', $RouteLogic->upstream_host_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->snapshot();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-11
	 * @title 创建快照
	 * @desc 创建快照
	 * @url /console/v1/remf_cloud/:id/snapshot
	 * @method  POST
	 * @author theworld
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int name - 快照名称 require
	 * @param   int disk_id - 磁盘ID require
	 */
	public function snapshotCreate()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/snapshot', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_create_snap_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $param['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_create_snap_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $param['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->snapshotCreate();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 快照还原
	 * @desc 快照还原
	 * @url /console/v1/remf_cloud/:id/snapshot/restore
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int snapshot_id - 快照ID require
	 * @return  string name - 快照名称
	 */
	public function snapshotRestore()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/snapshot/restore', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_snap_restore_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['snapshot_id'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_snap_restore_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['snapshot_id'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->snapshotRestore();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 删除快照
	 * @desc 删除快照
	 * @url /console/v1/remf_cloud/:id/snapshot/:snapshot_id
	 * @method  DELETE
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int snapshot_id - 快照ID require
	 * @return  string name - 快照名称
	 */
	public function snapshotDelete()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/snapshot/%d', $RouteLogic->upstream_host_id, $param['snapshot_id']), [], 'DELETE');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_delete_snap_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['snapshot_id'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_delete_snap_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['snapshot_id'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->snapshotDelete();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 备份列表
	 * @desc 备份列表
	 * @url /console/v1/remf_cloud/:id/backup
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int page - 页数
	 * @param   int limit - 每页条数
	 * @return  int list[].id - 备份ID
	 * @return  string list[].name - 备份名称
	 * @return  int list[].create_time - 创建时间
	 * @return  string list[].notes - 备注
	 * @return  int list[].status - 状态(0=创建中,1=创建成功)
	 * @return  int count - 总条数
	 */
	public function backup(){
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/backup', $RouteLogic->upstream_host_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->backup();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-11
	 * @title 创建备份
	 * @desc 创建备份
	 * @url /console/v1/remf_cloud/:id/backup
	 * @method  POST
	 * @author theworld
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int name - 备份名称 require
	 * @param   int disk_id - 磁盘ID require
	 */
	public function backupCreate()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/backup', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_create_backup_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $param['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_create_backup_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $param['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->backupCreate();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 备份还原
	 * @desc 备份还原
	 * @url /console/v1/remf_cloud/:id/backup/restore
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int backup_id - 备份ID require
	 * @return  string name - 备份名称
	 */
	public function backupRestore()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/backup/restore', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_start_backup_restore_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['backup_id'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_start_backup_restore_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['backup_id'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->backupRestore();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-06-27
	 * @title 删除备份
	 * @desc 删除备份
	 * @url /console/v1/remf_cloud/:id/backup/:backup_id
	 * @method  DELETE
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int backup_id - 备份ID require
	 * @return  string name - 备份名称
	 */
	public function backupDelete()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/backup/%d', $RouteLogic->upstream_host_id, $param['backup_id']), [], 'DELETE');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_host_delete_backup_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['backup_id'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_host_delete_backup_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'] ?? 'ID-'.(int)$param['backup_id'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->backupDelete();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-01
	 * @title 日志
	 * @desc 日志
	 * @url /console/v1/remf_cloud/:id/log
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
	 * @title 获取魔方云远程信息
	 * @desc 获取魔方云远程信息
	 * @url console/v1/remf_cloud/:id/remote_info
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  int rescue - 是否正在救援系统(0=不是,1=是)
	 * @return  string username - 远程用户名
	 * @return  string password - 远程密码
	 * @return  int port - 远程端口
	 * @return  int ip_num - IP数量
	 */
	public function remoteInfo()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/remote_info', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->remoteInfo();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-01
	 * @title 获取IP列表
	 * @desc 获取IP列表
	 * @url /console/v1/remf_cloud/:id/ip
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param int id - 产品ID
     * @param int page 1 页数
     * @param int limit - 每页条数
     * @return array list - 列表数据
     * @return string list[].ip - IP
     * @return string list[].subnet_mask - 掩码
     * @return string list[].gateway - 网关
     * @return int count - 总数
	 */
	public function ipList()
	{
		$param = array_merge(request()->param(), ['page' => request()->page, 'limit' => request()->limit, 'sort' => request()->sort]);

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/ip', $RouteLogic->upstream_host_id), $param, 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->ipList();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 获取订购磁盘价格
	 * @desc 获取订购磁盘价格
	 * @url /console/v1/remf_cloud/:id/disk/price
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   array remove_disk_id - 要取消订购的磁盘ID
	 * @param   array add_disk - 新增磁盘大小参数,如:[{"size":1,"type":"SSH"}]
	 * @param   int add_disk[].size - 磁盘大小
	 * @param   string add_disk[].type - 磁盘类型
	 * @return  string price - 价格
     * @return  string description - 生成的订单描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string base_price - 基础价格
     * @return  string price_client_level_discount - 价格等级折扣
     * @return  string price_difference_client_level_discount - 差价等级折扣
     * @return  string renew_price_difference_client_level_discount - 续费差价等级折扣
	 */
	public function calBuyDiskPrice()
	{
		$param = request()->param();

        $HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_disk')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		$hostId = $param['id'];

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/disk/price', $RouteLogic->upstream_host_id), $param, 'POST');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->calBuyDiskPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 生成购买磁盘订单
	 * @desc 生成购买磁盘订单
	 * @url /console/v1/remf_cloud/:id/disk/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   array remove_disk_id - 要取消订购的磁盘ID
	 * @param   array add_disk - 新增磁盘大小参数,如:[{"size":1,"type":"SSH"}]
	 * @param   int add_disk[].size - 磁盘大小
	 * @param   string add_disk[].type - 磁盘类型
	 * @return  string id - 订单ID
	 */
	public function createBuyDiskOrder()
	{
		$param = request()->param();

        $HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_disk')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }
		$hostId = $param['id'];

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/disk/price', $RouteLogic->upstream_host_id), $param, 'POST');
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
		                'type'       => 'buy_disk',
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createBuyDiskOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 获取磁盘扩容价格
	 * @desc 获取磁盘扩容价格
	 * @url /console/v1/remf_cloud/:id/disk/resize
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int resize_data_disk[].id - 魔方云磁盘ID
	 * @param   int resize_data_disk[].size - 磁盘大小
	 * @return  string price - 价格
     * @return  string description - 生成的订单描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string base_price - 基础价格
     * @return  string price_client_level_discount - 价格等级折扣
     * @return  string price_difference_client_level_discount - 差价等级折扣
     * @return  string renew_price_difference_client_level_discount - 续费差价等级折扣
	 */
	public function calResizeDiskPrice()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('resize_disk')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }
        $hostId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/disk/resize', $RouteLogic->upstream_host_id), $param, 'POST');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->calResizeDiskPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 生成磁盘扩容订单
	 * @desc 生成磁盘扩容订单
	 * @url /console/v1/remf_cloud/:id/disk/resize/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int resize_data_disk[].id - 魔方云磁盘ID
	 * @param   int resize_data_disk[].size - 磁盘大小
	 * @return  string id - 订单ID
	 */
	public function createResizeDiskOrder()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('resize_disk')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		$hostId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/disk/resize', $RouteLogic->upstream_host_id), $param, 'POST');
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
		                'type'       => 'resize_disk',
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createResizeDiskOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 检查产品是够购买过镜像
	 * @desc 检查产品是够购买过镜像
	 * @url /console/v1/remf_cloud/:id/image/check
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

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/image/check', $RouteLogic->upstream_host_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->checkHostImage();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 生成购买镜像订单
	 * @desc 生成购买镜像订单
	 * @url /console/v1/remf_cloud/:id/image/order
	 * @method POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @return  string data.id - 订单ID
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

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/image/check', $RouteLogic->upstream_host_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createImageOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 获取快照/备份数量升降级价格
	 * @desc 获取快照/备份数量升降级价格
	 * @url /console/v1/remf_cloud/:id/backup_config
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param   int id - 产品ID require
     * @param   string type - 类型(snap=快照,backup=备份) require
     * @param   int num - 数量 require
     * @return  string price - 价格
     * @return  string description - 描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string base_price - 基础价格
     * @return  string price_client_level_discount - 价格等级折扣
     * @return  string price_difference_client_level_discount - 差价等级折扣
     * @return  string renew_price_difference_client_level_discount - 续费差价等级折扣
	 */
	public function calBackupConfigPrice()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_backup')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }
        $hostId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/backup_config', $RouteLogic->upstream_host_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->calBackupConfigPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 生成快照/备份数量升降级订单
	 * @desc 生成快照/备份数量升降级订单
	 * @url /console/v1/remf_cloud/:id/backup_config/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   string type - 类型(snap=快照,backup=备份) require
     * @param   int num - 数量 require
	 * @return  string id - 订单ID
	 */
	public function createBackupConfigOrder()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_backup')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

    	$hostId = $param['id'];
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/backup_config', $RouteLogic->upstream_host_id), $param, 'GET');
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
		                'type'       => 'upgrade_backup',
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createBackupConfigOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 获取附加IP价格
	 * @desc 获取附加IP价格
	 * @url /console/v1/remf_cloud/:id/ip_num
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int ip_num - 附加IP数量 require
     * @return  string price - 价格
     * @return  string description - 生成的订单描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string base_price - 基础价格
     * @return  string price_client_level_discount - 价格等级折扣
     * @return  string price_difference_client_level_discount - 差价等级折扣
     * @return  string renew_price_difference_client_level_discount - 续费差价等级折扣
	 */
	public function calIpNumPrice()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_ip')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

		$hostId = $param['id'];

 		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/ip_num', $RouteLogic->upstream_host_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->calIpNumPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 生成附加IP订单
	 * @desc 生成附加IP订单
	 * @url /console/v1/remf_cloud/:id/ip_num/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int ip_num - 附加IP数量 require
	 * @return  string id - 订单ID
	 */
	public function createIpNumOrder()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('buy_ip')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

        $hostId = $param['id'];
 		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/ip_num', $RouteLogic->upstream_host_id), $param, 'GET');
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
		                'type'       => 'buy_ip',
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createIpNumOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 创建VPC网络
	 * @desc 创建VPC网络
	 * @url /console/v1/remf_cloud/:id/vpc_network
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string name - VPC网络名称 require
	 * @param   string ips - IP段(cidr,如10.0.0.0/16,系统分配时不传)
	 * @return  int id - VPC网络ID
	 */
	public function createVpcNetwork()
	{
		$param = request()->param();
		$param['create_sub_account'] = 1;

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('create_vpc')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }

        $hostId = $param['id'];
        try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vpc_network', $RouteLogic->upstream_host_id), $param, 'POST');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_add_vpc_network_success', [
					'{name}' => $param['name'],
					'{ips}'	 => isset($param['ips']) && !empty($param['ips']) ? $param['ips'] : '10.0.0.0/16',
				]);
				active_log($description, 'host', $hostId);
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createVpcNetwork();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception').$e->getMessage()];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title VPC网络列表
	 * @desc VPC网络列表
	 * @url /console/v1/remf_cloud/:id/vpc_network
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param   int id - 产品ID require
     * @param   int page - 页数
     * @param   int limit - 每页条数
     * @param   string orderby - 排序(id,name)
     * @param   string sort - 升降序(asc,desc)
     * @param   int downstream_client_id - 下游用户ID(api时可用)
     * @return  int list[].id - VPC网络ID
     * @return  string list[].name - VPC网络名称
     * @return  string list[].ips - VPC网络网段
     * @return  int count - 总条数
     * @return  int list[].host[].id - 主机产品ID
     * @return  string list[].host[].name - 主机标识
     * @return  array host - 可用产品ID(api时返回)
	 */
	public function vpcNetworkList()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vpc_network', $RouteLogic->upstream_host_id), $param, 'GET');
			if($result['status'] == 200){
				if(!empty($result['data']['host'])){
					$host = UpstreamHostModel::alias('uh')
							->field('h.id,uh.upstream_host_id,h.name')
							->join('host h', 'uh.host_id=h.id')
							->where('h.client_id', $HostModel['client_id'])
							->whereIn('uh.upstream_host_id', $result['data']['host'])
							->select()
							->toArray();

					$hostArr = [];
					$hostId  = [];
					foreach($host as $v){
						$hostArr[ $v['upstream_host_id'] ] = [
							'id' => $v['id'],
							'name' => $v['name']
						];
					}

					foreach($result['data']['list'] as $k=>$v){
						$host = [];
						foreach($v['host'] as $vv){
							if(isset($hostArr[ $vv['id'] ])){
								$host[] = $hostArr[ $vv['id'] ];
								$hostId = $hostArr[ $vv['id'] ]['id'];
							}
						}
						$result['data']['list'][$k]['host'] = $host;
					}
					$result['data']['host'] = $hostId;
				}
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->vpcNetworkList();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 修改VPC网络
	 * @desc 修改VPC网络
	 * @url /console/v1/remf_cloud/:id/vpc_network/:vpc_network_id
	 * @method  PUT
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   int vpc_network_id - VPC网络ID require
     * @param   string name - VPC网络名称 require
     * @param   int downstream_client_id - 下游用户ID(api时可用)
     * @return  string name - 原VPC名称
	 */
	public function vpcNetworkUpdate()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

        try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$vpcNetworkId = $param['vpc_network_id'];
			unset($param['id'], $param['vpc_network_id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vpc_network/%d', $RouteLogic->upstream_host_id, $vpcNetworkId), $param, 'PUT');

			if($result['status'] == 200 && $result['data']['name'] != $param['name']){
				$description = lang_plugins('res_mf_cloud_log_modify_vpc_network_success', [
					'{name}' => $result['data']['name'],
					'{new_name}' => $param['name']
				]);
				active_log($description, 'host', $HostModel['id']);
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->vpcNetworkUpdate();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-14
	 * @title 删除VPC网络
	 * @desc 删除VPC网络
	 * @url /console/v1/remf_cloud/:id/vpc_network/:vpc_network_id
	 * @method  DELETE
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int vpc_network_id - VPC网络ID require
	 * @param   int downstream_client_id - 下游用户ID(api时可用)
	 */
	public function vpcNetworkDelete()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vpc_network/%d', $RouteLogic->upstream_host_id, $param['vpc_network_id']), [], 'DELETE');

			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_delete_vpc_network_success', [
					'{name}' => $result['data']['name'],
					'{ips}' => $result['data']['ips'],
				]);
				active_log($description, 'host', $HostModel['id']);
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->vpcNetworkDelete();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-14
	 * @title 切换实例VPC网络
	 * @desc 切换实例VPC网络
	 * @url /console/v1/remf_cloud/:id/vpc_network
	 * @method  PUT
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int vpc_network_id - 新VPCID require
	 * @param   int downstream_client_id - 下游用户ID(api时可用)
	 * @return  string name - 变更后VPC网络名称
	 */
	public function changeVpcNetwork()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);
			$RouteLogic->setTimeout(180);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vpc_network', $RouteLogic->upstream_host_id), $param, 'PUT');

			if(isset($result['data']['name'])){
				if($result['status'] == 200){
					$description = lang_plugins('res_mf_cloud_log_change_vpc_network_success', [
						'{hostname}' => $HostModel['name'],
						'{name}' => $result['data']['name'],
					]);
				}else{
					$description = lang_plugins('res_mf_cloud_log_start_change_vpc_network_fail', [
						'{hostname}' => $HostModel['name'],
						'{name}' => $result['data']['name'],
					]);
				}
				active_log($description, 'host', $HostModel['id']);
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->changeVpcNetwork();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-14
	 * @title 获取cpu/内存使用信息
	 * @desc 获取cpu/内存使用信息
	 * @url /console/v1/remf_cloud/:id/real_data
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string cpu_usage - CPU使用率
	 * @return  string memory_total - 内存总量(‘-’代表获取不到)
	 * @return  string memory_usable - 已用内存(‘-’代表获取不到)
	 * @return  string memory_usage - 内存使用百分比(‘-1’代表获取不到)
	 */
	public function cloudRealData()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/real_data', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->cloudRealData();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 计算产品配置升级价格
	 * @desc 计算产品配置升级价格
	 * @url /console/v1/remf_cloud/:id/common_config
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   int cpu - 核心数 require
     * @param   int memory - 内存 require
     * @param   int bw - 带宽
     * @param   int flow - 流量
     * @param   int peak_defence - 防御峰值
     * @return  string price - 价格
     * @return  string description - 描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
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
        $CartValidate = new CartValidate();
		if (!$CartValidate->scene('upgrade_config')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($CartValidate->getError())]);
        }

        $hostId = $param['id'];
 		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/common_config', $RouteLogic->upstream_host_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->calCommonConfigPrice();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 生成产品配置升级订单
	 * @desc 生成产品配置升级订单
	 * @url /console/v1/remf_cloud/:id/common_config/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   int cpu - 核心数 require
     * @param   int memory - 内存 require
     * @param   int bw - 带宽
     * @param   int flow - 流量
     * @param   int peak_defence - 防御峰值
	 * @return  string id - 订单ID
	 */
	public function createCommonConfigOrder()
	{
		$param = request()->param();

		$HostValidate = new HostValidate();
		if (!$HostValidate->scene('auth')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($HostValidate->getError())]);
        }
        $CartValidate = new CartValidate();
		if (!$CartValidate->scene('upgrade_config')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($CartValidate->getError())]);
        }

        $hostId = $param['id'];
 		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/common_config', $RouteLogic->upstream_host_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createCommonConfigOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-14
	 * @title 获取线路配置
	 * @desc 获取线路配置
	 * @url /console/v1/product/:id/remf_cloud/line/:line_id
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   int line_id - 线路ID require
     * @return  string bill_type - 计费类型(bw=带宽计费,flow=流量计费)
     * @return  string gpu_name - 显卡名称
     * @return  string bw[].type - 配置方式(radio=单选,step=阶梯,total=总量)
     * @return  int bw[].value - 带宽
     * @return  int bw[].min_value - 最小值
     * @return  int bw[].max_value - 最大值
     * @return  int bw[].step - 步长
     * @return  int flow[].value - 流量
     * @return  int defence[].value - 防御峰值(G)
     * @return  int ip[].value - IP数量
     * @return  int gpu[].value - 显卡数量
	 */
	public function lineConfig()
	{
		$param = request()->param();

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByProduct($param['id']);

			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_cloud/line/%d', $RouteLogic->upstream_product_id, $param['line_id']), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->lineConfig();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-03-01
	 * @title 验证下单
	 * @desc 验证下单
	 * @url /console/v1/product/:id/remf_cloud/validate_settle
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   int downstream_client_id - 下游用户ID(api时可用)
	 * @param   int custom.duration_id - 周期ID require
     * @param   int custom.recommend_config_id - 套餐ID
     * @param   int custom.data_center_id - 数据中心ID
     * @param   int custom.cpu - CPU
     * @param   int custom.memory - 内存
     * @param   int custom.system_disk.size - 系统盘大小(G)
     * @param   string custom.system_disk.disk_type - 系统盘类型
     * @param   int custom.data_disk[].size - 数据盘大小(G)
     * @param   string custom.data_disk[].disk_type - 数据盘类型
     * @param   int custom.line_id - 线路ID
     * @param   int custom.bw - 带宽(Mbps)
     * @param   int custom.flow - 流量(G)
     * @param   int custom.peak_defence - 防御峰值(G)
     * @param   int custom.gpu_num - 显卡数量
     * @param   int custom.image_id - 镜像ID
     * @param   int custom.backup_num 0 备份数量
     * @param   int custom.snap_num 0 快照数量
     * @param   int custom.ip_mac_bind_enable 0 嵌套虚拟化(0=关闭,1=开启)
     * @param   int custom.ipv6_num_enable 0 是否使用IPv6(0=关闭,1=开启)
     * @param   int custom.nat_acl_limit_enable 0 是否启用NAT转发(0=关闭,1=开启)
     * @param   int custom.nat_web_limit_enable 0 是否启用NAT建站(0=关闭,1=开启)
     * @param   int custom.resource_package_id 0 资源包ID
     * @param   string custom.network_type - 网络类型(normal=经典网络,vpc=VPC网络)
     * @param   int custom.vpc.id - VPC网络ID
     * @param   string custom.vpc.ips - VPCIP段
     * @return  string price - 价格 
     * @return  string renew_price - 续费价格 
     * @return  string billing_cycle - 周期 
     * @return  int duration - 周期时长
     * @return  string description - 订单子项描述
     * @return  string base_price - 基础价格
     * @return  string billing_cycle_name - 周期名称多语言
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
			$result = $RouteLogic->curl( sprintf('console/v1/product/%d/remf_cloud/validate_settle', $RouteLogic->upstream_product_id), $param, 'POST');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->validateSettle();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-09-20
	 * @title NAT转发列表
	 * @desc NAT转发列表
	 * @url console/v1/remf_cloud/:id/nat_acl
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  int list[].id - 转发ID
	 * @return  string list[].name - 名称
	 * @return  string list[].ip - IP端口
	 * @return  int list[].int_port - 内部端口
	 * @return  int list[].protocol - 协议(1=tcp,2=udp,3=tcp+udp)
	 * @return  int count - 总条数
	 */
	public function natAclList()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/nat_acl', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->natAclList();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-09-20
	 * @title 创建NAT转发
	 * @desc 创建NAT转发
	 * @url console/v1/remf_cloud/:id/nat_acl
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   string name - 名称 require
     * @param   int int_port - 内部端口 require
     * @param   int protocol - 协议(1=tcp,2=udp,3=tcp+udp) require
	 */
	public function natAclCreate()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/nat_acl', $RouteLogic->upstream_host_id), $param, 'POST');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_nat_acl_create_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $param['name'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_nat_acl_create_fail', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $param['name'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->natAclCreate();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-09-20
	 * @title 删除NAT转发
	 * @desc 删除NAT转发
	 * @url console/v1/remf_cloud/:id/nat_acl
	 * @method  DELETE
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   int nat_acl_id - NAT转发ID require
	 */
	public function natAclDelete()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/nat_acl', $RouteLogic->upstream_host_id), $param, 'DELETE');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_nat_acl_delete_success', [
					'{hostname}' => $HostModel['name'],
					'{name}'	 => $result['data']['name'],
				]);
			}else{
				if(isset($result['data']['name'])){
					$description = lang_plugins('res_mf_cloud_log_nat_acl_delete_fail', [
						'{hostname}' => $HostModel['name'],
						'{name}'	 => $result['data']['name'],
					]);
				}
			}
			if(!empty($description)){
				active_log($description, 'host', $HostModel['id']);
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->natAclDelete();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-09-20
	 * @title NAT建站列表
	 * @desc NAT建站列表
	 * @url console/v1/remf_cloud/:id/nat_web
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  int list[].id - 建站ID
	 * @return  string list[].domain - 域名
	 * @return  int list[].ext_port - 外部端口
	 * @return  int list[].int_port - 内部端口
	 * @return  int count - 总条数
	 */
	public function natWebList()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/nat_web', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->natWebList();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-09-20
	 * @title 创建NAT建站
	 * @desc 创建NAT建站
	 * @url console/v1/mf_cloud/:id/nat_web
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   string domain - 域名 require
     * @param   int int_port - 内部端口 require
	 */
	public function natWebCreate()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/nat_web', $RouteLogic->upstream_host_id), $param, 'POST');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_nat_web_create_success', [
					'{hostname}' => $HostModel['name'],
					'{domain}'	 => $param['domain'],
				]);
			}else{
				$description = lang_plugins('res_mf_cloud_log_nat_web_create_fail', [
					'{hostname}' => $HostModel['name'],
					'{domain}'	 => $param['domain'],
				]);
			}
			active_log($description, 'host', $HostModel['id']);
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->natWebCreate();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-09-20
	 * @title 删除NAT建站
	 * @desc 删除NAT建站
	 * @url console/v1/mf_cloud/:id/nat_web
	 * @method  DELETE
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   int nat_web_id - NAT建站ID require
     * @return  string domain - 域名
	 */
	public function natWebDelete()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/nat_web', $RouteLogic->upstream_host_id), $param, 'DELETE');
			if($result['status'] == 200){
				$description = lang_plugins('res_mf_cloud_log_nat_web_delete_success', [
					'{hostname}' => $HostModel['name'],
					'{domain}'	 => $result['data']['domain'],
				]);
			}else{
				if(isset($result['data']['domain'])){
					$description = lang_plugins('res_mf_cloud_log_nat_web_delete_fail', [
						'{hostname}' => $HostModel['name'],
						'{domain}'	 => $result['data']['domain'],
					]);
				}
			}
			if(!empty($description)){
				active_log($description, 'host', $HostModel['id']);
			}
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->natWebDelete();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-10-26
	 * @title 获取可升降级套餐
	 * @desc 获取可升降级套餐
	 * @url console/v1/remf_cloud/:id/recommend_config
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  int list[].id - 套餐ID
     * @return  int list[].product_id - 商品ID
     * @return  string list[].name - 名称
     * @return  string list[].description - 描述
     * @return  int list[].order - 排序ID
     * @return  int list[].data_center_id - 数据中心ID
     * @return  int list[].cpu - CPU
     * @return  int list[].memory - 内存(GB)
     * @return  int list[].system_disk_size - 系统盘大小(GB)
     * @return  int list[].data_disk_size - 数据盘大小(GB)
     * @return  int list[].bw - 带宽(Mbps)
     * @return  int list[].peak_defence - 防御峰值(G)
     * @return  string list[].system_disk_type - 系统盘类型
     * @return  string list[].data_disk_type - 数据盘类型
     * @return  int list[].flow - 流量
     * @return  int list[].line_id - 线路ID
     * @return  int list[].create_time - 创建时间
     * @return  int list[].ip_num - IP数量
     * @return  int list[].upgrade_range - 升降级范围(0=不可升降级,1=全部,2=自选)
     * @return  int list[].hidden - 是否隐藏(0=否,1=是)
     * @return  int list[].gpu_num - 显卡数量
     * @return  string list[].gpu_name - 显卡名称
     * @return  int count - 总条数
	 */
	public function getUpgradeRecommendConfig()
	{
		$param = request()->param();

		$HostModel = HostModel::find($param['id']);
		if(empty($HostModel) || $HostModel['client_id'] != get_client_id() || $HostModel['is_delete']){
			return json(['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_not_found')]);
		}

		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['id']);

			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/recommend_config', $RouteLogic->upstream_host_id), [], 'GET');
		}catch(\Exception $e){
			if(!$RouteLogic->isUpstream){
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->getUpgradeRecommendConfig();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-10-25
	 * @title 计算升降级套餐价格
	 * @desc 计算升降级套餐价格
	 * @url console/v1/remf_cloud/:id/recommend_config/price
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int recommend_config_id - 套餐ID require
     * @return  string price - 价格
     * @return  string description - 描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string base_price - 基础价格
     * @return  string price_client_level_discount - 价格等级折扣
     * @return  string price_difference_client_level_discount - 差价等级折扣
     * @return  string renew_price_difference_client_level_discount - 续费差价等级折扣
	 */
	public function calUpgradeRecommendConfig()
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

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/recommend_config/price', $RouteLogic->upstream_host_id), $param, 'GET');
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->calUpgradeRecommendConfig();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-10-25
	 * @title 生成套餐升级订单
	 * @desc 生成套餐升级订单
	 * @url console/v1/remf_cloud/:id/recommend_config/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @param   int recommend_config_id - 套餐ID require
	 * @return  string id - 订单ID
	 */
	public function createUpgradeRecommendConfigOrder()
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

			// 标志为下游
			$param['is_downstream'] = 1;
			unset($param['id']);
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/recommend_config/price', $RouteLogic->upstream_host_id), $param, 'GET');
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
		                'type'       => 'upgrade_recommend_config',
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
				if(class_exists('\server\mf_cloud\controller\home\CloudController')){
					return (new \server\mf_cloud\controller\home\CloudController())->createUpgradeRecommendConfigOrder();
				}else{
					$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_error_act')];
				}
			}else{
				$result = ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_act_exception')];
			}
		}
		return json($result);
	}


}
