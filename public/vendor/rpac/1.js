(window.webpackJsonp=window.webpackJsonp||[]).push([[1],{186:function(e,t,n){"use strict";n.r(t);var o={data:function(){return{search:"",searchWidth:120,models:[],roles:[],entities:[],collapses:{},activeNames:["1"],table:[],loading:!0,edit:null,create:!1,error:"",form:{role:"",entity:"",action:"",name:""},rules:{role:[{required:!0,message:"Нужно выбрать",trigger:"change"}],entity:[{required:!0,message:"Нужно выбрать",trigger:"change"},{required:!0,message:"Нужно выбрать",trigger:"blur"}],action:[{required:!0,message:"Нужно указать",trigger:"blur"},{pattern:"[0-9a-zA-Z_]+",message:"Только латинские символы, цифры или _",trigger:"blur"}],name:[]},temp:{}}},methods:{checkPivot:function(e,t){var n=e.find((function(e){return e.id===t}));return!(!n||!n.pivot)&&n.pivot.role_id===t},openCreateForm:function(){this.error="",this.create=!0},resetForm:function(){!(arguments.length>0&&void 0!==arguments[0])||arguments[0];this.temp.rules&&(this.rules=Object.assign({},this.temp.rules),this.temp.rules=null)},submitForm:function(){var e=this;this.resetForm(),this.temp.rules||(this.temp.rules=Object.assign({},this.rules)),this.$refs.ruleForm.validate((function(t){if(!t)return!1;e.createRow(),e.resetForm()}))},createRow:function(){var e=this;this.loading=!0,this.$http.post("/rpac/permissions/",this.form).then((function(t){return e.form={role:"",entity:"",action:"",name:""},e.error="",t.data})).then((function(t){e.collapses[t.entity].push(t),e.create=!1,e.loading=!1})).catch((function(t){var n=t.response.data;console.error(t,n),n&&n.message&&(e.error=n.message,n.errors&&Object.keys(n.errors).forEach((function(t){e.rules[t]={type:"object",validator:function(e,o,r){e.message=n.errors[t].join(". "),r(new Error(e.message))}},e.$refs.ruleForm.validateField(t),e.rules[t]={}})))}))},deleteRow:function(e){var t=this;this.loading=!0,this.$http.post("/rpac/permissions/"+e,{_method:"DELETE"}).then((function(e){return e.data})).then((function(e){t.edit=null,t.loading=!1})).catch((function(e){return console.error(e)}))},changeRow:function(e,t){this.loading=!0,e._method="PUT",e.role=t,this.saveRow(e)},saveRow:function(e){var t=this;this.$http.post("/rpac/permissions/"+e.id,e).then((function(e){return e.data})).then((function(e){var n=t.collapses[e.entity].findIndex((function(t){return t.id===e.id}));t.collapses[e.entity][n]=e,t.edit=null,t.loading=!1})).catch((function(e){return console.error(e)}))},editRow:function(e){this.toggleEdit(e.id)},toggleEdit:function(e){this.edit=this.edit===e?null:e},getTableData:function(){var e=this;this.$http.get("/rpac/permissions").then((function(t){e.entities=Object.keys(t.data),e.collapses=t.data,console.warn("permissions",t,e.table),e.loading=!1})).catch((function(e){return console.error(e)}))},getRoles:function(){var e=this;this.$http.get("/rpac/roles").then((function(t){e.roles=t.data})).catch((function(e){return console.error(e)}))},getModels:function(){var e=this;this.$http.get("/wfac/access?model&abilities").then((function(t){e.models=t.data})).catch((function(e){return console.error(e)}))}},mounted:function(){this.getModels(),this.getRoles(),this.getTableData()}},r=(n(92),n(1)),i=Object(r.a)(o,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("el-main",[n("el-row",{staticStyle:{"min-height":"48px"}},[n("el-col",{attrs:{span:12}},[n("h3",{staticStyle:{margin:"0"}},[n("i",{staticClass:"el-icon-guide"}),e._v(" Права доступа")])]),e._v(" "),n("el-col",{staticStyle:{"text-align":"right"},attrs:{span:12}},[n("el-button",{attrs:{type:"text",icon:"el-icon-plus"},on:{click:e.openCreateForm}},[e._v("Создать")])],1)],1),e._v(" "),n("el-dialog",{attrs:{title:"Добавить доступ",visible:e.create},on:{"update:visible":function(t){e.create=t}}},[e.error?n("el-alert",{staticStyle:{display:"block","margin-bottom":"1em"},attrs:{type:"error"},domProps:{innerHTML:e._s(e.error)}}):e._e(),e._v(" "),n("el-form",{ref:"ruleForm",attrs:{model:e.form,"label-width":"140px",rules:e.rules}},[n("el-form-item",{attrs:{label:"Роль",prop:"role"}},[n("el-select",{attrs:{placeholder:"Роль"},model:{value:e.form.role,callback:function(t){e.$set(e.form,"role",t)},expression:"form.role"}},e._l(e.roles,(function(e){return n("el-option",{key:e.id,attrs:{label:e.name,value:e.id}})})),1)],1),e._v(" "),n("el-form-item",{attrs:{label:"Модель",prop:"entity"}},[n("el-select",{attrs:{placeholder:"Модель"},model:{value:e.form.entity,callback:function(t){e.$set(e.form,"entity",t)},expression:"form.entity"}},e._l(e.models,(function(e){return n("el-option",{key:e.model,attrs:{label:e.model,value:e.model}})})),1)],1),e._v(" "),n("el-form-item",{attrs:{label:"Метод политики",prop:"entity"}},[n("el-select",{attrs:{placeholder:"Метод политики",disabled:!e.form.entity},model:{value:e.form.action,callback:function(t){e.$set(e.form,"action",t)},expression:"form.action"}},[e.form.entity&&e.models.find((function(t){return t.model===e.form.entity}))?e._l(e.models.find((function(t){return t.model===e.form.entity})).actions,(function(e){return n("el-option",{key:e,attrs:{label:e,value:e}})})):e._e()],2)],1),e._v(" "),n("el-form-item",{attrs:{label:"Название",prop:"name"}},[n("el-input",{attrs:{autocomplete:"off",placeholder:"Название (для удобства)",disabled:!e.form.action},model:{value:e.form.name,callback:function(t){e.$set(e.form,"name",t)},expression:"form.name"}})],1)],1),e._v(" "),n("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("el-button",{on:{click:function(t){e.create=!1}}},[e._v("Отмена")]),e._v(" "),n("el-button",{attrs:{type:"primary"},on:{click:e.submitForm}},[e._v("Сохранить")])],1)],1),e._v(" "),e.entities.length?n("el-collapse",[e._l(e.entities,(function(t){return[n("el-collapse-item",{attrs:{title:t,name:t}},[n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticStyle:{"min-width":"100%"},attrs:{data:e.collapses[t]}},[n("el-table-column",{attrs:{prop:"name",label:"Действие (метод политики)",width:"400"},scopedSlots:e._u([{key:"default",fn:function(t){return n("div",{on:{dblclick:function(n){return e.editRow(t.row)}}},[t.row.id===e.edit?[n("el-form",{attrs:{inline:!0}},[n("el-form-item",[n("el-input",{attrs:{autocomplete:"off",placeholder:"Название"},nativeOn:{keyup:function(n){return!n.type.indexOf("key")&&e._k(n.keyCode,"enter",13,n.key,"Enter")?null:e.changeRow(t.row,null)}},model:{value:t.row.name,callback:function(n){e.$set(t.row,"name",n)},expression:"scope.row.name"}})],1),e._v(" "),n("el-form-item",[n("el-input",{attrs:{autocomplete:"off",required:"true",placeholder:"Метод класса модели"},nativeOn:{keyup:function(n){return!n.type.indexOf("key")&&e._k(n.keyCode,"enter",13,n.key,"Enter")?null:e.changeRow(t.row,null)}},model:{value:t.row.action,callback:function(n){e.$set(t.row,"action",n)},expression:"scope.row.action"}})],1)],1)]:[n("strong",[e._v(e._s(t.row.name))]),e._v(" "),n("i",[e._v("("+e._s(t.row.action)+")")])]],2)}}],null,!0)}),e._v(" "),n("el-table-column",{attrs:{label:"Права по ролям",align:"center","header-align":"center",prop:"roles"}},e._l(e.roles,(function(t,o){return n("el-table-column",{key:o,attrs:{label:t.name,align:"center","header-align":"center"},scopedSlots:e._u([{key:"default",fn:function(o){return[n("el-checkbox",{attrs:{name:"roles",checked:e.checkPivot(o.row.roles,t.id)},on:{change:function(n){return e.changeRow(o.row,t.id)}}})]}}],null,!0)})})),1),e._v(" "),n("el-table-column",{attrs:{fixed:"right",width:"80",align:"right"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("el-popconfirm",e._g({attrs:{confirmButtonText:"Удалить",confirmButtonType:"danger",cancelButtonText:"Отмена",icon:"el-icon-delete",iconColor:"red",title:"Удалить права?"},on:{confirm:function(n){return e.deleteUser(t.row.id)}}},{onConfirm:function(n){return e.deleteRow(t.row.id)},onCancel:function(e){return!1}}),[n("el-button",{attrs:{slot:"reference",type:"danger",size:"small",icon:"el-icon-delete"},slot:"reference"})],1)]}}],null,!0)})],1)],1)]}))],2):e._e()],1)}),[],!1,null,"0b27a01c",null);t.default=i.exports},22:function(e,t,n){var o=n(93);"string"==typeof o&&(o=[[e.i,o,""]]);var r={hmr:!0,transform:void 0,insertInto:void 0};n(16)(o,r);o.locals&&(e.exports=o.locals)},92:function(e,t,n){"use strict";var o=n(22);n.n(o).a},93:function(e,t,n){(e.exports=n(15)(!1)).push([e.i,".el-form-item__label[data-v-0b27a01c] {\n  width: 120px;\n}\n.el-dialog__title[data-v-0b27a01c] {\n  padding-left: 120px;\n}\n.el-dialog__footer[data-v-0b27a01c] {\n  text-align: left;\n  padding-left: 140px;\n}\n.el-input__suffix[data-v-0b27a01c] {\n  right: 10px;\n}\n.el-table th[data-v-0b27a01c],\n.el-table td[data-v-0b27a01c] {\n  vertical-align: text-top;\n}\n.tar[data-v-0b27a01c] {\n  text-align: right;\n}\n.no-wrap[data-v-0b27a01c] {\n  white-space: nowrap;\n  text-overflow: ellipsis;\n  max-width: 100%;\n  overflow: hidden;\n  display: inline-block;\n}",""])}}]);