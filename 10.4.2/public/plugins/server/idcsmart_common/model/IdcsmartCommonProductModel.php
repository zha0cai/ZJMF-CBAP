<?php
namespace server\idcsmart_common\model;

use app\common\model\HostModel;
use app\common\model\OrderModel;
use app\common\model\ProductDurationRatioModel;
use app\common\model\ProductModel;
use app\common\model\MenuModel;
use app\common\model\ProductUpgradeProductModel;
use server\idcsmart_common\logic\IdcsmartCommonLogic;
use server\idcsmart_common\logic\ProvisionLogic;
use think\Db;
use think\db\Query;
use think\Model;
use app\common\model\SelfDefinedFieldModel;

class IdcsmartCommonProductModel extends Model
{
    protected $name = 'module_idcsmart_common_product';

    // 设置字段信息
    protected $schema = [
        'id'                     => 'int',
        'product_id'             => 'int',
        'order_page_description' => 'string',
        'allow_qty'              => 'int',
        'auto_support'           => 'int',
        'create_time'            => 'int',
        'update_time'            => 'int',
        'type'                   => 'string',
        'rel_id'                 => 'int',
        'config_option1'         => 'string',
        'config_option2'         => 'string',
        'config_option3'         => 'string',
        'config_option4'         => 'string',
        'config_option5'         => 'string',
        'config_option6'         => 'string',
        'config_option7'         => 'string',
        'config_option8'         => 'string',
        'config_option9'         => 'string',
        'config_option10'         => 'string',
        'config_option11'         => 'string',
        'config_option12'         => 'string',
        'config_option13'         => 'string',
        'config_option14'         => 'string',
        'config_option15'         => 'string',
        'config_option16'         => 'string',
        'config_option17'         => 'string',
        'config_option18'         => 'string',
        'config_option19'         => 'string',
        'config_option20'         => 'string',
        'config_option21'         => 'string',
        'config_option22'         => 'string',
        'config_option23'         => 'string',
        'config_option24'         => 'string'
    ];

