<template>
    <el-main>
        <el-row style="min-height: 48px;">
            <el-col :span="12">
                <h3 style="margin: 0;"><i class="el-icon-guide"></i> Права доступа</h3>
            </el-col>
            <el-col :span="12" style="text-align: right;">
                <el-button type="text" @click="openCreateForm" icon="el-icon-plus">Создать</el-button>
            </el-col>
        </el-row>


        <el-dialog title="Добавить доступ" :visible.sync="create">
            <el-alert v-if="error" type="error" v-html="error" style="display: block; margin-bottom: 1em;"></el-alert>
            <el-form :model="form" label-width="140px" :rules="rules" ref="ruleForm">
                <el-form-item label="Роль" prop="role">
                    <el-select v-model="form.role" placeholder="Роль">
                        <el-option
                                v-for="role in roles"
                                :key="role.id"
                                :label="role.name"
                                :value="role.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Модель" prop="entity">
                    <el-select v-model="form.entity" placeholder="Модель">
                        <el-option
                                v-for="entity in models"
                                :key="entity.model"
                                :label="entity.model"
                                :value="entity.model">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Метод политики" prop="entity">
                    <el-select  v-model="form.action" placeholder="Метод политики" :disabled="!form.entity">
                        <template v-if="form.entity && models.find(m => m.model === form.entity)">
                            <el-option
                                    v-for="action in models.find(m => m.model === form.entity).actions"
                                    :key="action"
                                    :label="action"
                                    :value="action">
                            </el-option>
                        </template>
                    </el-select>
                </el-form-item>
                <el-form-item label="Название" prop="name">
                    <el-input v-model="form.name" autocomplete="off" placeholder="Название (для удобства)" :disabled="!form.action"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="create = false">Отмена</el-button>
                <el-button type="primary" @click="submitForm">Сохранить</el-button>
             </span>
        </el-dialog>

        <el-collapse v-if="entities.length">
            <template v-for="entity in entities">
                <el-collapse-item :title="entity" :name="entity">

                    <el-table
                            v-loading="loading"
                            :data="collapses[entity]"
                            style="min-width: 100%">

                        <el-table-column
                                prop="name"
                                label="Действие (метод политики)"
                                width="400"
                        >
                            <div slot-scope="scope" @dblclick="editRow(scope.row)">
                                <template v-if="scope.row.id===edit">
                                    <el-form :inline="true">
                                        <el-form-item>
                                            <el-input v-model="scope.row.name" autocomplete="off" placeholder="Название" @keyup.enter.native="changeRow(scope.row, null)"></el-input>
                                        </el-form-item>
                                        <el-form-item>
                                            <el-input v-model="scope.row.action" autocomplete="off" required="true" placeholder="Метод класса модели" @keyup.enter.native="changeRow(scope.row, null)"></el-input>
                                        </el-form-item>
                                    </el-form>
                                </template>
                                <template v-else>
                                    <strong>{{ scope.row.name }}</strong> <i>({{ scope.row.action }})</i>
                                </template>
                            </div>
                        </el-table-column>

                        <el-table-column label="Права по ролям" align="center" header-align="center" prop="roles">
                            <el-table-column
                                    v-for="(role, rindx) in roles"
                                    :key="rindx"
                                    :label="role.name" align="center" header-align="center"
                            >
                                <template slot-scope="scope">
                                    <el-checkbox name="roles" :checked="checkPivot(scope.row.roles, role.id)" @change="changeRow(scope.row, role.id)"></el-checkbox>
                                </template>
                            </el-table-column>
                        </el-table-column>

                        <el-table-column
                                fixed="right"
                                width="80"
                                align="right">
                            <template slot-scope="scope">
                                <el-popconfirm
                                        confirmButtonText='Удалить'
                                        confirmButtonType='danger'
                                        cancelButtonText='Отмена'
                                        icon="el-icon-delete"
                                        iconColor="red"
                                        title="Удалить права?"
                                        @confirm="deleteUser(scope.row.id)"
                                        v-on="{onConfirm:_=>deleteRow(scope.row.id),onCancel:_=>{return false;}}"
                                >
                                    <el-button slot="reference" type="danger" size="small" icon="el-icon-delete"></el-button>
                                </el-popconfirm>
                            </template>
                        </el-table-column>


                    </el-table>


                </el-collapse-item>
            </template>
        </el-collapse>

    </el-main>
</template>

