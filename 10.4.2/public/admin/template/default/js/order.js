/* 用户信息-订单管理 */
(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("order")[0];
    Vue.prototype.lang = window.lang;
    Vue.prototype.moment = window.moment;
    new Vue({
      components: {
        comConfig,
        comTreeSelect
      },
      data() {
        return {
          id: "",
          rootRul: url,
          submitLoading: false,
          data: [],
          tableLayout: true,
          bordered: true,
          visible: false,
          delVisible: false,
          priceModel: false,
          hover: true,
          fullLoading: false,
          currency_prefix:
            JSON.parse(localStorage.getItem("common_set")).currency_prefix ||
            "¥",
          columns: [
            {
              colKey: "row-select",
              type: "multiple",
              width: 30,
            },
            {
              colKey: "id",
              title: "ID",
              width: 100,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "client_name",
              title: lang.user + "(" + lang.contact+ ")",
              width: 250,
              ellipsis: true,
            },
            {
              colKey: "icon",
              width: 16,
              className: "icon-width",
            },
            {
              colKey: "product_names",
              title: lang.product_name,
              width: 200,
              ellipsis: true,
            },
            // {
            //   colKey: 'host_name',
            //   title: lang.host_name,
            //   ellipsis: true,
            //   width: 130
            // },
            {
              colKey: "amount",
              title: lang.money,
              ellipsis: true,
              width: 150,
            },
            {
              colKey: "gateway",
              title: lang.pay_way,
              width: 170,
              ellipsis: true,
            },
            {
              colKey: "create_time",
              title: lang.order_time,
              width: 170,
              ellipsis: true,
            },
            {
              colKey: "status",
              title: lang.status,
              width: 120,
              ellipsis: true,
            },
            {
              colKey: "op",
              title: lang.operation,
              width: 120,
            },
          ],
          params: {
            keywords: "",
            page: 1,
            limit: 20,
            orderby: "id",
            sort: "desc",
            type: "",
            gateway: "",
            status: "",
            amount: "",
            username: "",
            product_id: ""
          },
          total: 0,
          father_client_id: "",
          pageSizeOptions: [20, 50, 100],
          loading: false,
          delId: "",
          expandIcon: true,
          delete_host: false, // 是否删除产品:0否1是
          // 变更价格
          formData: {
            id: "",
            amount: "",
            description: "",
          },
          rules: {
            amount: [
              {
                required: true,
                message: lang.input + lang.money,
                type: "error",
              },
              {
                pattern: /^-?\d+(\.\d{0,2})?$/,
                message: lang.verify10,
                type: "warning",
              },
              {
                validator: (val) => val * 1 !== 0,
                message: lang.verify10,
                type: "warning",
              },
            ],
            description: [
              {
                required: true,
                message: lang.input + lang.description,
                type: "error",
              },
              {
                validator: (val) => val.length <= 1000,
                message: lang.verify3 + 1000,
                type: "warning",
              },
            ],
          },
          orderNum: 0,
          signForm: {
            amount: 0,
            credit: 0,
          },
          payVisible: false,
          maxHeight: "",
          use_credit: true,
          curInfo: {},
          optType: "", // order,sub
          isAdvance: false,
          orderStatus: [
            { value: "Unpaid", label: lang.Unpaid },
            { value: "Paid", label: lang.Paid },
            { value: "Cancelled", label: lang.Cancelled },
            { value: "Refunded", label: lang.refunded },
          ],
          orderTypes: [
            { value: "new", label: lang.new },
            { value: "renew", label: lang.renew },
            { value: "upgrade", label: lang.upgrade },
            { value: "artificial", label: lang.artificial },
          ],
          payWays: [],
          range: [],
          /* 批量 */
          checkId: [],
          isBatch: false,
          deleteTit: "",
          hasCredit: false,
          recycleConfig: {
            order_recycle_bin: "",
            order_recycle_bin_save_days: "",
          },
          recycleVisble: false,
          searchType: "",
          typeOption: [
            { value: "", label: lang.auth_all },
            { value: "order_id", label: "ID" },
          //  { value: "username", label: lang.username },
            { value: "email", label: lang.email },
            { value: "phone", label: lang.phone },
            { value: "product_id", label: lang.product_name },
          ],
        };
      },
      created() {
        this.id = this.getQuery("id");
        /* 全局搜索 */
        const searchType = this.getQuery("type") || "";
        const keywords = this.getQuery("keywords") || "";
        if (searchType === "status") {
          this.params.status = keywords;
        } else if (searchType === "type") {
          this.params.type = keywords;
        } else if (searchType === "product_id") {
          this.params.product_id = keywords * 1 || "";
        } else if (searchType === "amount") {
          this.params.amount = keywords;
        } else if (searchType === "pay_time") {
          this.range.push(keywords, keywords);
          this.params.start_time = new Date(this.range[0].replace(/-/g, "/")).getTime() / 1000 || "";
          this.params.end_time = new Date(this.range[1].replace(/-/g, "/")).getTime() / 1000 || "";
        } else {
          this.params.keywords = keywords;
        }
        if (
          searchType === "pay_time" ||
          searchType === "status" ||
          searchType === "amount" ||
          searchType === "type"  ||
          searchType === "username"
        ) {
          this.searchType = "";
          this.isAdvance = true;
        } else {
          this.searchType = searchType;
        }
        /* 全局搜索 end */
        if (sessionStorage.orderListParams) {
          this.params = Object.assign(
            this.params,
            JSON.parse(sessionStorage.orderListParams)
          );
        }
        sessionStorage.removeItem("orderListParams");
        if (this.params.start_time && this.params.end_time) {
          this.isAdvance = true;
          const start = new Date(this.params.start_time * 1000);
          const end = new Date(this.params.end_time * 1000);
          // 2024-01-01
          this.range = [
            `${start.getFullYear()}-${
              start.getMonth() + 1 < 10
                ? "0" + (start.getMonth() + 1)
                : start.getMonth() + 1
            }-${
              start.getDate() < 10 ? "0" + start.getDate() : start.getDate()
            }`,
            `${end.getFullYear()}-${
              end.getMonth() + 1 < 10
                ? "0" + (end.getMonth() + 1)
                : end.getMonth() + 1
            }-${end.getDate() < 10 ? "0" + end.getDate() : end.getDate()}`,
          ];
        }
        this.getClientList();
        this.getPayWay();
        this.getRecycleSetting();
      },
      methods: {
        changeType () {
          this.params.keywords = "";
        },
        choosePro (id) {
          this.params.product_id = id;
        },
        getQuery(name) {
          const reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
          const r = window.location.search.substr(1).match(reg);
          if (r != null) return decodeURI(r[2]);
          return null;
        },
        /* 回收站 */
        async getRecycleSetting() {
          try {
            const res = await getRecycleConfig();
            this.recycleConfig = res.data.data;
          } catch (error) {}
        },
        openRecyle() {
          this.recycleVisble = true;
        },
        async handleRecyle() {
          try {
            this.submitLoading = true;
            const res = await openRecycleConfig();
            this.$message.success(res.data.msg);
            this.submitLoading = false;
            this.recycleVisble = false;
            this.getRecycleSetting();
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        goRecycle() {
          location.href = "order_recycle_bin.htm";
        },
        /* 回收站 end */
        async getAddonList() {
          try {
            const res = await getAddon();
            this.hasCredit =
              res.data.data.list.filter((item) => item.name === "CreditLimit")
                .length > 0;
            if (this.hasCredit) {
              this.payWays.unshift({
                name: "credit_limit",
                title: lang.credit_pay,
              });
            }
          } catch (error) {
            this.$message.error(error.data.msg);
          }
        },
        /* 批量删除 */
        batchDel() {
          this.renewForm = [];
          this.renewList = [];
          if (this.checkId.length === 0) {
            return this.$message.error(`${lang.select}${lang.order}`);
          }
          this.isBatch = true;
          this.delVisible = true;
          this.deleteTit = `${lang.batch_dele}${lang.order}`;
        },
        rehandleSelectChange(value, { selectedRowData }) {
          this.checkId = value;
          this.selectedRowKeys = selectedRowData;
        },
        /* 批量删除 end */
        changeAdvance() {
          this.isAdvance = !this.isAdvance;
          this.params.type = "";
          this.params.gateway = "";
          // this.params.status = ''
          this.params.amount = "";
          this.range = [];
        },
        async getPayWay() {
          try {
            const res = await getPayList();
            this.payWays = res.data.data.list;
            this.getAddonList();
          } catch (error) {
            this.$message.error(error.data.msg);
          }
        },
        lookDetail(row) {
          sessionStorage.currentOrderUrl = window.location.href;
          sessionStorage.orderListParams = JSON.stringify(this.params);
          location.href = `order_details.htm?id=${row.id}`;
        },
        jumpPorduct(client_id, id) {
          location.href = `host_detail.htm?client_id=${client_id}&id=${id}`;
        },
        addOrder() {
          location.href = "create_order.htm";
        },
        // 排序
        sortChange(val) {
          if (!val) {
            this.params.orderby = "id";
            this.params.sort = "desc";
          } else {
            this.params.orderby = val.sortBy;
            this.params.sort = val.descending ? "desc" : "asc";
          }
          this.getClientList();
        },
        clearKey() {
          this.params.keywords = "";
          this.seacrh();
        },
        seacrh() {
          this.params.page = 1;
          if (this.range.length > 0) {
            this.params.start_time =
              new Date(this.range[0].replace(/-/g, "/")).getTime() / 1000 || "";
            this.params.end_time =
              (new Date(this.range[1].replace(/-/g, "/")).getTime() +
                24 * 3600 * 1000) /
                1000 || "";
          } else {
            this.params.start_time = "";
            this.params.end_time = "";
          }
          this.getClientList();
        },
        // 自定义图标
        treeExpandAndFoldIconRender(h, { type }) {},
        // 调整价格
        updatePrice(row, type) {
          this.optType = type;
          this.formData.id = row.id;
          this.formData.amount = "";
          this.formData.description = "";
          this.$refs.update_price && this.$refs.update_price.clearValidate();
          this.priceModel = true;
          this.curInfo = row;
          if (type === "sub") {
            this.formData = { ...row };
          }
        },
        async onSubmit({ validateResult, firstError }) {
          if (validateResult === true) {
            if (this.optType === "order") {
              this.changeOrderPrice();
            } else {
              this.changeSubPrice();
            }
          } else {
            console.log("Errors: ", validateResult);
            this.$message.warning(firstError);
          }
        },
        // 修改订单价格
        async changeOrderPrice() {
          try {
            this.submitLoading = true;
            await updateOrder(this.formData);
            this.$message.success(lang.modify_success);
            this.priceModel = false;
            this.getClientList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        // 修改子项人工价格
        async changeSubPrice() {
          try {
            this.submitLoading = true;
            await updateArtificialOrder(this.formData);
            this.$message.success(lang.modify_success);
            this.priceModel = false;
            this.getClientList();
            this.optType = "";
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },

        closePrice() {
          this.priceModel = false;
          this.$refs.priceForm.reset();
        },
        // 删除订单
        delteOrder(row) {
          this.delId = row.id;
          this.delVisible = true;
          this.delete_host = false;
          this.isBatch = false;
          this.deleteTit = lang.deleteOrder;
        },
        async onConfirm() {
          try {
            // 处理批量删除
            if (this.isBatch) {
              this.batchDeleteOrder();
              return;
            }
            const params = {
              id: this.delId,
              delete_host: this.delete_host ? 1 : 0,
            };
            this.submitLoading = true;
            await delOrderDetail(params);
            this.$message.success(window.lang.del_success);
            this.delVisible = false;
            this.params.page =
              this.data.length > 1 ? this.params.page : this.params.page - 1;
            this.getClientList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        async batchDeleteOrder() {
          try {
            this.submitLoading = true;
            await batchDelOrder({
              id: this.checkId,
              delete_host: this.delete_host ? 1 : 0,
            });
            this.$message.success(lang.del_success);
            this.delVisible = false;
            this.checkId = [];
            this.getClientList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        // 标记支付
        signPay(row) {
          if (row.status === "Paid") {
            return;
          }
          this.payVisible = true;
          this.delId = row.id;
          this.signForm.amount = row.amount;
          this.signForm.credit = row.client_credit;
        },
        async sureSign() {
          try {
            const params = {
              id: this.delId,
              use_credit: this.use_credit ? 1 : 0,
            };
            this.submitLoading = true;
            const res = await signPayOrder(params);
            this.$message.success(res.data.msg);
            this.getClientList();
            this.payVisible = false;
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        // 展开行
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.checkId = [];
          this.getClientList();
        },
        // 获取订单列表
        async getClientList() {
          try {
            this.loading = true;
            this.fullLoading = true;
            const params = JSON.parse(JSON.stringify(this.params));
            if (this.searchType && this.searchType !== 'product_id') {
              params[this.searchType] = params.keywords;
              params.keywords = "";
            }
            const res = await getOrder(params);
            this.data = res.data.data.list;
            this.total = res.data.data.count;
            this.data.forEach((item) => {
              item.list = [];
              item.isExpand = false;
            });
            this.loading = false;
            // if (JSON.stringify(this.curInfo) !== '{}') { //修改子项打开对应的订单下拉
            //   this.itemClick(this.curInfo)
            // } else {
            // }
          } catch (error) {
            this.loading = false;
          }
        },
        // id点击获取订单详情
        itemClick(row) {
          // if (row.order_item_count < 2) {
          //   this.jumpPorduct(row.client_id, row.host_id)
          //   return
          // }
          row.isExpand = row.isExpand ? false : true;
          const rowData = this.$refs.table.getData(row.id);
          this.$refs.table.toggleExpandData(rowData);
          if (row.list?.length > 0) {
            return;
          }
          this.father_client_id = row.client_id;
          this.getOrderDetail(this.optType === "sub" ? row.pId : row.id);
        },
        childItemClick(row) {
          this.jumpPorduct(this.father_client_id, row.host_id);
        },
        // 订单详情
        async getOrderDetail(id) {
          try {
            const res = await getOrderDetail(id);
            res.data.data.order.items.forEach((item) => {
              item.pId = res.data.data.order.id;
              this.$refs.table.appendTo(id, item);
            });
          } catch (error) {}
        },
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