    /**
     * 时间 2022-09-26
     * @title 商品基础信息
     * @desc 商品基础信息,插入默认价格信息
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @return array
     * @return string pay_type - 付款类型：付款类型(免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid
     * @return object common_product - 商品信息
     * @return int common_product - 商品信息
     * @return int common_product.product_id - 商品ID
     * @return string common_product.order_page_description - 订购页面html
     * @return int common_product.allow_qty - 是否允许选择数量:1是，0否
     * @return int common_product.auto_support - 是否自动化支持:1是，0否
     * @return  object pricing - 周期信息(注意显示)
     * @return  float pricing.onetime - 一次性,价格(当pay_type=='onetime'时,只显示此价格)
     * @return object custom_cycle - 自定义周期
     * @return int custom_cycle.id - 自定义周期ID
     * @return string custom_cycle.name - 名称
     * @return int custom_cycle.cycle_time - 时长
     * @return string custom_cycle.cycle_unit - 时长单位
     * @return float custom_cycle.amount - 金额
     */
    public function indexProduct($param)
    {
        $productId = $param['product_id']??0;

        $ProductModel = new ProductModel();

        $product = $ProductModel->find($productId); # pay_type

        $configoptionFields = "";
        for ($i=1;$i<=24;$i++){
            $configoptionFields .= ',config_option'.$i;
        }

        $commonProduct = $this->field('product_id,order_page_description,allow_qty,auto_support,type,rel_id server_id'.$configoptionFields)
            ->where('product_id',$productId)
            ->find();

        # 插入默认数据
        if (empty($commonProduct)){
            $this->insert([
                'product_id' => $productId,
                'order_page_description' => '',
                'allow_qty' => 0,
                'auto_support' => 0,
                'create_time' => time()
            ]);
            $commonProduct = $this->field('product_id,order_page_description,allow_qty,auto_support'.$configoptionFields)
                ->where('product_id',$productId)
                ->find();
        }

        if (!empty($commonProduct)){
            $commonProduct['order_page_description'] = htmlspecialchars_decode($commonProduct['order_page_description']);
        }

        # 一次性价格
        $IdcsmartCommonPricingModel = new IdcsmartCommonPricingModel();
        $pricing = $IdcsmartCommonPricingModel->where('type','product')
            ->where('rel_id',$productId)
            ->find();
        if (empty($pricing)){
            $IdcsmartCommonPricingModel->commonInsert([],$productId,'product');
        }

        $pricing = $IdcsmartCommonPricingModel
            ->withoutField('id,type,rel_id')
            ->where('type','product')
            ->where('rel_id',$productId)
            ->find();

        # 自定义周期及价格
        $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();
        $customCycle = $IdcsmartCommonCustomCycleModel->alias('cc')
            ->field('cc.id,cc.name,cc.cycle_time,cc.cycle_unit,ccp.amount,pdr.ratio')
            ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.custom_cycle_id=cc.id AND ccp.type=\'product\'')
            ->leftJoin('product_duration_ratio pdr','pdr.duration_id=cc.id AND pdr.product_id=cc.product_id')
            ->where('ccp.rel_id',$productId)
            ->select()
            ->toArray();
        # 自定义周期为空,预设月-三年的周期
        if (empty($customCycle) && !in_array($product['pay_type'],['onetime','free'])){
            $IdcsmartCommonCustomCycleModel->preSetCycle($productId);

            $customCycle = $IdcsmartCommonCustomCycleModel->alias('cc')
                ->field('cc.id,cc.name,cc.cycle_time,cc.cycle_unit,ccp.amount')
                ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.custom_cycle_id=cc.id AND ccp.type=\'product\'')
                ->where('ccp.rel_id',$productId)
                ->select()
                ->toArray();
        }

        $config_option = [];
        for ($i=1;$i<=24;$i++){
            $config_option['config_option'.$i] = $commonProduct['config_option'.$i];
        }

        $data = [
            'pay_type' => $product['pay_type'],
            'common_product' => $commonProduct,
            'pricing' => $pricing??[],
            'custom_cycle' => $customCycle??[],
            'config_option' => $config_option
        ];

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];

        return $result;
    }

    /**
     * 时间 2022-09-26
     * @title 保存商品基础信息
     * @desc 保存商品基础信息
     * @author wyh
     * @version v1
     * @param int product_id - 商品ID require
     * @param string order_page_description - 订购页描述
     * @param int allow_qty - 是否允许选择数量:1是，0否默认
     * @param int auto_support - 自动化支持:开启后所有配置选项都可输入参数
     * @param object pricing - 周期价格,格式:{"onetime":0.1}
     * @param float pricing.onetime - 一次性价格
     * @param array configoption - 自定义配置值数组
     */
    public function createProduct($param)
    {
        $this->startTrans();

        try{
            $productId = $param['product_id']??0;

            $commonProduct = $this->where('product_id',$productId)->find();

            // 保存自定义配置
            $configoptionData = [];
            if (isset($param['configoption']) && is_array($param['configoption'])){
                foreach ($param['configoption'] as $key=>$value){
                    $configoptionData['config_option'.($key+1)] = $value;
                }
            }

            if (!empty($commonProduct)){
                $data = [
                    'order_page_description' => htmlspecialchars($param['order_page_description']),
                    'allow_qty' => intval($param['allow_qty']),
                    'auto_support' => intval($param['auto_support']),
                    'type' => 'server',
                    'rel_id' => $param['server_id']??0,
                    'update_time' => time()
                ];
                $data = array_merge($data,$configoptionData);
                $commonProduct->save($data);
            }else{
                $data = [
                    'product_id' => $productId,
                    'order_page_description' => htmlspecialchars($param['order_page_description']),
                    'allow_qty' => intval($param['allow_qty']),
                    'auto_support' => intval($param['auto_support']),
                    'type' => 'server',
                    'rel_id' => $param['server_id']??0,
                    'create_time' => time()
                ];
                $data = array_merge($data,$configoptionData);
                $this->insert($data);
            }

            $IdcsmartCommonPricingModel = new IdcsmartCommonPricingModel();

            $IdcsmartCommonPricingModel->commonInsert($param['pricing']??[],$productId);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        return ['status'=>200,'msg'=>lang_plugins('success_message')];
    }

    /**
     * 时间 2023-12-18
     * @title 获取周期比例
     * @desc 获取周期比例
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @return  int list[].id - 周期ID
     * @return  string list[].name - 周期名称
     * @return  int list[].num - 周期时长
     * @return  string list[].unit - 单位(hour=小时,day=天,month=月)
     * @return  string list[].ratio - 比例
     */
    public function indexRatio($product_id){
        try{
            $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();
            $data = $IdcsmartCommonCustomCycleModel->alias('d')
                ->field('d.id,d.name,d.cycle_time as num,d.cycle_unit as unit,pdr.ratio')
                ->leftJoin('product_duration_ratio pdr', 'd.id=pdr.duration_id AND pdr.product_id='.$product_id)
                ->where('d.product_id', $product_id)
                ->orderRaw('field(d.cycle_unit, "hour","day","month")')
                ->order('d.cycle_time', 'asc')
                ->withAttr('ratio', function($val){
                    return $val ?? '';
                })
                ->group('d.id')
                ->select()
                ->toArray();
        }catch(\Exception $e){
            $data = [];
        }
        return $data;
    }

    /**
     * 时间 2023-12-18
     * @title 保存周期比例
     * @desc  保存周期比例
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @param   object ratio - 比例(如{"2":"1.5"},键是周期ID,值是比例) require
     */
    public function saveRatio($param){
        $productId = $param['product_id'] ?? 0;
        $product = ProductModel::find($productId);
        if(empty($product)){
            return ['status'=>400, 'msg'=>lang('product_is_not_exist') ];
        }
        $old = $this->indexRatio($productId);

        $data = [];
        $detail = '';
        foreach($old as $v){
            if(isset($param['ratio'][$v['id']]) && $param['ratio'][$v['id']] > 0){
                $data[] = [
                    'product_id'    => $productId,
                    'duration_id'   => $v['id'],
                    'ratio'         => $param['ratio'][$v['id']],
                ];
                if($v['ratio'] != $param['ratio'][$v['id']]){
                    $detail .= lang('log_product_duration_ratio_change', [
                        '{name}' => $v['name'],
                        '{old}'  => $v['ratio'] ?? lang('null'),
                        '{new}'  => $param['ratio'][$v['id']],
                    ]);
                }
            }
        }
        if(empty($data) || count($old) != count($data)){
            return ['status'=>400, 'msg'=>lang('please_input_all_duration_ratio')];
        }

        $ProductDurationRatioModel = new ProductDurationRatioModel();

        $ProductDurationRatioModel->startTrans();
        try{
            $ProductDurationRatioModel->where('product_id', $param['product_id'])->delete();
            $ProductDurationRatioModel->insertAll($data);

            $ProductDurationRatioModel->commit();
        }catch(\Exception $e){
            $ProductDurationRatioModel->rollback();

            $result = [
                'status' => 400,
                'msg'    => $e->getMessage(),
            ];
            return $result;
        }

        if(!empty($detail)){
            $description = lang('log_save_product_duration_ratio', [
                '{product}' => 'product#'.$productId.'#'.$product['name'].'#',
                '{detail}'  => $detail,
            ]);
            active_log($description, 'product', $productId);
        }

        $result = [
            'status' => 200,
            'msg'    => lang('save_success'),
        ];
        return $result;
    }

    /**
     * 时间 2023-12-18
     * @title 计算自动填充
     * @desc  计算自动填充
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @param   object price - 价格(如{"2":"1.5"},键是周期ID,值是价格) require
     */
    public function autoFill($param){
        bcscale(2);

        $productId = $param['product_id'] ?? 0;
        $product = ProductModel::find($productId);
        if(empty($product)){
            return ['status'=>400, 'msg'=>lang('product_is_not_exist')];
        }
        $data = $this->indexRatio($productId);
        if(empty($data)){
            return ['status'=>400, 'msg'=>lang('please_set_duration_ratio_first')];
        }
        $baseDuration = null;

        $res = [];
        foreach($data as $k=>$v){
            // 最小的周期作为基准
            if(isset($param['price'][$v['id']]) && $param['price'][$v['id']] > 0){
                $baseDuration = $v;
                $baseDuration['price'] = $param['price'][$v['id']];
                break;
            }
        }
        if(is_null($baseDuration)){
            return ['status'=>400, 'msg'=>lang('please_set_at_lease_one_price')];
        }
        foreach($data as $v){
            if(empty($v['ratio'])){
                return ['status'=>400, 'msg'=>lang('please_set_duration_ratio_first')];
            }
            if(!isset($res[$v['id']])){
                if($v['id'] == $baseDuration['id']){
                    $res[$v['id']] = amount_format($baseDuration['price']);
                }else{
                    $res[$v['id']] = amount_format(bcdiv(bcmul($baseDuration['price'], $v['ratio']), $baseDuration['ratio']));
                }
            }
        }

        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => [
                'list'  => $res,
            ],
        ];
        return $result;
    }

    /**
     * 时间 2022-09-26
     * @title 获取自定义周期详情
     * @desc 获取自定义周期详情
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @param   int id - 自定义字段ID require
     * @return object custom_cycle
     * @return string custom_cycle.name - 名称
     * @return string custom_cycle.cycle_time - 周期时长
     * @return string custom_cycle.cycle_unit - 周期单位:day天,month月
     * @return string custom_cycle.amout - 金额
     */
    public function customCycle($param)
    {
        $productId = $param['product_id']??0;

        $id = $param['id']??0;

        $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();

        $customCycle = $IdcsmartCommonCustomCycleModel->where('product_id',$productId)->where('id',$id)->find();
        if (empty($customCycle)){
            return ['status'=>400,'msg'=>lang_plugins('idcsmart_common_custom_cycle_not_exist')];
        }

        $IdcsmartCommonCustomCyclePricingModel = new IdcsmartCommonCustomCyclePricingModel();
        $customCyclePricing = $IdcsmartCommonCustomCyclePricingModel->where('custom_cycle_id',$id)
            ->where('type','product')
            ->where('rel_id',$productId)
            ->find();

        $customCycle['amount'] = $customCyclePricing['amount']??0;

        return ['status'=>200,'msg'=>lang_plugins('success_message'),'data'=>['custom_cycle'=>$customCycle??(object)[]]];
    }

    /**
     * 时间 2022-09-26
     * @title 添加自定义周期
     * @desc 添加自定义周期
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @param   int name - 名称 require
     * @param   int cycle_time - 周期时长 require
     * @param   int cycle_unit - 周期单位:day天,month月 require
     */
    public function createCustomCycle($param)
    {
        $this->startTrans();

        try{
            $productId = $param['product_id']??0;

            $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();
            $customCycleId = $IdcsmartCommonCustomCycleModel->insertGetId([
                'product_id' => $productId,
                'name' => $param['name']??'',
                'cycle_time' => $param['cycle_time']??0,
                'cycle_unit' => $param['cycle_unit']??'',
                'create_time' => time(),
            ]);

            $IdcsmartCommonCustomCyclePricingModel = new IdcsmartCommonCustomCyclePricingModel();
            $IdcsmartCommonCustomCyclePricingModel->insert([
                'custom_cycle_id' => $customCycleId,
                'rel_id' => $productId,
                'type' => 'product',
                'amount' => $param['amount']??0,
            ]);

            # 默认增加配置子项价格
            $IdcsmartCommonProductConfigoptionModel = new IdcsmartCommonProductConfigoptionModel();

            $configoptionsId = $IdcsmartCommonProductConfigoptionModel->where('product_id',$productId)->column('id');

            $IdcsmartCommonProductConfigoptionSubModel = new IdcsmartCommonProductConfigoptionSubModel();

            $configoptionSubs = $IdcsmartCommonProductConfigoptionSubModel->whereIn('product_configoption_id',$configoptionsId)
                ->select()
                ->toArray();
            $customCyclePricingArray = [];
            foreach ($configoptionSubs as $configoptionSub){
                $customCyclePricingArray[] = [
                    'custom_cycle_id' => $customCycleId,
                    'rel_id' => $configoptionSub['id'],
                    'type' => 'configoption',
                    'amount' => 0,
                ];
            }
            $IdcsmartCommonCustomCyclePricingModel->insertAll($customCyclePricingArray);

            # 更新商品最低价格
            $this->updateProductMinPrice($productId);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();

            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        return ['status'=>200,'msg'=>lang_plugins('success_message')];
    }

    /**
     * 时间 2022-09-26
     * @title 修改自定义周期
     * @desc 修改自定义周期
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @param   int id - 自定义周期ID require
     * @param   int product_id - 商品ID require
     * @param   string name - 名称 require
     * @param   int cycle_time - 周期时长 require
     * @param   string cycle_unit - 周期单位:day天,month月 require
     * @param   float amout - 金额 require
     */
    public function updateCustomCycle($param)
    {
        $this->startTrans();

        try{
            $productId = $param['product_id']??0;

            $id = $param['id']??0;

            $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();

            $customCycle = $IdcsmartCommonCustomCycleModel->where('product_id',$productId)->where('id',$id)->find();
            if (empty($customCycle)){
                throw new \Exception(lang_plugins('idcsmart_common_custom_cycle_not_exist'));
            }

            $customCycle->save([
                'product_id' => $productId,
                'name' => $param['name']??'',
                'cycle_time' => $param['cycle_time']??0,
                'cycle_unit' => $param['cycle_unit']??'',
                'update_time' => time(),
            ]);

            $IdcsmartCommonCustomCyclePricingModel = new IdcsmartCommonCustomCyclePricingModel();
            $customCyclePricing = $IdcsmartCommonCustomCyclePricingModel->where('custom_cycle_id',$id)
                ->where('type','product')
                ->where('rel_id',$productId)
                ->find();
            if (!empty($customCyclePricing)){
                $customCyclePricing->save([
                    'amount' => $param['amount']??0,
                ]);
            }else{
                $IdcsmartCommonCustomCyclePricingModel->insert([
                    'custom_cycle_id' => $id,
                    'rel_id' => $productId,
                    'type' => 'product',
                    'amount' => $param['amount']??0,
                ]);
            }

            # 更新商品最低价格
            $this->updateProductMinPrice($productId);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();

            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        return ['status'=>200,'msg'=>lang_plugins('success_message')];
    }

    /**
     * 时间 2022-09-26
     * @title 删除自定义周期
     * @desc 删除自定义周期
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @param   int id - 自定义周期ID require
     */
    public function deleteCustomCycle($param)
    {
        $this->startTrans();

        try{
            $productId = $param['product_id']??0;

            $id = $param['id']??0;

            $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();

            $customCycle = $IdcsmartCommonCustomCycleModel->where('product_id',$productId)->where('id',$id)->find();
            if (empty($customCycle)){
                throw new \Exception(lang_plugins('idcsmart_common_custom_cycle_not_exist'));
            }

            $customCycle->delete();

            $IdcsmartCommonCustomCyclePricingModel = new IdcsmartCommonCustomCyclePricingModel();
            $IdcsmartCommonCustomCyclePricingModel->where('custom_cycle_id',$id)
                ->where('type','product')
                ->where('rel_id',$productId)
                ->delete();

            # 删除配置子项价格
            $IdcsmartCommonProductConfigoptionModel = new IdcsmartCommonProductConfigoptionModel();
            $configoptionSubsId = $IdcsmartCommonProductConfigoptionModel->alias('pc')
                ->leftJoin('module_idcsmart_common_product_configoption_sub pcs','pcs.product_configoption_id=pc.id')
                ->where('pc.product_id',$productId)
                ->column('pcs.id');
            $IdcsmartCommonCustomCyclePricingModel->where('custom_cycle_id',$id)
                ->whereIn('rel_id',$configoptionSubsId)
                ->where('type','configoption')
                ->delete();

            # 更新商品最低价格
            $this->updateProductMinPrice($productId);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();

            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        return ['status'=>200,'msg'=>lang_plugins('success_message')];
    }

    /**
     * 时间 2022-09-26
     * @title 前台商品配置信息
     * @desc 前台商品配置信息
     * @url /console/v1/idcsmart_common/product/:product_id/configoption
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @return  object common_product - 商品基础信息
     * @return  string common_product.name - 商品名称
     * @return  string common_product.order_page_description - 订购页面html
     * @return  string common_product.allow_qty - 是否允许选择数量:1是，0否默认
     * @return  string common_product.pay_type - 付款类型(免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid
     * @return  object configoptions - 配置项信息
     * @return  int configoptions.id - 配置项ID
     * @return  int configoptions.option_name - 配置项名称
     * @return  int configoptions.option_type -  配置项类型：select下拉单选，multi_select下拉多选，radio点击单选，quantity数量输入，quantity_range数量拖动，yes_no是否，area区域
     * @return  int configoptions.qty_min - 数量时最小值
     * @return  int configoptions.qty_max - 数量时最大值
     * @return  int configoptions.unit - 单位
     * @return  int configoptions.allow_repeat - 数量类型时：是否允许重复:开启后,前台购买时，可通过点击添加按钮，自动创建一个新的配置项，取名如bw1
     * @return  int configoptions.max_repeat - 最大允许重复数量
     * @return  int configoptions.description - 说明
     * @return array configoptions.subs - 子项信息
     * @return  float configoptions.subs.id - 子项ID
     * @return  float configoptions.subs.option_name - 子项名称
     * @return object cycles - 周期({"onetime":1.00})
     * @return object custom_cycles - 自定义周期
     * @return int custom_cycles.id - 自定义周期ID
     * @return string custom_cycles.name - 自定义周期名称
     * @return int custom_cycles.cycle_time - 自定义周期时长
     * @return string custom_cycles.cycle_unit - 自定义周期单位
     * @return int custom_cycles.cycle_amount - 自定义周期金额
     */
    public function cartConfigoption($param)
    {
        $productId = $param['product_id']??0;

        $commonProduct = $this->alias('cp')
            ->field('p.name,cp.order_page_description,cp.allow_qty,p.pay_type,p.product_id')
            ->leftJoin('product p','p.id=cp.product_id')
            ->where('cp.product_id',$productId)
            ->withAttr('name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->withAttr('order_page_description', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->find();

        $IdcsmartCommonPricingModel = new IdcsmartCommonPricingModel();
        $pricing = $IdcsmartCommonPricingModel->where('type','product')
            ->where('rel_id',$productId)
            ->find();

        $IdcsmartCommonLogic = new IdcsmartCommonLogic();
        $systemCycles = array_keys($IdcsmartCommonLogic->systemCycles);

        $IdcsmartCommonProductConfigoptionModel = new IdcsmartCommonProductConfigoptionModel();
        $IdcsmartCommonProductConfigoptionSubModel = new IdcsmartCommonProductConfigoptionSubModel();
        $configoptions = $IdcsmartCommonProductConfigoptionModel->field('id,product_id,option_name,option_type,qty_min,qty_max,unit,allow_repeat,max_repeat,description,configoption_id')
            ->where('product_id',$productId)
            ->where('hidden',0)
            ->withAttr('option_name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->withAttr('unit', function($value){
                if(!empty($value)){
                    $multiLanguage = hook_one('multi_language', [
                        'replace' => [
                            'name' => $value,
                        ],
                    ]);
                    if(isset($multiLanguage['name'])){
                        $value = $multiLanguage['name'];
                    }
                }
                return $value;
            })
            ->order('order','asc') # 升序
            ->order('id','asc')
            ->select()
            ->toArray();
        # 配置子项价格(取第一个)
        $minSubPricings = [];
        foreach ($configoptions as &$configoption){
            $subs = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                ->field('pcs.id,pcs.option_name,pcs.qty_min,pcs.qty_max,pcs.country,pc.option_type,pc.fee_type,pcs.product_configoption_id,pcs.qty_change')
                ->leftJoin('module_idcsmart_common_product_configoption pc','pc.id=pcs.product_configoption_id')
                ->leftJoin('module_idcsmart_common_pricing p','p.rel_id=pcs.id AND p.type=\'configoption\'')
                ->where('pcs.product_configoption_id',$configoption['id'])
                ->where('pcs.hidden',0)
                ->withAttr('option_name', function($value){
                    $multiLanguage = hook_one('multi_language', [
                        'replace' => [
                            'name' => $value,
                        ],
                    ]);
                    if(isset($multiLanguage['name'])){
                        $value = $multiLanguage['name'];
                    }
                    return $value;
                })
                ->order('pcs.order','asc')
                ->order('pcs.id','asc')
                ->select()
                ->toArray();
            // 处理操作系统
            if ($configoption['option_type']=='os'){
                $osArray = [];
                foreach ($subs as $sub){
                    $optionNameArray = explode("^",$sub['option_name']);
                    if (count($optionNameArray)>=2){
                        $sub['option_name'] = $optionNameArray[1];
                        $osArray[$optionNameArray[0]][] = $sub;
                    }
                }
                $osArrayFilter = [];
                foreach ($osArray as $k=>$value){
                    $osArrayFilter[] = [
                        'os' => $k,
                        'version' => $value
                    ];
                }
                $configoption['subs'] = $osArrayFilter;
            }else{
                $configoption['subs'] = $subs??[];
            }

            if (!empty($subs[0])){
                $minSubPricings[] = $subs[0];
            }
        }

        $cycles = [];
        foreach ($systemCycles as $systemCycle){
            if ($pricing[$systemCycle]<0){
                unset($pricing[$systemCycle]);
            }else{
                $cycleFee = $pricing[$systemCycle]??0;

                foreach ($minSubPricings as $minSubPricing){
                    $cycleFee = bcadd($cycleFee,$minSubPricing[$systemCycle]??0,2);
                }

                $cycles[$systemCycle] = $cycleFee;
            }
        }

        # 自定义周期及价格
        $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();
        $customCycles = $IdcsmartCommonCustomCycleModel->alias('cc')
            ->field('cc.id,cc.name,cc.cycle_time,cc.cycle_unit,ccp.amount')
            ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.custom_cycle_id=cc.id AND ccp.type=\'product\'')
            ->where('cc.product_id',$productId)
            ->where('ccp.rel_id',$productId)
            ->where('ccp.amount','>=',0) # 可显示出得周期
            ->withAttr('name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->select()
            ->toArray();
        $IdcsmartCommonCustomCyclePricingModel = new IdcsmartCommonCustomCyclePricingModel();
        foreach ($customCycles as $key=>$customCycle){
            $customCycleAmount = $customCycle['amount']??0;

            # 配置子项的自定义价格
            foreach ($minSubPricings as $minSubPricing){
                if ($IdcsmartCommonLogic->checkQuantity($minSubPricing['option_type'])){
                    # 阶梯计费
                    if ($minSubPricing['fee_type'] == 'stage'){
                        $amount = $IdcsmartCommonLogic->quantityStagePrice($minSubPricing['product_configoption_id'],$minSubPricing['qty_min'],$customCycle['id'],0,true);
                    }else{ # 数量计费
                        $amount = $IdcsmartCommonCustomCyclePricingModel->where('custom_cycle_id',$customCycle['id'])
                            ->where('rel_id',$minSubPricing['id'])
                            ->where('type','configoption')
                            ->value('amount');
                        $amount = $amount * $minSubPricing['qty_min'];
                    }

                }else{
                    $amount = $IdcsmartCommonCustomCyclePricingModel->where('custom_cycle_id',$customCycle['id'])
                        ->where('rel_id',$minSubPricing['id'])
                        ->where('type','configoption')
                        ->value('amount');
                }
                $customCycleAmount = bcadd($customCycleAmount,$amount??0);
            }
            $customCycles[$key]['cycle_amount'] = $customCycleAmount;
        }

        if (empty($commonProduct) || (!empty($commonProduct) && $commonProduct['pay_type'] == 'free')){
            $cycles = [];
            $cycles['free'] = 0;
        }

        $data = [
            'common_product' => $commonProduct??(object)[],
            'configoptions' => $configoptions??(object)[],
            'cycles' => $cycles??(object)[],
            'custom_cycles' => $customCycles??(object)[]
        ];

        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
    }

    /**
     * 时间 2022-09-26
     * @title 前台商品配置信息计算价格
     * @desc 前台商品配置信息计算价格
     * @author wyh
     * @version v1
     * @param   object configoption - 配置信息{168:1,514:53} require
     * @return object cycles - 周期({"onetime":1.00})
     * @return object custom_cycles - 自定义周期
     * @return int custom_cycles.id - 自定义周期ID
     * @return string custom_cycles.name - 自定义周期名称
     * @return int custom_cycles.cycle_time - 自定义周期时长
     * @return string custom_cycles.cycle_unit - 自定义周期单位
     * @return int custom_cycles.cycle_amount - 自定义周期金额
     */
    public function cartConfigoptionCalculate($param)
    {
        $param['configoption'] = $param['config_options']['configoption']??[];

        $productId = $param['product_id']??0;

        $IdcsmartCommonLogic = new IdcsmartCommonLogic();

        # 自定义周期及价格
        $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();
        $customCycles = $IdcsmartCommonCustomCycleModel->alias('cc')
            ->field('cc.id,cc.name,cc.cycle_time,cc.cycle_unit,ccp.amount')
            ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.custom_cycle_id=cc.id AND ccp.type=\'product\'')
            ->where('cc.product_id',$productId)
            ->where('ccp.rel_id',$productId)
            ->where('ccp.amount','>=',0) # 可显示出得周期
            ->withAttr('name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->select()
            ->toArray();
        foreach ($customCycles as &$customCycle){
            $param['cycle'] = $customCycle['id'];
            $result = $IdcsmartCommonLogic->cartCalculatePrice($param);
            $customCycle['cycle_amount'] = $result['data']['price']??bcsub(0,0,2);
        }

        $cycles = [];
        $systemCycles = array_keys($IdcsmartCommonLogic->systemCycles);
        foreach ($systemCycles as $systemCycle){
            $param['cycle'] = $systemCycle;
            $result = $IdcsmartCommonLogic->cartCalculatePrice($param);
            $cycles[$systemCycle] = $result['data']['price']??bcsub(0,0,2);
        }

        $data = [
            'custom_cycles' => $customCycles,
            'cycles' => $cycles
        ];

        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
    }

    /**
     * 时间 2022-09-26
     * @title 前台产品内页
     * @desc 前台产品内页
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @return  object host - 财务信息
     * @return  int host.create_time - 订购时间
     * @return  int host.due_time - 到期时间
     * @return  string host.billing_cycle - 计费方式:计费周期免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid
     * @return  string host.billing_cycle_name - 模块计费周期名称
     * @return  int host.billing_cycle_time - 模块计费周期时间,秒
     * @return  float host.renew_amount - 续费金额
     * @return  float host.first_payment_amount - 首付金额
     * @return  string host.dedicatedip - 独立ip
     * @return  string host.username - 用户名
     * @return  string host.password - 密码
     * @return  string host.os - 操作系统，后台未配置时显示远程操作系统模板ID
     * @return  string host.assignedips - 分配ip，逗号分隔
     * @return  int host.bwlimit - 流量限制
     * @return  float host.bwusage - 流量使用
     * @return  object configoptions - 配置项信息
     * @return  int configoptions.id - 配置项ID
     * @return  string configoptions.option_name - 配置项名称
     * @return  string configoptions.option_type - 配置项类型：select下拉单选，multi_select下拉多选，radio点击单选，quantity数量输入，quantity_range数量拖动，yes_no是否，area区域，os操作系统
     * @return  string configoptions.unit - 单位
     * @return  array configoptions.subs -
     * @return  string configoptions.subs.option_name - 子项名称
     * @return  int configoptions.qty - 数量(当类型为数量时,显示此值)
     * @return array chart - 图表tab
     * @return string chart[].title - 标题
     * @return string chart[].type - 类型
     * @return array chart[].select - 下拉选择
     * @return string chart[].select[].name - 名称
     * @return string chart[].select[].value - 值
     * @return array client_area - 客户自定义tab区域
     * @return string client_area[].key - 键
     * @return string client_area[].name - 名称标题
     * @return array client_button - 管理按钮区域(默认模块操作)
     * @return array client_button.console - 控制台
     * @return array client_button.console[].func - 模块(调模块动作传此值)
     * @return array client_button.console[].name - 模块名称
     * @return array client_button.console[].type - 类型
     * @return array client_button.control - 下拉管理
     * @return array client_button.control[].func - 模块(调模块动作传此值)
     * @return array client_button.control[].name - 模块名称
     * @return array client_button.control[].type - 类型
     * @return object os - 操作系统
     * @return int os.id - 配置项ID
     * @return int os.option_name - 配置项名称
     * @return int os.option_type - 配置项类型
     * @return array os.subs - 子项
     * @return string os.subs[].os - 操作系统
     * @return array os.subs[].version - 操作系统详细版本
     * @return int os.subs[].version[].id - 子项ID
     * @return int os.subs[].version[].option_name - 名称
     */
    public function hostConfigotpion($param)
    {
        $hostId = $param['host_id']??0;

        $where = [];
        $where[] = ['h.id', '=', $hostId];
        $where[] = ['h.is_delete', '=', 0];
        
        $HostModel = new HostModel();

        $host = $HostModel->alias('h')
            ->field('h.id,h.order_id,h.product_id,h.create_time,h.due_time,h.billing_cycle,h.billing_cycle_name,
            h.billing_cycle_time,h.renew_amount,h.first_payment_amount,p.name,h.status,h.name as host_name,h.product_id,
            h.client_notes,hl.dedicatedip,hl.assignedips,hl.username,hl.password,hl.bwlimit,hl.os,hl.bwusage')
            ->leftJoin('product p','p.id=h.product_id')
            ->leftJoin('module_idcsmart_common_server_host_link hl','h.id=hl.host_id')
            ->where($where)
            ->withAttr('name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->withAttr('password',function ($value){
                if (!empty($value)){
                    return password_decrypt($value);
                }
                return $value;
            })
            ->find();
        if (empty($host)){
            return ['status'=>400,'msg'=>lang_plugins('host_is_not_exist')];
        }
        $host['status'] = $host['status'] != 'Failed' ? $host['status'] : 'Pending';
        $IdcsmartCommonProductConfigoptionModel = new IdcsmartCommonProductConfigoptionModel();
        $configoptions = $IdcsmartCommonProductConfigoptionModel->alias('pc')
            ->field('pc.id,pc.option_name,pc.option_type,pc.unit,hc.qty,hc.repeat')
            ->leftJoin('module_idcsmart_common_host_configoption hc','hc.configoption_id=pc.id ')
            ->where('hc.host_id',$hostId)
            ->order("pc.order",'asc')
            ->withAttr('option_name',function ($value,$data){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                if ($data['repeat']>0){
                    return $value.$data['repeat'];
                }
                return $value;
            })
            ->withAttr('unit',function ($value){
                if(!empty($value)){
                    $multiLanguage = hook_one('multi_language', [
                        'replace' => [
                            'name' => $value,
                        ],
                    ]);
                    if(isset($multiLanguage['name'])){
                        $value = $multiLanguage['name'];
                    }
                }
                return $value;
            })
            ->select()
            ->toArray();
        $IdcsmartCommonProductConfigoptionSubModel = new IdcsmartCommonProductConfigoptionSubModel();

        $IdcsmartCommonLogic = new IdcsmartCommonLogic();

        $configoptionMultiSelect = $configoptionOther = [];

        foreach ($configoptions as $key=>$configoption){
            $subs = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                ->field('pcs.option_name,pcs.country')
                ->leftJoin('module_idcsmart_common_host_configoption hc','hc.configoption_sub_id=pcs.id')
                ->where('hc.host_id',$hostId)
                ->where('hc.configoption_id',$configoption['id'])
                ->withAttr('option_name', function($value){
                    $multiLanguage = hook_one('multi_language', [
                        'replace' => [
                            'name' => $value,
                        ],
                    ]);
                    if(isset($multiLanguage['name'])){
                        $value = $multiLanguage['name'];
                    }
                    return explode("^",$value)[1]??$value;
                })
                ->select()
                ->toArray();
            $configoption['subs'] = $subs??[];

            if ($IdcsmartCommonLogic->checkMultiSelect($configoption['option_type'])){
                $configoptionMultiSelect[$configoption['id']] = $configoption;
            }else{
                $configoptionOther[] = $configoption;
            }

        }

        $configoptionFilter = array_merge($configoptionOther,array_values($configoptionMultiSelect));

        // TODO 内页其他自定义数据
        $ProvisionLogic = new ProvisionLogic();
        $chart = $ProvisionLogic->chart($hostId);
        $clientArea = $ProvisionLogic->clientArea($hostId);
        $clientButtonOutput = $ProvisionLogic->clientButtonOutput($hostId);
        $os = $IdcsmartCommonProductConfigoptionModel->field('id,option_name,option_type')
            ->where('product_id',$host['product_id'])
            ->where('hidden',0)
            ->where('option_type','os')
            ->find();
        $osSubs = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
            ->field('pcs.id,pcs.option_name,pcs.option_param')
            ->leftJoin('module_idcsmart_common_product_configoption pc','pc.id=pcs.product_configoption_id')
            ->leftJoin('module_idcsmart_common_pricing p','p.rel_id=pcs.id AND p.type=\'configoption\'')
            ->where('pcs.product_configoption_id',$os['id'])
            ->where('pcs.hidden',0)
            ->withAttr('option_name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->order('pcs.order','asc')
            ->order('pcs.id','asc')
            ->select()
            ->toArray();
        $osArray = [];
        // 处理操作系统
        foreach ($osSubs as $sub){
            $optionNameArray = explode("^",$sub['option_name']);
            if (count($optionNameArray)>=2){
                $sub['option_name'] = $optionNameArray[1];
                $osArray[$optionNameArray[0]][] = $sub;
            }
        }
        $osArrayFilter = [];
        foreach ($osArray as $k=>$value){
            $osArrayFilter[] = [
                'os' => $k,
                'version' => $value
            ];
        }
        $os['subs'] = $osArrayFilter;

        $data = [
            'host' => $host,
            'configoptions' => $configoptionFilter??[],
            'chart' => $chart,
            'client_area' => $clientArea,
            'client_button' => $clientButtonOutput,
            'os' => $os??(object)[]
        ];

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];

        return $result;
    }

    /**
     * 时间 2023-11-21
     * @title 前台产品内页自定义页面输出
     * @desc 前台产品内页自定义页面输出
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   string key - snapshot快照等 require
     */
    public function clientAreaOutput($param)
    {
        $hostId = $param['host_id']??0;
        $ProvisionLogic = new ProvisionLogic();
        $html = $ProvisionLogic->clientAreaDetail($hostId,$param['key']??"");
        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'html' => $html
            ]
        ];
    }

    /**
     * 时间 2023-11-21
     * @title 前台产品内页图表页面
     * @desc 前台产品内页图表页面
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   array chart - 图表数据 require
     * @param   int chart[].start - 开始时间 require
     * @param   int chart[].type - 类型：cpu/disk/flow require
     * @param   array chart[].select[] - 传select下得value组合成的数组 require
     */
    public function chartData($param)
    {
        $hostId = $param['host_id']??0;
        $ProvisionLogic = new ProvisionLogic();
        $res = $ProvisionLogic->getChartData($hostId,$param['chart']);
        if ($res['status']=='success'){
            return [
                'status' => 200,
                'msg' => lang_plugins('success_message'),
                'data' => $res['data']
            ];
        }else{
            return [
                'status' => 400,
                'msg' => $res['msg']
            ];
        }
    }

    /**
     * 时间 2023-11-21
     * @title 执行子模块方法
     * @desc 执行子模块方法
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   string func - 模块方法:如on开机/off关机等 require
     */
    public function provisionFunc($param)
    {
        $hostId = $param['host_id']??0;

        $ProvisionLogic = new ProvisionLogic();

        $func = $param['func'];

        // 特殊处理两个方法
        switch ($func){
            case "crack_pass":
                $password = $param['password']??"";
                $result = $ProvisionLogic->crackPassword($hostId,$password);
                if ($result['status']=='success' || $result['status']==200){
                    $IdcsmartCommonServerHostLinkModel = new IdcsmartCommonServerHostLinkModel();
                    $IdcsmartCommonServerHostLinkModel->where('host_id',$hostId)->update(['password'=>password_encrypt($password)]);
                }
                break;
            case "reinstall":
                $os = $param['os']??0;
                $port = $param['os_name']??"";
                $subId = $param['sub_id']??0;
                $optionId = $param['option_id']??0;
                $result = $ProvisionLogic->reinstall($hostId,$os,$port,$subId,$optionId);
                if ($result['status']=='success' || $result['status']==200){
                    // 修改关联
                    if ($subId){
                        $IdcsmartCommonHostConfigoptionModel = new IdcsmartCommonHostConfigoptionModel();
                        $IdcsmartCommonHostConfigoptionModel->where('host_id',$hostId)->where('configoption_id',$optionId)
                            ->update(['configoption_sub_id'=>$subId]);
                    }
                }
                break;
            default:
                $funcJava = parse_name($func,1,false);
                $result = $ProvisionLogic->$funcJava($hostId);
                break;
        }

        if ($result['status']=='success' || $result['status']==200){
            $result['status'] = 200;
            $result['msg'] = lang_plugins('success_message');
            $description = lang_plugins('log_idcsmart_common_success_'.$func, [
                '{host_id}' => $hostId,
            ]);
            active_log($description, 'host', $hostId);
        }else{
            $result['status'] = 400;
            $description = lang_plugins('log_idcsmart_common_fail_'.$func, [
                '{host_id}' => $hostId,
                '{fail}' => $result['msg']
            ]);
            active_log($description, 'host', $hostId);
        }

        return $result;
    }

    /**
     * 时间 2023-11-21
     * @title 执行子模块自定义方法
     * @desc 执行子模块自定义方法
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   string func - 模块方法:如on开机/off关机等 require
     * @param   array custom_fields - 自定义字段
     */
    public function provisionFuncCustom($param)
    {
        $hostId = $param['host_id']??0;

        $ProvisionLogic = new ProvisionLogic();

        $func = $param['func'];

        $result = $ProvisionLogic->execCustomFunc($func,$hostId,'client',$param['custom_fields']??[]);

        if ($result['status']=='success'){
            $result['status'] = 200;
            $result['msg'] = lang_plugins("success_message");
        }else{
            $result['status'] = 400;
        }

        return $result;
    }

    /**
     * 时间 2022-09-28
     * @title 产品列表
     * @desc 产品列表
     * @author wyh
     * @version v1
     * @param int m - 菜单ID
     * @param int client_id - 客户ID
     * @param string keywords - 关键字,搜索范围:产品ID,商品名称,标识
     * @param string status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id,active_time,due_time
     * @param string sort - 升/降序 asc,desc
     * @return array list - 产品
     * @return int list[].id - 产品ID
     * @return int list[].product_id - 商品ID
     * @return string list[].product_name - 商品名称
     * @return string list[].name - 标识
     * @return int list[].active_time - 开通时间
     * @return int list[].due_time - 到期时间
     * @return string list[].first_payment_amount - 金额
     * @return string list[].billing_cycle - 周期
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return object list[].self_defined_field - 产品自定义字段自定义字段，格式{"自定义字段ID":"值"}
     * @return int count - 产品总数
     */
    public function hostList($param)
    {
        $param['m'] = $param['m'] ?? 0;
        $param['client_id'] = get_client_id();
        $param['keywords'] = $param['keywords'] ?? '';
        $param['status'] = $param['status'] ?? '';
        $param['orderby'] = isset($param['orderby']) && in_array($param['orderby'], ['id', 'client_id', 'product_name', 'name', 'active_time', 'due_time', 'first_payment_amount', 'status']) ? $param['orderby'] : 'id';
        if($param['orderby']=='product_name'){
            $param['orderby'] = 'p.name';
        }else{
            $param['orderby'] = 'h.'.$param['orderby'];
        }

        $menu = MenuModel::find($param['m']);
        if(!empty($menu)){
            $param['product_id'] = json_decode($menu['product_id'], true);
        }else{
            $param['product_id'] = [];
        }

        // 获取子账户可见产品
        $res = hook('get_client_host_id', ['client_id' => get_client_id(false)]);
        $res = array_values(array_filter($res ?? []));
        foreach ($res as $key => $value) {
            if(isset($value['status']) && $value['status']==200){
                $hostId = $value['data']['host'];
            }
        }
        $param['host_id'] = $hostId ?? [];

        $where = function (Query $query) use($param) {
            if(!empty($param['host_id'])){
                $query->whereIn('h.id', $param['host_id']);
            }
            if(!empty($param['product_id'])){
                $query->whereIn('h.product_id', $param['product_id']);
            }
            if(!empty($param['client_id'])){
                $query->where('h.client_id', $param['client_id'])->where('h.status', '<>', 'Cancelled');
            }
            if(!empty($param['keywords'])){
                try{
                    $language = get_client_lang();

                    $filterProductId = ProductModel::alias('p')
                        ->leftJoin('addon_multi_language ml', 'p.name=ml.name')
                        ->leftJoin('addon_multi_language_value mlv', 'ml.id=mlv.language_id AND mlv.language="'.$language.'"')
                        ->whereLike('p.name|mlv.value', '%'.$param['keywords'].'%')
                        ->limit(200)
                        ->column('p.id');
                    if(!empty($filterProductId)){
                        $query->where(function($query) use ($param, $filterProductId) {
                            $query->whereOr('h.id|h.name|c.username|c.email|c.phone', 'like', "%{$param['keywords']}%")
                                ->whereOr('p.id', 'IN', $filterProductId);
                        });
                    }else{
                        $query->where('h.id|p.name|h.name|c.username|c.email|c.phone', 'like', "%{$param['keywords']}%");   
                    }
                }catch(\Exception $e){
                    $query->where('h.id|p.name|h.name|c.username|c.email|c.phone', 'like', "%{$param['keywords']}%");
                }
            }
            if(!empty($param['status'])){
                if($param['status'] == 'Pending'){
                    $query->whereIn('h.status',['Pending','Failed']);
                }else{
                    $query->where('h.status', $param['status']);
                }
            }
            $query->where('s.module|ss.module','idcsmart_common');

            $query->where('p.product_id',0);
            $query->where('h.is_delete', 0);
        };
        $HostModel = new HostModel();
        $count = $HostModel->alias('h')
            ->field('h.id')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->leftjoin('server s','p.type=\'server\' AND p.rel_id=s.id AND s.module=\'idcsmart_common\'')
            ->leftjoin('server_group sg','p.type=\'server_group\' AND p.rel_id=sg.id')
            ->leftjoin('server ss','ss.server_group_id=sg.id AND ss.module=\'idcsmart_common\'')
            ->leftjoin('client c', 'c.id=h.client_id')
            ->where($where)
            ->count();
        $hosts = $HostModel->alias('h')
            ->field('h.id,h.product_id,h.client_id,c.username client_name,c.email,c.phone_code,c.phone,c.company,h.product_id,p.name product_name,h.name,h.create_time,h.active_time,h.due_time,h.first_payment_amount,h.renew_amount,h.billing_cycle,h.billing_cycle_name,h.status,o.pay_time,h.client_notes')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->leftjoin('server s','p.type=\'server\' AND p.rel_id=s.id AND s.module=\'idcsmart_common\'')
            ->leftjoin('server_group sg','p.type=\'server_group\' AND p.rel_id=sg.id')
            ->leftjoin('server ss','ss.server_group_id=sg.id AND ss.module=\'idcsmart_common\'')
            ->leftjoin('client c', 'c.id=h.client_id')
            ->leftjoin('order o', 'o.id=h.order_id')
            ->where($where)
            ->withAttr('status',function ($value){
                if ($value=='Failed'){
                    return 'Pending';
                }
                return $value;
            })
            ->withAttr('product_name', function($val){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'product_name' => $val,
                    ],
                ]);
                if(isset($multiLanguage['product_name'])){
                    $val = $multiLanguage['product_name'];
                }
                return $val;
            })
            ->withAttr('billing_cycle_name', function($val){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'billing_cycle_name' => $val,
                    ],
                ]);
                if(isset($multiLanguage['billing_cycle_name'])){
                    $val = $multiLanguage['billing_cycle_name'];
                }
                return $val;
            })
            ->limit($param['limit'])
            ->page($param['page'])
            ->order($param['orderby'], $param['sort'])
            ->select()
            ->toArray();

        if(!empty($hosts) && class_exists('app\common\model\SelfDefinedFieldModel')){
            $hostId = array_column($hosts, 'id');
            $productId = array_column($hosts, 'product_id');

            $SelfDefinedFieldModel = new SelfDefinedFieldModel();
            $selfDefinedField = $SelfDefinedFieldModel->getHostListSelfDefinedFieldValue([
                'product_id' => $productId,
                'host_id'    => $hostId,
            ]);
        }
        foreach ($hosts as $key => $host) {
            $hosts[$key]['first_payment_amount'] = amount_format($host['first_payment_amount']); // 处理金额格式
            $hosts[$key]['billing_cycle'] = $host['billing_cycle']!='onetime' ? $host['billing_cycle_name'] : '';
            $hosts[$key]['self_defined_field'] = $selfDefinedField['self_defined_field_value'][ $host['id'] ] ?? (object)[];

            unset($hosts[$key]['client_id'], $hosts[$key]['client_name'], $hosts[$key]['email'], $hosts[$key]['phone_code'], $hosts[$key]['phone'], $hosts[$key]['company']);

            unset($hosts[$key]['billing_cycle_name'], $hosts[$key]['create_time'], $hosts[$key]['pay_time']);
        }

        return ['list' => $hosts, 'count' => $count, 'self_defined_field' => $selfDefinedField['self_defined_field'] ?? []];
    }

    # 删除商品时实现钩子
    public function deleteProduct($param)
    {
        $productId = $param['id']??0;

        $this->startTrans();

        try{
            $this->where('product_id',$productId)->delete();

            $IdcsmartCommonPricingModel = new IdcsmartCommonPricingModel();
            $IdcsmartCommonPricingModel->where('type','product')->where('rel_id',$productId)->delete();

            $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();
            $IdcsmartCommonCustomCycleModel->where('product_id',$productId)->delete();

            $IdcsmartCommonCustomCyclePricingModel = new IdcsmartCommonCustomCyclePricingModel();
            $IdcsmartCommonCustomCyclePricingModel->where('type','product')->where('rel_id',$productId)->delete();

            $IdcsmartCommonProductConfigoptionModel = new IdcsmartCommonProductConfigoptionModel();
            $configoptions = $IdcsmartCommonProductConfigoptionModel->where('product_id',$productId)
                ->select()
                ->toArray();
            $IdcsmartCommonProductConfigoptionSubModel = new IdcsmartCommonProductConfigoptionSubModel();
            foreach ($configoptions as $configoption){
                $configoptionSubsId = $IdcsmartCommonProductConfigoptionSubModel->where('product_configoption_id',$configoption['id'])
                    ->column('id');
                $IdcsmartCommonCustomCyclePricingModel->whereIn('rel_id',$configoptionSubsId)
                    ->where('type','configoption')
                    ->delete();

                $IdcsmartCommonPricingModel->whereIn('rel_id',$configoptionSubsId)
                    ->where('type','configoption')
                    ->delete();

                $IdcsmartCommonProductConfigoptionSubModel->whereIn('id',$configoptionSubsId)->delete();
                $IdcsmartCommonProductConfigoptionModel->where('id',$configoption['id'])->delete();
            }
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();

            return false;
        }

        return true;
    }

    # 更新商品最低配置价格数据
    public function updateProductMinPrice($product_id)
    {
        $res = $this->productMinPrice($product_id);

        $ProductModel = new ProductModel();

        $ProductModel->setPriceCycle($product_id, $res['price'], $res['cycle']);
        return true;
    }

    # 获取商品最低配置价格数据
    public function productMinPrice($product_id)
    {
        $ProductModel = new ProductModel();
        $product = $ProductModel->find($product_id);

        $IdcsmartCommonLogic = new IdcsmartCommonLogic();

        $IdcsmartCommonProductConfigoptionModel = new IdcsmartCommonProductConfigoptionModel();

        $IdcsmartCommonProductConfigoptionSubModel = new IdcsmartCommonProductConfigoptionSubModel();

        $cycle = null;
        if ($product['pay_type']=='free'){
            $price = 0;
        }elseif ($product['pay_type']=='onetime'){
            $IdcsmartCommonPricingModel = new IdcsmartCommonPricingModel();
            $commonPricing = $IdcsmartCommonPricingModel->where('type','product')
                ->where('rel_id',$product_id)
                ->where('onetime','>=',0)
                ->find();
            $customPrice = $commonPricing['onetime']??0;

            $configoptions = $IdcsmartCommonProductConfigoptionModel->field('id,product_id,option_name,option_type,qty_min,qty_max,unit,allow_repeat,max_repeat,description,fee_type')
                ->where('product_id',$product_id)
                ->where('hidden',0)
                ->order('order','asc') # 升序
                ->order('id','asc')
                ->select()
                ->toArray();
            foreach ($configoptions as $configoption){
                if ($IdcsmartCommonLogic->checkQuantity($configoption['option_type'])){
                    if ($configoption['fee_type']=='stage') { # 阶梯计费 数量选最小
                        $qtyMin = $configoption['qty_min'];
                        if ($qtyMin>0){
                            $subPricing = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                                ->leftJoin('module_idcsmart_common_pricing cp','cp.rel_id=pcs.id AND cp.type=\'configoption\'')
                                ->where('pcs.product_configoption_id',$configoption['id'])
                                ->where('pcs.qty_min',$qtyMin)
                                ->where('cp.onetime','>=',0) # 金额>=0
                                ->find();
                            $customPrice += ($subPricing['onetime']??0) * 1; # 阶梯计费,数量最小都按1个算
                        }
                    }else{ # 数量计费 价格总价最小
                        $subPricings = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                            ->leftJoin('module_idcsmart_common_pricing cp','cp.rel_id=pcs.id AND cp.type=\'configoption\'')
                            ->where('pcs.product_configoption_id',$configoption['id'])
                            ->where('cp.onetime','>=',0) # 金额>=0
                            ->select()
                            ->toArray();
                        $qtyPriceArray = [];
                        foreach ($subPricings as $subPricing){
                            $qtyPriceArray[] = $subPricing['onetime'] * $subPricing['qty_min'];
                        }
                        if (!empty($qtyPriceArray)){
                            $customPrice += min($qtyPriceArray);
                        }
                    }
                }elseif ($IdcsmartCommonLogic->checkMultiSelect($configoption['option_type'])){ # 多选不选
                    $customPrice += 0;
                }else{
                    $amount = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                        ->leftJoin('module_idcsmart_common_pricing cp','cp.rel_id=pcs.id AND cp.type=\'configoption\'')
                        ->where('pcs.product_configoption_id',$configoption['id'])
                        ->where('pcs.hidden',0)
                        ->where('cp.onetime','>=',0)
                        ->order('pcs.order','asc') # 升序
                        ->order('pcs.id','asc')
                        ->min('cp.onetime');

                    $customPrice += $amount;
                }
            }

            $price = $customPrice;

        }else{
            $IdcsmartCommonCustomCycleModel = new IdcsmartCommonCustomCycleModel();
            $customCycles = $IdcsmartCommonCustomCycleModel->alias('cc')
                ->field('cc.id,cc.name,cc.cycle_time,cc.cycle_unit,ccp.amount')
                ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.custom_cycle_id=cc.id AND ccp.type=\'product\'')
                ->where('cc.product_id',$product_id)
                ->where('ccp.rel_id',$product_id)
                ->where('ccp.amount','>=',0) # 可显示出得周期
                ->select()
                ->toArray();

            $configoptions = $IdcsmartCommonProductConfigoptionModel->field('id,product_id,option_name,option_type,qty_min,qty_max,unit,allow_repeat,max_repeat,description,fee_type')
                ->where('product_id',$product_id)
                ->where('hidden',0)
                ->order('order','asc') # 升序
                ->order('id','asc')
                ->select()
                ->toArray();

            // $priceArray = [];

            $minPrice = null;
            foreach ($customCycles as $customCycle){

                $customPrice = $customCycle['amount']??0;

                foreach ($configoptions as $configoption){
                    if ($IdcsmartCommonLogic->checkQuantity($configoption['option_type'])){
                        if ($configoption['fee_type']=='stage'){ # 阶梯计费 数量选最小
                            $qtyMin = $configoption['qty_min'];
                            if ($qtyMin>0){
                                $subPricing = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                                    ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.rel_id=pcs.id AND ccp.type=\'configoption\'')
                                    ->where('pcs.product_configoption_id',$configoption['id'])
                                    ->where('pcs.qty_min',$qtyMin)
                                    ->where('ccp.custom_cycle_id',$customCycle['id'])
                                    ->where('ccp.amount','>=',0) # 金额>=0
                                    ->find();
                                $customPrice += ($subPricing['amount']??0) * 1; # 阶梯计费,数量最小都按1个算
                            }
                        }else{ # 数量计费 价格总价最小
                            $subPricings = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                                ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.rel_id=pcs.id AND ccp.type=\'configoption\'')
                                ->where('pcs.product_configoption_id',$configoption['id'])
                                ->where('ccp.custom_cycle_id',$customCycle['id'])
                                ->where('ccp.amount','>=',0) # 金额>=0
                                ->select()
                                ->toArray();
                            $qtyPriceArray = [];
                            foreach ($subPricings as $subPricing){
                                $qtyPriceArray[] = $subPricing['amount'] * $subPricing['qty_min'];
                            }
                            if (!empty($qtyPriceArray)){
                                $customPrice += min($qtyPriceArray);
                            }

                        }

                    }elseif ($IdcsmartCommonLogic->checkMultiSelect($configoption['option_type'])){ # 多选不选
                        $customPrice += 0;
                    }else{ # 价格最小的配置项
                        $amount = $IdcsmartCommonProductConfigoptionSubModel->alias('pcs')
                            ->leftJoin('module_idcsmart_common_custom_cycle_pricing ccp','ccp.rel_id=pcs.id AND ccp.type=\'configoption\'')
                            ->where('pcs.product_configoption_id',$configoption['id'])
                            ->where('ccp.custom_cycle_id',$customCycle['id'])
                            ->where('pcs.hidden',0)
                            ->where('ccp.amount','>=',0) # 金额>=0
                            ->order('pcs.order','asc') # 升序
                            ->order('pcs.id','asc')
                            ->min('ccp.amount');
                        $customPrice += $amount;

                    }
                }

                if(is_numeric($minPrice)){
                    if($customPrice < $minPrice){
                        $minPrice = $customPrice;
                        $cycle = $customCycle['name'];
                    }
                }else{
                    $minPrice = $customPrice;
                    $cycle = $customCycle['name'];
                }
                // $priceArray[] = $customPrice;
            }

            $price = $minPrice ?? 0;
        }

        return ['price'=>$price, 'cycle'=>$cycle];
    }

    /**
     * 时间 2022-09-26
     * @title 产品升降级页面
     * @desc 产品升降级页面
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @return  object host -
     * @return  int host.product_id - 商品ID
     * @return  string host.name - 名称
     * @return  float host.first_payment_amount - 金额
     * @return  string host.billing_cycle_name - 周期
     * @return  object configoptions - 配置
     * @return  array configoptions - 配置
     * @return  string configoptions[].option_type - 配置类型
     * @return  string configoptions[].option_name - 名称
     * @return  string configoptions[].sub_name - 子项名称
     * @return  string configoptions[].qty - 数量(类型为数量时,显示此值)
     * @return  string configoptions[].configoption_sub_id - 子项ID
     * @return  array son_host - 子产品
     * @return  int son_host[].id - 子产品ID
     * @return  string son_host[].name - 名称
     * @return  float son_host[].first_payment_amount - 金额
     * @return  string son_host[].billing_cycle_name - 周期
     * @return  array upgrade - 可升降级商品(参考购物车配置那块数据)
     */
    public function upgradePage($param)
    {
        $hostId = $param['host_id'];

        $where = [];
        $where[] = ['h.id', '=', $hostId];
        $where[] = ['h.is_delete', '=', 0];
        
        $HostModel = new HostModel();
        $host = $HostModel->alias('h')
            ->field('h.id,h.product_id,p.name,h.first_payment_amount,h.billing_cycle_name')
            ->leftJoin('product p','p.id=h.product_id')
            ->where($where)
            ->find();
        if (empty($host)){
            return [
                'status' => 400,
                'msg' => lang_plugins('error_message')
            ];
        }

        $IdcsmartCommonHostConfigoptionModel = new IdcsmartCommonHostConfigoptionModel();
        $configoptions = $IdcsmartCommonHostConfigoptionModel->alias('hc')
            ->field('dpc.option_type,dpc.option_name,dpcs.option_name as sub_name,hc.qty,hc.configoption_sub_id')
            ->leftJoin('module_idcsmart_common_product_configoption dpc','dpc.id=hc.configoption_id')
            ->leftJoin('module_idcsmart_common_product_configoption_sub dpcs','dpcs.id=hc.configoption_sub_id')
            ->where('hc.host_id',$hostId)
            ->where('dpc.son_product_id',0) # 没有子商品
            ->withAttr('option_name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->withAttr('sub_name', function($value){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'name' => $value,
                    ],
                ]);
                if(isset($multiLanguage['name'])){
                    $value = $multiLanguage['name'];
                }
                return $value;
            })
            ->select()
            ->toArray();

        $productId = $host['product_id'];

        $ProductUpgradeProductModel = new ProductUpgradeProductModel();

        $upgradeProductIds = $ProductUpgradeProductModel->where('product_id',$productId)->column('upgrade_product_id');

        $upgrade = [];

        foreach ($upgradeProductIds as $upgradeProductId){
            $result = $this->cartConfigoption(['product_id'=>$upgradeProductId]);
            $upgrade[] = $result['data']??[];
        }

        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'host' => $host,
                'configoptions' => $configoptions,
                'upgrade' => $upgrade
            ]
        ];
}

    /**
     * 时间 2022-09-26
     * @title 产品升降级异步获取升降级价格
     * @desc 产品升降级异步获取升降级价格
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param  object  - 与购物车计算价格参数一致:{"configoption":{"1"：2,"2":3,"4":[1,2,3]},"cycle":"monthly","product_id":104,son:{}}
     */
    public function syncUpgradePrice($param)
    {
        $time = time();

        $hostId = $param['host_id'];

        $where = [];
        $where[] = ['h.id', '=', $hostId];
        $where[] = ['h.is_delete', '=', 0];

        $HostModel = new HostModel();
        $host = $HostModel->alias('h')
            ->field('h.id,h.product_id,p.name,h.first_payment_amount,h.billing_cycle,h.billing_cycle_time,h.billing_cycle_name,h.due_time,h.active_time')
            ->leftJoin('product p','p.id=h.product_id')
            ->where($where)
            ->find();
        if (empty($host)){
            return ['status'=>400,'msg'=>lang_plugins('success_message')];
        }

        $IdcsmartCommonLogic = new IdcsmartCommonLogic();
        $result = $IdcsmartCommonLogic->cartCalculatePrice([
            'configoption' => $param['configoption']??[],
            'product_id' => $param['product_id']??0,
            'cycle' => $param['cycle']??'',
            'son' => $param['son']??[]
        ]);

        // 计算退款金额
        if($host['billing_cycle']=='onetime'){
            $refund = $host['first_payment_amount'];
        }else if($host['billing_cycle']=='free'){
            $refund = 0;
        }else{
            if($host['billing_cycle_time']>0){
                if(($host['due_time']-$time)>0){ // 以自然年计算,周期时间不固定,闰年多一天,导致金额有误差
                    $hookResult = hook_one('renew_host_refund_amount',['id'=>$hostId]);
                    $renewRefundTotal = $hookResult[0]??0; // 总续费退款
                    $renewCycleTotal = $hookResult[1]??0; // 总续费周期
                    if (isset($hookResult[2]) && $hookResult[2]){
                        $refund = $renewRefundTotal;
                    }else{
                        $hostBillingCycleTime = $host['due_time']-$renewCycleTotal-$host['active_time']; // 产品购买周期=(总到期时间-续费周期-开通时间)
                        $refund = bcdiv(bcdiv($host['first_payment_amount'],$hostBillingCycleTime,20)*($host['due_time']-$renewCycleTotal-$time), 1, 2);
                        $refund = bcadd($refund,$renewRefundTotal,2);
                    }
                    //$refund = bcdiv(bcdiv($host['first_payment_amount'],$host['billing_cycle_time'],20)*($host['due_time']-$time), 1, 2);
                }else{
                    $refund = $host['first_payment_amount'];
                }
            }else{
                $refund = $host['first_payment_amount'];
            }
        }

        $ProductModel = new ProductModel();
        $product = $ProductModel->find($param['product_id']??0);

        if($product['pay_type']=='onetime'){
            $pay = $result['data']['price'];
        }else if($product['pay_type']=='free'){
            $pay = 0;
        }else{
            if($result['data']['duration']>0){
                if(($host['due_time']-$time)>0){
                    // 升级后到期时间不变更，所以金额可能为0
                    $pay = $result['data']['price'];//bcdiv($result['data']['price']/$result['data']['duration']*($host['due_time']-$time), 1, 2);
                }else{
                    $pay = $result['data']['price'];
                }
            }else{
                $pay = $result['data']['price'];
            }
        }

        $upgradePrice = bcsub($pay,$refund,2);

        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'upgrade_price' => $upgradePrice>0?$upgradePrice:bcsub(0,0,2),
                'base_price' => $product['pay_type']=='free'?0:$result['data']['price']
            ]
        ];
    }

    /**
     * 时间 2022-09-26
     * @title 产品升降级
     * @desc 产品升降级
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   int product_id - 商品ID require
     * @param   object config_options - 与购物车结算的一样:{"configoption":{"1"：2,"2":3,"4":[1,2,3]},"cycle":"monthly","product_id":104,son:{}} require
     */
    public function upgrade($param)
    {
        $OrderModel = new OrderModel();

        $result = $OrderModel->createUpgradeOrder([
            'host_id' => $param['host_id'],
            'client_id' => get_client_id(),
            'upgrade_refund' => 0, # 不支持退款
            'product' => [
                'product_id' => $param['product_id'],
                'config_options' => $param['config_options'],
            ]
        ]);

        return $result;
    }

    /**
     * 时间 2022-09-26
     * @title 产品配置升降级页面
     * @desc 产品配置升降级页面
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @return  object host -
     * @return  int host.product_id - 商品ID
     * @return  string host.name - 名称
     * @return  float host.first_payment_amount - 金额
     * @return  string host.billing_cycle_name - 周期
     * @return  object configoptions - 配置
     * @return  array configoptions - 配置
     * @return  string configoptions[].option_type - 配置类型
     * @return  string configoptions[].option_name - 名称
     * @return  string configoptions[].sub_name - 子项名称
     * @return  string configoptions[].qty - 数量(类型为数量时,显示此值)
     * @return  array son_host - 子产品
     * @return  int son_host[].id - 子产品ID
     * @return  string son_host[].name - 名称
     * @return  float son_host[].first_payment_amount - 金额
     * @return  string son_host[].billing_cycle_name - 周期
     * @return  array upgrade_configoptions - 可升降级配置项
     * @return  int upgrade_configoptions[].id - 配置项ID
     * @return  int upgrade_configoptions[].option_name - 配置项名称
     * @return  array upgrade_configoptions[].subs - 配置子项数据
     */
    public function upgradeConfigPage($param)
    {
        $hostId = $param['host_id'];

        $where = [];
        $where[] = ['h.id', '=', $hostId];
        $where[] = ['h.is_delete', '=', 0];

        $HostModel = new HostModel();
        $host = $HostModel->alias('h')
            ->field('h.id,h.product_id,p.name,h.first_payment_amount,h.billing_cycle_name')
            ->leftJoin('product p','p.id=h.product_id')
            ->where($where)
            ->find();
        if (empty($host)){
            return [
                'status' => 400,
                'msg' => lang_plugins('error_message')
            ];
        }
        $productId = $host['product_id'];

        $IdcsmartCommonHostConfigoptionModel = new IdcsmartCommonHostConfigoptionModel();
        $configoptions = $IdcsmartCommonHostConfigoptionModel->alias('hc')
            ->field('dpc.id,dpc.option_type,dpc.option_name,dpcs.option_name as sub_name,hc.qty,hc.configoption_sub_id')
            ->leftJoin('module_idcsmart_common_product_configoption dpc','dpc.id=hc.configoption_id')
            ->leftJoin('idcsmart_module_idcsmart_common_product_configoption_sub dpcs','dpcs.id=hc.configoption_sub_id')
            ->where('hc.host_id',$hostId)
            ->where('dpc.option_type','<>','os')
            ->select()
            ->toArray();

        $IdcsmartCommonProductConfigoptionModel = new IdcsmartCommonProductConfigoptionModel();
        $upgradeConfigoptions = $IdcsmartCommonProductConfigoptionModel->where('product_id',$productId)
            ->where('hidden',0)
            ->where('option_type','<>','os')
            ->select()
            ->toArray();
        $IdcsmartCommonProductConfigoptionSubModel = new IdcsmartCommonProductConfigoptionSubModel();
        $upgradeConfigoptionsFilter = [];
        foreach ($upgradeConfigoptions as $upgradeConfigoption){
            $subs = $IdcsmartCommonProductConfigoptionSubModel->where('product_configoption_id',$upgradeConfigoption['id'])
                ->where('hidden',0)
                ->select()
                ->toArray();
            $upgradeConfigoption['subs'] = $subs;
            $upgradeConfigoptionsFilter[] = $upgradeConfigoption;
        }

        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'host' => $host,
                'configoptions' => $configoptions,
                'upgrade_configoptions' => $upgradeConfigoptionsFilter
            ]
        ];
    }

    /**
     * 时间 2022-09-26
     * @title 产品配置升降级异步获取升降级价格
     * @desc 产品配置升降级异步获取升降级价格
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param  object configoption - "configoption":{"1"：2,"2":3,"4":[1,2,3]}
     */
    public function syncUpgradeConfigPrice($param)
    {
        $IdcsmartCommonLogic = new IdcsmartCommonLogic();

        $res = $IdcsmartCommonLogic->upgradeConfigPrice($param);

        return [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data' => [
                'price' => $res['data']['price_difference']??0,
            ]
        ];
    }

    /**
     * 时间 2022-09-26
     * @title 产品配置升降级
     * @desc 产品配置升降级
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param  object configoption - "configoption":{"1"：2,"2":3,"4":[1,2,3]}
     * @return int id - 订单ID
     */
    public function upgradeConfig($param)
    {
        $IdcsmartCommonLogic = new IdcsmartCommonLogic();

        $res = $IdcsmartCommonLogic->upgradeConfigPrice($param);

        $OrderModel = new OrderModel();

        $data = [
            'host_id'     => $param['host_id']??0,
            'client_id'   => get_client_id(),
            'type'        => 'upgrade_config',
            'amount'      => $res['data']['price_difference'],
            'description' => $res['data']['description'],
            'price_difference' => $res['data']['new_first_payment_amount'],
            'renew_price_difference' => $res['data']['renew_price_difference'],
            'base_price' => $res['data']['base_price'],
            'upgrade_refund' => 0,
            'config_options' => [
                'configoption' => $param['configoption']??[],
                'buy' => $param['buy']??false,
                'renew_price_difference_son' => $res['data']['renew_price_difference_son'],
            ]
        ];

        return $OrderModel->createOrder($data);
    }

    /**
     * 时间 2023-06-01
     * @title 获取模块列表
     * @desc 获取模块列表
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @return array list -
     * @return string list[].name - 名称
     * @return string list[].value - 值
     */
    public function getModules($param){
        $ProvisionLogic  = new ProvisionLogic();
        $data = $ProvisionLogic->getModules();
        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'list' => $data
            ]
        ];
    }

    /**
     * 时间 2023-06-01
     * @title 获取模块自定义参数
     * @desc 获取模块自定义参数
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @param   int server_id - 服务器ID require
     * @return array configoption -
     * @return string configoption[].name - 名称
     * @return string configoption[].placeholder - 填充
     * @return string configoption[].description - 描述
     * @return string configoption[].default - 默认值
     * @return string configoption[].type - 类型text,password,yesno(值 on|off),radio,dropdown,textarea,
     * @return string configoption[].options - 选项,单选和下拉才有
     * @return string configoption[].rows - 文本域属性rows
     * @return string configoption[].cols - 文本域属性cols
     * @return object module_meta -
     * @return string module_meta.APIVersion - 版本
     * @return string module_meta.HelpDoc - 帮助文档地址
     */
    public function getModuleConfig($param)
    {
        $ProductModel = new ProductModel();
        $product = $ProductModel->where('id',$param['product_id']??0)->find();
        if (empty($product)){
            return ['status'=>400,'msg'=>lang_plugins('product_not_found')];
        }

        $IdcsmartCommonServerModel = new IdcsmartCommonServerModel();
        $server = $IdcsmartCommonServerModel->where('id',$param['server_id']??0)->find();
        if (empty($server)){
            return ['status'=>400,'msg'=>lang_plugins('idcsmart_common_server_not_exist')];
        }

        $data = [];

        if ($server['serer_type']=='dicm'){
            $data['configoption'] = [
                [
                    'default'=>'rent',
                    'description'=>'',
                    'name'=>'产品类型',
                    'type'=>'dropdown',
                    'options'=>[
                        ['value'=>'rent', 'name'=>'租用/托管'],
                        ['value'=>'cabinet', 'name'=>'机柜/带宽/IP'],
                        ['value'=>'bms', 'name'=>'裸金属'],
                    ]
                ]
            ];
            $data['module_meta']['HelpDoc'] = 'https://www.idcsmart.com/wiki_list/338.html#2.1.5';
        }else if($server['system_type'] == 'dcimcloud'){
            $data['configoption'] = [];
            $result['module_meta']['HelpDoc'] = 'https://www.idcsmart.com/wiki_list/358.html#2.1.3';
        }else{
            $module = $server['type'];
            $ProvisionLogic = new ProvisionLogic();
            $data['configoption'] = $ProvisionLogic->getModuleConfigOptions($module);
            $data['module_meta'] = $ProvisionLogic->getModuleMetaData($module);
        }

        return [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
    }
}