<script>
    export default {
        data() {
            return {
                search: '',
                searchWidth: 120,
                models: [],
                roles: [],
                entities: [],
                collapses: {},
                activeNames: ['1'],
                table: [],
                loading: true,
                edit: null,
                create: false,
                error: '',
                form: {
                    role: '',
                    entity: '',
                    action: '',
                    name: '',
                },
                rules: {
                    role: [
                        { required: true, message: 'Нужно выбрать', trigger: 'change' }
                    ],
                    entity: [
                        { required: true, message: 'Нужно выбрать', trigger: 'change' },
                        { required: true, message: 'Нужно выбрать', trigger: 'blur' }
                    ],
                    action: [
                        { required: true, message: 'Нужно указать', trigger: 'blur' },
                        { pattern: '[0-9a-zA-Z_]+', message: 'Только латинские символы, цифры или _', trigger: 'blur' }
                    ],
                    name: [],
                },
                temp: {}
            }
        },

        methods: {
            checkPivot(rolesList, roleId) {
                let role = rolesList.find(r => r.id === roleId);
                //if(!role) return '?';

                return role && role.pivot ? role.pivot.role_id === roleId : false;
            },

            openCreateForm(){
                this.error = '';
                this.create = true;
            },

            resetForm(clear=true){
                if(this.temp.rules) {
                    this.rules = Object.assign({}, this.temp.rules);
                    this.temp.rules = null;
                }
            },

            submitForm(){
                this.resetForm();
                if(!this.temp.rules) this.temp.rules = Object.assign({}, this.rules);
                this.$refs.ruleForm.validate((valid) => {
                    if (valid) {
                        this.createRow();
                        this.resetForm();
                    } else {
                        //console.log('error submit!!');
                        return false;
                    }
                });
            },

            createRow(){
                this.loading = true;
                this.$http
                    .post('/rpac/permissions/', this.form)
                    .then(r => {
                        //console.warn(r.data);
                        this.form = {
                            role: '',
                            entity: '',
                            action: '',
                            name: '',
                        };
                        this.error = '';

                        return r.data;
                    })
                    .then(d => {
                        //console.warn(d);
                        //this.table.push(d);

                        //let rowInd = this.collapses[d.entity].findIndex(r => r.id === d.id);
                        this.collapses[d.entity].push(d);

                        this.create = false;
                        this.loading = false;
                    })
                    .catch((e) => {
                        let err = e.response.data;
                        console.error(e, err);

                        if(err) {
                            if(err.message) {
                                this.error = err.message;
                                if(err.errors) {
                                    Object.keys(err.errors).forEach(f => {
                                        //if(this.rules[f]) {
                                        this.rules[f] = {
                                            type: 'object',
                                            validator: (rule, value, callback) => {
                                                //console.log(rule, value, callback);
                                                rule.message = err.errors[f].join('. ');
                                                callback(new Error(rule.message));
                                            }
                                        }
                                        this.$refs.ruleForm.validateField(f);
                                        this.rules[f] = {}
                                    });

                                }
                            }
                        }
                    });
            },
            deleteRow(rowId){
                this.loading = true;
                this.$http
                    .post('/rpac/permissions/' + rowId, {_method: 'DELETE'})
                    .then(r => {
                        //console.warn(r.data);
                        return r.data;
                    })
                    .then(d => {
                        //console.warn(d);
                        // let rowInd = this.table.findIndex(r => r.id === rowId);
                        // this.table.splice(rowInd, 1);
                        this.edit = null;
                        this.loading = false;
                    })
                    .catch(e => console.error(e));
            },
            changeRow(row, roleId){
                //console.warn(row, roleId);
                this.loading = true;
                row._method = 'PUT';
                row.role = roleId;

                this.saveRow(row);
            },
            saveRow(row){
                this.$http
                    .post('/rpac/permissions/' + row.id, row)
                    .then(r => {
                        //console.warn(r.data);
                        return r.data;
                    })
                    .then(d => {

                        // console.warn('saveRow then2 before', d);
                        let rowInd = this.collapses[d.entity].findIndex(r => r.id === d.id);
                        this.collapses[d.entity][rowInd] = d;
                        // console.warn('saveRow then2 after', d);

                        this.edit = null;
                        this.loading = false;
                    })
                    .catch(e => console.error(e));
            },
            editRow(val) {
                this.toggleEdit(val.id)
            },
            toggleEdit(rowId) {
                this.edit = this.edit===rowId ? null : rowId;
            },
            getTableData() {

                // this.$http
                //     .get('/rpac/roles')
                //     .then(r => {
                //         console.warn(r);
                //         this.table = r.data;
                //     })
                //     .catch(e => console.error(e));

                this.$http
                    .get('/rpac/permissions')
                    .then(r => {

                        this.entities = Object.keys(r.data);
                        this.collapses = r.data;

                        // s

                        //this.table = Object.values(r.data);

                        console.warn('permissions', r, this.table);
                        // this.table = r.data;
                        // this.loading = false;
                        this.loading = false;
                    })
                    .catch(e => console.error(e));

            },


            getRoles() {
                this.$http
                    .get('/rpac/roles')
                    .then(r => {
                        this.roles = r.data;
                    })
                    .catch(e => console.error(e));
            },

            getModels() {
                this.$http
                    .get('/rpac/access?model&abilities')
                    .then(r => {
                        this.models = r.data;
                    })
                    .catch(e => console.error(e));
            },
        },

        mounted() {
            this.getModels();
            this.getRoles();
            this.getTableData();
        }
    }
</script>

<style scoped lang="scss">
    .el-form-item__label {
        width: 120px;
    }
    .el-dialog__title {
        padding-left: 120px;
    }
    .el-dialog__footer {
        text-align: left;
        padding-left: 140px;
    }
    .el-input__suffix {
        right: 10px;
    }

    .el-table th,
    .el-table td {
        vertical-align: text-top;
    }

    .tar {
        text-align: right;
    }
    .no-wrap {
        white-space: nowrap;
        text-overflow: ellipsis;
        max-width: 100%;
        overflow: hidden;
        display: inline-block;
    }
</style>
