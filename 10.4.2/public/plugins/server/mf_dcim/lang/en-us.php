<?php 

return [
	'change' => 'change',
	'null' => 'empty',
	'success_message' => 'request successful',
	'param_error'=>'parameter error',
	'create_success'=>'created successfully',
	'create_fail'=>'create failed',
	'update_success'=>'modified successfully',
	'update_fail'=>'Modification failed',
	'delete_success'=>'delete successfully',
	'delete_fail'=>'Delete failed',
	'no_auth' => 'no authority',
	'mf_dcim_cannot_buy_flow_packet'=>'This product does not support the purchase of flow packets',
	'mf_dcim_flow_limit_desc'=>'Flow limit: {total}GB, used: {used}GB',
	
	// model
	'product_not_found'=>'Product does not exist',
	'product_not_link_mf_dcim_module'=>'The product is not associated with the DCIM (custom configuration) module interface',
	'mf_dcim_line_not_found'=>'The line does not exist',
	'mf_dcim_model_config_not_found'=>'model configuration does not exist',
	'mf_dcim_already_add_the_same_config_limit'=>'The same configuration limit has been added',
	'mf_dcim_config_limit_not_found'=>'Configuration limit does not exist',
	'mf_dcim_cannot_select_this_config'=>'cannot select this configuration',
	'mf_dcim_country_id_error'=>'country ID error',
	'mf_dcim_the_same_data_center_already_add'=>'The same data center has been added',
	'mf_dcim_cannot_delete_data_center_for_line_exist'=>'There is a line under the data center and cannot be deleted',
	'mf_dcim_duration_not_found'=>'period does not exist',
	'mf_dcim_line_bw_not_found'=>'Bandwidth configuration does not exist',
	'mf_dcim_line_flow_not_found'=>'Flow configuration does not exist',
	'mf_dcim_line_defence_not_found'=>'Defense configuration does not exist',
	'mf_dcim_line_add_ip_not_found'=>'Public network IP configuration does not exist',
	'mf_dcim_image_not_found'=>'The operating system does not exist',
	'mf_dcim_peak_defence_not_found'=>'Defense peak does not exist',
	'mf_dcim_ip_num_error'=>'IP number error',
	'mf_dcim_host_already_created'=>'The product has been opened',
	'mf_dcim_host_create_success'=>'Open successfully',
	'mf_dcim_host_create_fail'=>'Failed to open',
	'mf_dcim_not_link_dcim'=>'The product is not activated',
	'mf_dcim_suspend_success'=>'Suspend successfully',
	'mf_dcim_suspend_fail'=>'Suspend failed',
	'mf_dcim_unsuspend_success'=>'Unsuspend successfully',
	'mf_dcim_unsuspend_fail'=>'Unsuspend failed',
	'mf_dcim_image_group_name_already_add'=>'category name has been added',
	'mf_dcim_image_group_not_found'=>'The operating system classification does not exist',
	'mf_dcim_image_group_cannot_delete'=>'There are operating systems under the operating system category, which cannot be deleted',
	'mf_dcim_host_not_found'=>'The product does not exist',
	'mf_dcim_product_must_link_server_can_sync_image'=>'Only the product directly associated with the interface can pull the operating system',
	'mf_dcim_sync_image_failed'=>'Failed to pull the operating system, please check the interface status',
	'mf_dcim_line_name_exist'=>'The line name already exists',
	'mf_dcim_already_add_the_same_option'=>'The same configuration has been added',
	'mf_dcim_option_intersect' => 'Add configuration range intersection',
	'mf_dcim_option_not_found'=>'Configuration does not exist',
	'mf_dcim_vnc_token_expired'=>'The console certificate has expired, please reopen',
	'mf_dcim_onetime'=>'one time',
	'mf_dcim_free'=>'Free',
	'mf_dcim_real_bw'=>'real bandwidth',
	'mf_dcim_unlimited_flow'=>'unlimited flow',
	'mf_dcim_real_ip'=>'real IP',
	'mf_dcim_custom_ip_num_format_error'=>'IP number format is wrong',
	'mf_dcim_buy_image'=>'buy image',
	'mf_dcim_line_not_bw_cannot_add_bw_rule'=>'The line is not bandwidth billing, bandwidth billing cannot be added',
	'mf_dcim_line_not_flow_cannot_add_flow_rule'=>'The line is not flow metering, flow metering cannot be added',
	'mf_dcim_upgrade_ip_num_success'=>'Modify the number of public network IP successfully',
	'mf_dcim_upgrade_ip_num_fail'=>'Failed to modify the number of public network IP, reason:',
	'mf_dcim_upgrade_in_bw_success'=>'Modify into bandwidth successfully',
	'mf_dcim_upgrade_in_bw_fail'=>'Failed to modify bandwidth, reason:',
	'mf_dcim_upgrade_out_bw_success'=>'Modify the bandwidth successfully',
	'mf_dcim_upgrade_out_bw_fail'=>'Failed to modify the bandwidth, reason:',
	'mf_dcim_upgrade_flow_success'=>'Modify flow settings successfully',
	'mf_dcim_upgrade_flow_fail'=>'Failed to modify flow settings, reason:',
	'mf_dcim_upgrade_flow_unsuspend_success'=>'Flow quota: {total}GB, used: {used}GB, successfully lifted the suspension due to excess flow',
	'mf_dcim_upgrade_flow_unsuspend_fail'=>'Flow quota: {total}GB, used: {used}GB, unsuspend failure due to excess flow, reason: {reason}',
	'mf_dcim_upgrade_config_complete'=>'Module upgrade configuration is complete,',
	'mf_dcim_ip_num_cannot_modify_to_nc'=>'Additional IP number cannot be modified to NC',
	'mf_dcim_modify_dcimid_fail'=>'Failed to modify DCIMID, reason:',
	'mf_dcim_server_is_not_free'=>'Server sales status is not idle',
	'mf_dcim_assign_dcimid_success'=>'Successfully assigned DCIMID',
	'mf_dcim_free_dcimid_success'=>'Idle Success DCIMID',
	'mf_dcim_free_dcimid_fail'=>', Idle failed DCIM: {dcimid}, reason: {reason}',
	'mf_dcim_base_config'=>'Basic Configuration',
	'mf_dcim_model_specification'=>'Model Specifications',
	'mf_dcim_model'=>'Model',
	'mf_dcim_network_config'=>'Network Configuration',
	'mf_dcim_time_duration'=>'duration',
	'mf_dcim_package_use_this_option_cannot_delete'=>'The flexible model is currently using this hardware configuration and cannot be deleted',
	'mf_dcim_cpu_option_not_found'=>'Processor does not exist',
	'mf_dcim_memory_option_not_found'=>'Memory does not exist',
	'mf_dcim_disk_option_not_found'=>'no fixed disks present',
	'mf_dcim_memory_optional_not_found'=>'Optional memory does not exist',
	'mf_dcim_disk_optional_not_found'=>'Optional hard drive does not exist',
	'mf_dcim_gpu_optional_not_found'=>'Optional graphics card does not exist',
	'mf_dcim_package_not_found'=>'Flexible models do not exist',
	'mf_dcim_not_optional'=>'Not optional',
	'mf_dcim_already_over_package_mem_max'=>'Exceeded the maximum supported memory of the current model',
	'mf_dcim_already_over_package_mem_num_max'=>'The maximum supported memory for the current model has been exceeded',
	'mf_dcim_already_over_package_disk_num_max'=>'The maximum number of hard drives supported by the current model has been exceeded',
	'mf_dcim_already_over_package_gpu_num_max'=>'The maximum number of graphics cards supported by the current model has been exceeded',
	'mf_dcim_addition_memory'=>'add memory',
	'mf_dcim_addition_disk'=>'Add Hard Drive',
	'mf_dcim_addition_gpu'=>'Add graphics card',
	'mf_dcim_client_level'=>'User level {name}- {value}% percentage discount',
	'mf_dcim_additional_ip'=>'Additional IP',
	'mf_dcim_host_used_cannot_delete'=>'A product is in use and cannot be deleted',
	'mf_dcim_disk_cannot_reduce'=>'Hard drive cannot be reduced',
	'mf_dcim_no_defence'=>'No Defense',
	'mf_dcim_please_select_dcim_server'=>'please choose the server',
	'mf_dcim_assign_server_but_already_assigned'=>'DCIM ID:{dcim_id} has been assigned to product {host}, please confirm the product status',
	'mf_dcim_assign_success'=>'Allocation successful',
	'mf_dcim_assign_fail'=>'Allocation failed',
	'mf_dcim_free_success'=>'idle successfully',
	'mf_dcim_free_fail'=>'Idle failed',
	'mf_dcim_host_already_free'=>'Product is idle',

	// 
	'mf_dcim_data_center'=>'data center',
	'mf_dcim_model_config'=>'Model configuration',
	'mf_dcim_line'=>'line',
	'mf_dcim_bw'=>'Bandwidth',
	'mf_dcim_flow'=>'Flow',
	'mf_dcim_none' => 'none',
	'mf_dcim_switch_off'=>'off',
	'mf_dcim_switch_on'=>'open',
	'mf_dcim_rand_ssh_port' => 'random SSH port',
	'mf_dcim_country'=>'Country',
	'mf_dcim_city'=>'City',
	'mf_dcim_area'=>'Area',
	'mf_dcim_image'=>'Server image',
	'mf_dcim_peak_defence'=>'defense peak',
	'mf_dcim_ip_num'=>'IP number',
	'mf_dcim_image_group' => 'operating system classification',
	'mf_dcim_image_name'=>'name',
	'mf_dcim_image_charge'=>'Whether to charge',
	'mf_dcim_image_enable'=>'Available',
	'mf_dcim_image_rel_image_id'=>'associated operating system ID',
	'mf_dcim_enable'=>'enable',
	'mf_dcim_disable'=>'Disable',
	'mf_dcim_price'=>'price',
	'mf_dcim_line_name'=>'line name',
	'mf_dcim_line_bw_ip_group'=>'Bandwidth billing IP group',
	'mf_dcim_line_defence_enable' => 'enable defense price configuration',
	'mf_dcim_line_defence_ip_group'=>'Defense IP group',
	'mf_dcim_model_config_name'=>'configuration name',
	'mf_dcim_model_config_group_id'=>'Sales Group ID',
	'mf_dcim_model_config_cpu'=>'processor',
	'mf_dcim_model_config_cpu_param'=>'processor parameters',
	'mf_dcim_model_config_memory' => 'memory',
	'mf_dcim_model_config_disk'=>'hard disk',
	'mf_dcim_option_2'=>'Line bandwidth configuration',
	'mf_dcim_option_3'=>'Line traffic configuration',
	'mf_dcim_option_4'=>'Line protection configuration',
	'mf_dcim_option_5'=>'Line public network IP configuration',
	'mf_dcim_option_6'=>'processor configuration',
	'mf_dcim_option_7'=>'Memory Configuration',
	'mf_dcim_option_8'=>'Hard disk configuration',
	'mf_dcim_option_9'=>'Graphics card configuration',
	'mf_dcim_option_value_2'=>'Bandwidth',
	'mf_dcim_option_value_3'=>'Flow',
	'mf_dcim_option_value_4'=>'Defense Peak',
	'mf_dcim_option_value_5'=>'IP number',
	'mf_dcim_option_value_6'=>'processor',
	'mf_dcim_option_value_7'=>'Memory',
	'mf_dcim_option_value_8'=>'Hard disk',
	'mf_dcim_option_value_9'=>'Graphics Card',
	'mf_dcim_option_bill_cycle_month'=>'natural month',
	'mf_dcim_option_bill_cycle_last_30days'=>'Purchase day cycle',
	'mf_dcim_line_bw_in_bw'=>'incoming bandwidth',
	'mf_dcim_line_flow_in_bw'=>'into bandwidth limit',
	'mf_dcim_line_flow_out_bw'=>'out bandwidth limit',
	'mf_dcim_line_flow_bill_cycle'=>'Billing cycle',
	'mf_dcim_reinstall_sms_verify'=>'reinstall system SMS verification',
	'mf_dcim_reset_password_sms_verify'=>'reset password SMS verification',
	'mf_dcim_manual_resource'=>'Manual Resources',
	'mf_dcim_out_server_bw'=>'Outbound bandwidth',
	'mf_dcim_in_server_bw'=>'Inbound bandwidth',
	'mf_dcim_attach_ip_num'=>'Number of additional IPs',
	'mf_dcim_server_id'=>'DCIMID',
	'mf_dcim_ip'=>'IP address',
	'mf_dcim_level_discount_memory_order'=>'Is there a discount on the application level of memory for ordering',
	'mf_dcim_level_discount_memory_upgrade'=>'Is there a promotion or downgrade for memory application level discounts',
	'mf_dcim_level_discount_disk_order'=>'Does the hard drive apply level discounts for ordering',
	'mf_dcim_level_discount_disk_upgrade'=>'Is the hard drive upgraded or downgraded based on the application level discount',
	'mf_dcim_level_discount_bw_upgrade'=>'Is the bandwidth upgraded or downgraded based on the application level discount',
	'mf_dcim_level_discount_ip_num_upgrade'=>'Does IP apply level discounts for promotion or downgrade',
	'mf_dcim_memory_slot'=>'Memory slot',
	'mf_dcim_memory_capacity'=>'Memory capacity',
	'mf_dcim_package_name'=>'Model name',
	'mf_dcim_group_id'=>'Group ID',
	'mf_dcim_package_cpu'=>'processor',
	'mf_dcim_package_cpu_num'=>'Number of Processors',
	'mf_dcim_package_memory'=>'Memory',
	'mf_dcim_package_mem_num'=>'Number of memory',
	'mf_dcim_package_disk'=>'Hard disk',
	'mf_dcim_package_disk_num'=>'Number of hard drives',
	'mf_dcim_package_bw'=>'bandwidth',
	'mf_dcim_package_description'=>'Simple description',
	'mf_dcim_package_optional_memory'=>'Optional memory configuration',
	'mf_dcim_package_mem_max'=>'maximum capacity',
	'mf_dcim_package_mem_max_num'=>'Maximum slot position',
	'mf_dcim_package_optional_disk'=>'Optional hard drive',
	'mf_dcim_package_disk_max_num'=>'maximum number',
	'mf_dcim_order'=>'sort',
	'mf_dcim_optional_host_auto_create'=>'Whether the optional machine is automatically activated',
    'mf_dcim_optional_host_cannot_auto_create'=>'The optional machine cannot be automatically activated',
    'mf_dcim_support_optional'=>'Allow value-added options',
    'mf_dcim_optional_only_for_upgrade'=>'Value added is only for upgrading and downgrading',
    'mf_dcim_leave_memory'=>'Remaining memory capacity',
    'mf_dcim_max_memory_num'=>'The number of memory items can be increased',
	'mf_dcim_max_disk_num'=>'The number of hard disks can be increased',
	'mf_dcim_level_discount_gpu_order'=>'Whether the graphics card should be ordered with grade discounts',
	'mf_dcim_level_discount_gpu_upgrade'=>'Whether the graphics card should be upgraded or downgraded with grade discounts',
	'mf_dcim_gpu'=>'graphics card',
	'mf_dcim_max_gpu_num'=>'Can increase the number of graphics cards',

	// validate
	'id_error' => 'ID error',
	'product_id_error'=>'product ID error',
	'data_center_id_error'=>'data center ID error',
	'please_select_model_config'=>'Please select the model configuration',
	'mf_dcim_please_select_image'=>'Please select the operating system',
	'mf_dcim_please_select_pay_duration'=>'Please select the payment period',
	'mf_dcim_notes_length_error'=>'The instance name cannot exceed 1000 characters',
	'mf_dcim_bw_error'=>'Bandwidth error',
	'mf_dcim_please_select_ip_num'=>'Please select the number of public network IP',
	'mf_dcim_please_select_line'=>'Please select the line',
	'mf_dcim_data_center_not_found'=>'Data center does not exist',
	'mf_dcim_please_input_password'=>'Please enter the password',
	'mf_dcim_password_format_error'=>'Password must be more than 6 characters, cannot start with "/", only uppercase letters, lowercase letters, numbers, ~!@#$&*()_-+=|{}[]; :<>?, special symbols in ./, and must contain lowercase letters, uppercase letters, numbers',
	'mf_dcim_please_select_rescue_type'=>'Please select the rescue system type',
	'mf_dcim_please_input_port'=>'Please input port',
	'mf_dcim_port_format_error'=>'The port can only be an integer between 1-65535',
	'mf_dcim_ip_num_format_error'=>'The number of public network IP can only be an integer of 1-99999',
	'mf_dcim_please_select_data_center'=>'Please select the data center',
	'mf_dcim_please_input_bw_min_value'=>'Please enter the minimum bandwidth value',
	'mf_dcim_bw_min_value_format_error'=>'The minimum value of the bandwidth can only be an integer of 0-99999999',
	'mf_dcim_please_input_bw_max_value'=>'Please enter the maximum bandwidth',
	'mf_dcim_bw_max_value_format_error'=>'The maximum bandwidth can only be an integer of 0-99999999',
	'mf_dcim_bw_max_value_must_gt_min_value'=>'The maximum bandwidth must be greater than the minimum bandwidth',
	'mf_dcim_please_input_flow_min_value'=>'Please input the minimum value of flow',
	'mf_dcim_flow_min_value_format_error'=>'The minimum flow value can only be an integer of 0-99999999',
	'mf_dcim_please_input_flow_max_value'=>'Please input the maximum flow',
	'mf_dcim_flow_max_value_format_error'=>'The maximum flow can only be an integer of 0-99999999',
	'mf_dcim_flow_max_value_must_gt_min_value'=>'The maximum flow must be greater than the minimum flow',
	'mf_dcim_rand_ssh_port_param_error'=>'Random SSH port parameter error',
	'mf_dcim_country_select_error'=>'country selection error',
	'mf_dcim_please_input_city'=>'Please enter the city',
	'mf_dcim_city_format_error'=>'The city cannot exceed 255 characters',
	'mf_dcim_please_input_area'=>'Please enter the area name',
	'mf_dcim_area_format_error'=>'The length of the area name cannot exceed 255 characters',
	'mf_dcim_order_require'=>'Please enter the order',
	'mf_dcim_order_format_error'=>'sorting can only be integers from 0-999',
	'mf_dcim_please_input_duration_name'=>'Please enter the period name',
	'mf_dcim_duration_name_length_error'=>'Cycle name cannot exceed 10 characters',
	'mf_dcim_please_input_duration_num'=>'Please enter the duration of the cycle',
	'mf_dcim_duration_num_format_error'=>'Cycle duration can only be an integer of 1-999',
	'mf_dcim_duration_unit_param_error'=>'Cycle duration unit parameter error',
	'mf_dcim_please_input_image_group_name'=>'Please enter the category name',
	'mf_dcim_image_group_name_length_error'=>'Classification name cannot exceed 50 characters',
	'mf_dcim_please_select_image_group_icon'=>'Please select the system icon',
	'mf_dcim_please_select_image_group'=>'Please select the system classification',
	'mf_dcim_please_input_image_name'=>'Please enter the system name',
	'mf_dcim_image_name_length_error'=>'The length of the system name cannot exceed 255 characters',
	'mf_dcim_charge_param_error'=>'Whether the charge parameter format is wrong',
	'mf_dcim_price_format_error'=>'price format error',
	'mf_dcim_price_must_between_0_999999'=>'The price can only be a number from 0-999999',
	'mf_dcim_enable_param_require'=>'Whether to enable parameters must',
	'mf_dcim_enable_param_error'=>'whether to enable parameter error',
	'mf_dcim_please_input_rel_image_id'=>'Please enter the operating system ID',
	'mf_dcim_please_select_line_bw_type'=>'Please select the billing method',
	'mf_dcim_line_bw_type_error'=>'Billing method error',
	'mf_dcim_please_input_bw'=>'Please input bandwidth',
	'mf_dcim_line_bw_format_error'=>'Bandwidth can only be an integer of 1-30000',
	'mf_dcim_please_input_min_value'=>'Please enter the minimum value',
	'mf_dcim_please_input_max_value'=>'Please enter the maximum value',
	'mf_dcim_line_bw_min_value_format_error'=>'The minimum value can only be an integer of 1-30000',
	'mf_dcim_line_bw_max_value_format_error'=>'The maximum value can only be an integer of 1-30000',
	'mf_dcim_max_value_must_gt_min_value'=>'The maximum value cannot be less than the minimum value',
	'mf_dcim_please_input_step'=>'Please enter the minimum change value',
	'mf_dcim_line_bw_step_format_error'=>'The minimum change value can only be an integer of 1-30000',
	'mf_dcim_price_cannot_lt_zero'=>'Please enter the correct price',
	'mf_dcim_in_bw_format_error'=>'Incoming bandwidth can only be an integer of 1-30000',
	'mf_dcim_step_must_gt_diff_of_max_and_min'=>'The minimum change value cannot exceed the difference between the maximum value and the minimum value',
	'mf_dcim_please_input_peak_defence'=>'Please input defense peak value',
	'mf_dcim_peak_defence_format_error'=>'Defense peak can only be an integer of 1-999999',
	'mf_dcim_please_input_line_flow'=>'Please input flow',
	'mf_dcim_line_flow_format_error'=>'The flow can only be an integer of 0-999999',
	'mf_dcim_option_other_config_param_error'=>'Configuration parameter error',
	'mf_dcim_please_input_flow_in_bw'=>'Please enter inbound bandwidth',
	'mf_dcim_flow_in_bw_format_error'=>'Inbound bandwidth can only be an integer of 0-30000',
	'mf_dcim_please_input_flow_out_bw'=>'Please enter the outbound bandwidth',
	'mf_dcim_flow_out_bw_format_error'=>'Outbound bandwidth can only be an integer of 0-30000',
	'mf_dcim_please_select_flow_bill_cycle'=>'Please select the billing cycle',
	'mf_dcim_please_input_line_ip_num'=>'Please enter the number of IP',
	'mf_dcim_line_ip_num_format_error'=>'The number of IP can only be an integer of 1-10000',
	'mf_dcim_please_input_line_name'=>'Please enter the line name',
	'mf_dcim_line_name_length_error'=>'The line name cannot exceed 50 characters',
	'mf_dcim_please_select_line_bill_type'=>'Please select the billing type',
	'mf_dcim_line_bw_ip_group_must_int'=>'Bandwidth IP grouping can only be an integer',
	'mf_dcim_line_defence_enable_param_error'=>'Defense price configuration parameter error',
	'mf_dcim_line_defence_ip_group_must_int'=>'The defense IP group can only be an integer',
	'mf_dcim_please_add_at_lease_one_bw_data'=>'Please add at least one bandwidth rule',
	'mf_dcim_please_add_at_lease_one_flow_data'=>'Please add at least one traffic billing',
	'mf_dcim_please_add_at_lease_one_defence_data'=>'Please add at least one defense price configuration',
	'mf_dcim_please_add_at_lease_one_ip_data'=>'Please add at least one public network IP price configuration',
	'mf_dcim_option_type_must_only_one_type' => 'only one billing method can be selected',
	'mf_dcim_line_bw_range_intersect'=>'Bandwidth range repeat',
	'mf_dcim_line_bw_already_exist'=>'Bandwidth cannot be repeated',
	'mf_dcim_line_flow_already_exist'=>'The flow cannot be repeated',
	'mf_dcim_line_defence_already_exist' => 'Defense peak cannot be repeated',
	'mf_dcim_line_ip_already_exist'=>'The number of IPs cannot be repeated',
	'mf_dcim_please_input_model_config_name'=>'Please enter the configuration name',
	'mf_dcim_model_config_name_length_error'=>'The length of the configuration name cannot exceed 100 characters',
	'mf_dcim_please_input_model_config_group_id'=>'Please enter the sales group ID',
	'mf_dcim_model_config_group_id_format_error'=>'Sales group ID can only be an integer from 1-99999999',
	'mf_dcim_please_input_model_config_cpu'=>'Please input the processor',
	'mf_dcim_model_config_cpu_length_error'=>'processor length cannot exceed 255 words',
	'mf_dcim_please_input_model_config_cpu_param'=>'Please input processor parameters',
	'mf_dcim_model_config_cpu_param_length_error'=>'The processor parameter length cannot exceed 255 words',
	'mf_dcim_please_input_model_config_memory'=>'Please input memory',
	'mf_dcim_model_config_memory_length_error'=>'The memory length cannot exceed 255 words',
	'mf_dcim_please_input_model_config_disk'=>'Please enter the hard disk',
	'mf_dcim_model_config_disk_length_error'=>'The length of the hard disk cannot exceed 255 words',
	'mf_dcim_image_group_require'=>'Please select image group',
	'mf_dcim_reinstall_sms_verify_param_error'=>'reinstall system SMS verification parameter error',
	'mf_dcim_reset_password_sms_verify_param_error'=>'reset password SMS verification parameter error',
	'mf_dcim_price_factor_format_error'=>'The price coefficient can only be a number from 0-9999',
	'mf_dcim_ip_num_length_error'=>'The number of IP cannot exceed 255 words',
	'mf_dcim_in_bw_format_error_for_update'=>'The input bandwidth can only be an integer of 0-30000',
	'mf_dcim_out_bw_format_error_for_update'=>'Outgoing bandwidth can only be an integer of 0-30000',
	'mf_dcim_defence_format_error_for_update' => 'Defense can only be an integer from 0-999999',
	'mf_dcim_cpu_value_require'=>'Please enter the processor',
	'mf_dcim_cpu_value_length_error'=>'The processor cannot exceed 255 words',
	'mf_dcim_memory_value_require'=>'Please enter memory',
	'mf_dcim_memory_value_length_error'=>'Memory cannot exceed 255 words',
	'mf_dcim_memory_slot_require'=>'Please enter the memory slot',
	'mf_dcim_memory_slot_format_error'=>'The memory slot can only be an integer from 1-1000',
	'mf_dcim_memory_capacity_require'=>'Please enter the memory capacity',
	'mf_dcim_memory_capacity_format_error'=>'The memory capacity can only be an integer from 1-100000',
	'mf_dcim_disk_value_require'=>'Please enter the hard disk',
	'mf_dcim_disk_value_length_error'=>'The hard disk cannot exceed 255 words',
	'mf_dcim_package_name_require'=>'Please enter the model name',
	'mf_dcim_package_name_length_error'=>'Model name cannot exceed 30 characters',
	'mf_dcim_package_group_id_require'=>'Please enter the group ID',
	'mf_dcim_package_group_id_format_error'=>'The group ID can only be an integer greater than 0',
	'mf_dcim_package_cpu_option_id_require'=>'Please select a processor',
	'mf_dcim_package_cpu_num_require'=>'Please enter the number of processors',
	'mf_dcim_package_cpu_num_format_error'=>'The number of processors can only be an integer from 1 to 10000',
	'mf_dcim_package_mem_option_id_require'=>'Please select memory',
	'mf_dcim_package_mem_num_require'=>'Please enter the memory quantity',
	'mf_dcim_package_mem_num_format_error'=>'The amount of memory can only be an integer from 1-256',
	'mf_dcim_package_disk_option_id_require'=>'Please select the hard disk',
	'mf_dcim_package_disk_num_require'=>'Please enter the number of hard disks',
	'mf_dcim_package_disk_num_format_error'=>'The number of hard disks can only be an integer from 1 to 256',
	'mf_dcim_package_bw_require'=>'Please enter bandwidth',
	'mf_dcim_package_bw_format_error'=>'The bandwidth can only be an integer from 1-30000',
	'mf_dcim_package_ip_num_require'=>'Please enter the IP number',
	'mf_dcim_package_ip_num_format_error'=>'The number of IPs can only be an integer from 1 to 30000',
	'mf_dcim_package_description_length_error'=>'Simple description cannot exceed 30 words',
	'mf_dcim_package_mem_max_format_error'=>'The maximum capacity can only be an integer from 0-99999999',
	'mf_dcim_package_mem_max_num_format_error'=>'The maximum slot can only be an integer from 0 to 10000',
	'mf_dcim_package_disk_max_num_format_error'=>'The maximum number can only be an integer from 0-10000',
	'mf_dcim_leave_memory_format_error'=>'The remaining memory capacity can only be an integer from 0-99999999',
	'mf_dcim_max_memory_num_format_error'=>'The number of memory items that can be added can only be an integer from 0 to 10000',
	'mf_dcim_max_disk_num_format_error'=>'The number of hard disks that can be added can only be an integer from 0 to 10000',
	'mf_dcim_gpu_require'=>'Please enter graphics card',
	'mf_dcim_gpu_format_error'=>'The graphics card cannot exceed 255 characters',
	'mf_dcim_max_gpu_num_format_error'=>'The number of graphics cards that can be added can only be a number from 0 to 10,000',
	'mf_dcim_ip_num_format_error2'=>'IP number format error',
	'mf_dcim_model_config_name_length_error2'=>'The model cannot exceed 255 characters',
	'mf_dcim_model_config_memory_length_error2'=>'The memory cannot exceed 2000 words',
	'mf_dcim_model_config_disk_length_error2'=>'The hard disk cannot exceed 2000 words',
	'mf_dcim_model_config_gpu_length_error2'=>'The graphics card cannot exceed 2000 words',
	'mf_dcim_image_param_error'=>'Operating system parameter error',
	'mf_dcim_ip_length_error'=>'The main IP cannot exceed 100 words',
	'mf_dcim_additional_ip_length_error'=>'Additional IP cannot exceed 65000 words',
	'mf_dcim_zjmf_dcim_id_param_error'=>'DCIMID parameter error',

	// logic
	'mf_dcim_on'=>'on',
	'mf_dcim_off'=>'off',
	'mf_dcim_suspend'=>'suspend',
	'mf_dcim_operating'=>'operating',
	'mf_dcim_fault'=>'fault',
	'mf_dcim_start_boot_success'=>'Boot initiated successfully',
	'mf_dcim_start_boot_fail' => 'failed to initiate boot',
	'mf_dcim_start_off_success'=>'shutdown initiated successfully',
	'mf_dcim_start_off_fail'=>'shutdown initiation failed',
	'mf_dcim_start_reboot_success'=>'reboot initiated successfully',
	'mf_dcim_start_reboot_fail'=>'Reboot initiation failed',
	'mf_dcim_vnc_start_fail'=>'Console startup failed',
	'mf_dcim_start_reset_password_success'=>'Reset password initiated successfully',
	'mf_dcim_start_reset_password_fail'=>'Failed to initiate password reset',
	'mf_dcim_start_rescue_success'=>'rescue system launched successfully',
	'mf_dcim_start_rescue_fail'=>'rescue system failed to initiate',
	'mf_dcim_image_is_charge_please_buy' => 'The image is a paid image, please buy first',
	'mf_dcim_start_reinstall_success'=>'Reinstallation initiated successfully',
	'mf_dcim_start_reinstall_fail'=>'reinstall failed',
	'mf_dcim_flow_info_get_failed'=>'Failed to obtain flow information',
	'mf_dcim_not_support_this_duration_to_upgrade'=>'This period is not supported for the time being and cannot be upgraded',
	'mf_dcim_line_not_found_to_upgrade_ip_num'=>'This instance IP upgrade is not supported for now',
	'mf_dcim_ip_num_not_change'=>'IP number has not changed',
	'mf_dcim_ip_num_error'=>'IP number error',
	'mf_dcim_upgrade_ip_num'=>'Number of IPs: {old}=>{new}',
	'mf_dcim_not_support_bw_upgrade'=>'This instance bandwidth upgrade is not supported for now',
	'mf_dcim_not_support_flow_upgrade'=>'This instance does not support flow upgrade at the moment',
	'mf_dcim_not_support_defence_upgrade'=>'This instance defense upgrade is not supported for now',
	'mf_dcim_not_change_config'=>'No change configuration',
	'mf_dcim_can_not_do_this'=>'Cannot perform this operation',
	'mf_dcim_host_not_link_server'=>'Product not associated interface',
	'mf_dcim_public_ip_num'=>'Number of public network IP',
	'mf_dcim_indivual'=>'',
	'mf_dcim_upgrade_bw_range_error'=>'Bandwidth cannot be lower than {bw}Mbps',
	'mf_dcim_upgrade_ip_num_range_error'=>'The number of IPs cannot be less than {ip_num}',

	// log
	'mf_dcim_log_modify' => ', modify {name} to {value}',
	'mf_dcim_log_common_modify' => ', {name} is modified from {old} to {new}',
	'mf_dcim_log_add_config_limit_success'=>'Add configuration limit successfully, data center: {data_center}, model: {name}',
	'mf_dcim_log_modify_config_limit_success'=>'Modify the configuration limit successfully, ID:{id}{detail}',
	'mf_dcim_log_delete_config_limit_success'=>'Delete configuration limit successful, data center: {data_center}, model: {name}',
	'mf_dcim_log_modify_config_success'=>'Modify other settings successfully {detail}',
	'mf_dcim_log_create_data_center_success'=>'Create data center successfully, name: {name}',
	'mf_dcim_log_modify_data_center_success'=>'Modify data center successfully, name: {name}{detail}',
	'mf_dcim_log_delete_data_center_success'=>'Delete data center successfully, name: {name}',
	'mf_dcim_log_add_duration_success'=>'add duration successfully, name: {name}',
	'mf_dcim_log_modify_duration_success'=>'The modification period is successful, the original name: {name}, the new name: {new_name}',
	'mf_dcim_log_delete_duration_success'=>'Delete duration successful, name: {name}',
	'mf_dcim_log_add_image_group_success'=>'adding the operating system category successfully, name: {name}',
	'mf_dcim_log_modify_image_group_success'=>'Modified operating system category successfully, original name: {name}, new name: {new_name}',
	'mf_dcim_log_delete_image_group_success'=>'Delete operating system category successfully, name: {name}',
	'mf_dcim_log_add_image_success'=>'add the operating system successfully, name: {name}',
	'mf_dcim_log_modify_image_success'=>'Modify the operating system successfully {detail}',
	'mf_dcim_log_delete_image_success'=>'Delete the operating system successfully, name: {name}',
	'mf_dcim_log_toggle_image_enable_success'=>'{act} operating system successful, name: {name}',
	'mf_dcim_log_add_line_success'=>'add line successfully, name: {name}',
	'mf_dcim_log_modify_line_success'=>'Modify the line successfully {detail}',
	'mf_dcim_log_delete_line_success'=>'delete line successfully, name: {name}',
	'mf_dcim_log_add_model_config_success'=>'Add model configuration successfully, configuration name: {name}',
	'mf_dcim_log_modify_model_config_success'=>'Modify model configuration successfully, ID:{id}{detail}',
	'mf_dcim_log_delete_model_config_success'=>'Delete model configuration successfully, configuration name: {name}',
	'mf_dcim_log_add_option_success'=>'add {option} successfully, {name}{detail}',
	'mf_dcim_log_modify_option_success'=>'Modify {option} successfully {detail}',
	'mf_dcim_log_delete_option_success'=>'Delete {option} successfully, {name}{detail}',
	'mf_dcim_log_host_start_boot_success'=>'Instance {hostname} boot initiated successfully',
	'mf_dcim_log_host_start_boot_fail'=>'Instance {hostname} failed to initiate startup',
	'mf_dcim_log_admin_host_start_boot_fail'=>'Instance {hostname} failed to boot, reason: {reason}',
	'mf_dcim_log_host_start_off_success'=>'Instance {hostname} shutdown initiated successfully',
	'mf_dcim_log_host_start_off_fail'=>'Instance {hostname} shutdown initiation failed',
	'mf_dcim_log_admin_host_start_off_fail'=>'Instance {hostname} shutdown failed, reason: {reason}',
	'mf_dcim_log_host_start_reboot_success'=>'Instance {hostname} reboot initiated successfully',
	'mf_dcim_log_host_start_reboot_fail'=>'Instance {hostname} restart failed',
	'mf_dcim_log_admin_host_start_reboot_fail'=>'Instance {hostname} restart failed, reason: {reason}',
	'mf_dcim_log_host_start_reset_password_success'=>'Instance {hostname} reset password initiated successfully',
	'mf_dcim_log_host_start_reset_password_fail'=>'Instance {hostname} failed to reset password',
	'mf_dcim_log_admin_host_start_reset_password_fail'=>'Instance {hostname} failed to reset password, reason: {reason}',
	'mf_dcim_log_host_start_rescue_success'=>'Instance {hostname} rescue mode initiated successfully',
	'mf_dcim_log_host_start_rescue_fail'=>'Instance {hostname} rescue mode failed to start',
	'mf_dcim_log_admin_host_start_rescue_fail'=>'Instance {hostname} rescue system failed, reason: {reason}',
	'mf_dcim_log_host_start_reinstall_success'=>'Instance {hostname} reinstallation system initiated successfully',
	'mf_dcim_log_host_start_reinstall_fail'=>'Instance {hostname} failed to initiate reinstallation system',
	'mf_dcim_log_admin_host_start_reinstall_fail'=>'instance {hostname} failed to reinstall the system, reason: {reason}',
	'mf_dcim_log_host_update_complete'=>'Modification configuration completed, product: {host}{detail}',
	'mf_dcim_log_upgrade_config_fail_for_no_dcim_id'=>'Upgrade configuration failed, reason: DCIMID not associated',
	'mf_dcim_log_upgrade_public_ip_num_success'=>'Module downgrade public IP number successfully',
	'mf_dcim_log_upgrade_public_ip_num_fail'=>'Module upgrading and upgrading public network IP number failed, reason: {reason}',
	'mf_dcim_log_host_flow_clear_and_unsuspend_success'=>'The flow of product {host} has been cleared successfully, and the suspension due to excess flow has been lifted successfully',
	'mf_dcim_log_host_flow_clear_but_unsuspend_fail'=>'The flow of product {host} is cleared successfully, and the suspension due to excess flow has failed, reason: {reason}',
	'mf_dcim_log_host_flow_clear_success'=>'Product {host} flow cleared successfully',
	'mf_dcim_log_host_flow_clear_fail'=>'Product {host} flow clear failed, reason: {reason}',
	'mf_dcim_log_host_over_flow_suspend_success'=>'Product {host} traffic usage exceeded, suspend successfully, traffic limit: {total}GB, used: {used}GB',
	'mf_dcim_log_host_over_flow_success_but_suspend_fail'=>'Product {host} traffic usage is excessive, suspension failed, traffic limit: {total}GB, used: {used}GB, reason: {reason}',
	'mf_dcim_log_host_over_flow_limit_bw_success'=>'Product {host} traffic usage exceeded, speed limit successful, traffic limit: {total}GB, used: {used}GB',
	'mf_dcim_log_host_over_flow_close_port_success'=>'Product {host} traffic usage is excessive, port closed successfully, traffic limit: {total}GB, used: {used}GB',
	'mf_dcim_log_host_over_flow_fail'=>'The product {host} has exceeded the flow usage, and the execution of the excess action failed, the flow limit: {total}GB, used: {used}GB, reason: {reason}',
	'mf_dcim_log_buy_flow_packet_success'=>'Flow package purchased successfully, successfully attached to product: {host}, order: {order}, flow: {flow}',
	'mf_dcim_log_buy_flow_packet_and_unsuspend_success'=>', after purchasing flow package - unsuspend successfully',
	'mf_dcim_log_buy_flow_packet_and_unsuspend_fail'=>', after purchasing a traffic package - unsuspend failed: {reason}',
	'mf_dcim_log_buy_flow_packet_remote_fail'=>'Flow package purchased successfully, failed to attach to product, please manually add temporary flow: {host}, order: {order}, flow: {flow}',
	'mf_dcim_log_create_package_success'=>'Successfully added flexible model, product: {product}, model name: {name}',
	'mf_dcim_log_update_package_success'=>'Successfully modified the flexible model, product: {product} {detail}',
	'mf_dcim_log_delete_package_success'=>'Successfully deleted flexible model, product: {product}, model name: {name}',
	'mf_dcim_log_assign_dcim_server_success'=>'Product {host} is assigned successfully, DCIM ID: {dcim_id}',
	'mf_dcim_log_assign_dcim_server_fail'=>'Product {host} allocation failed, failure reason: {reason}',
	'mf_dcim_log_free_dcim_server_success'=>'Product {host} is idle successfully, original DCIM ID: {dcim_id}',
	'mf_dcim_log_free_dcim_server_fail'=>'Product {host} failed to idle, failure reason: {reason}',

];


