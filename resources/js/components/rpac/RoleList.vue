<template>
    <el-main>
        <el-row>
            <el-col :span="12">
                <h3 style="margin: 0;"><i class="el-icon-medal"></i> Роли</h3>
            </el-col>
            <el-col :span="12" style="text-align: right;">
                <el-button type="text" @click="create = true" icon="el-icon-plus">Создать</el-button>
            </el-col>
        </el-row>


        <el-dialog title="Новая роль" :visible.sync="create">
            <el-form :model="form" label-width="120px">
                <el-form-item label="Название">
                    <el-input v-model="form.name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="Код">
                    <el-input v-model="form.slug" autocomplete="off"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="create = false">Отмена</el-button>
                <el-button type="primary" @click="createRow">Сохранить</el-button>
             </span>
        </el-dialog>

        <el-table
                v-loading="loading"
                :data="table"
                style="min-width: 100%">
            <el-table-column
                    fixed
                    prop="id"
                    label="ID"
                    width="40">
            </el-table-column>

            <el-table-column
                    prop="name"
                    label="Название"
                    sortable
                    width="200">
                <template slot-scope="scope">
                    <el-input
                            v-if="edit===scope.row.id"
                            v-model="scope.row.name"
                            :placeholder="scope.column.label"
                            required
                    ></el-input>
                    <strong v-else>{{ scope.row.name }}</strong>
                </template>
            </el-table-column>

            <el-table-column
                    prop="code"
                    label="Код"
                    sortable
                    width="200">
                <template slot-scope="scope">
                    <el-input
                            v-if="edit===scope.row.id"
                            v-model="scope.row.slug"
                            :placeholder="scope.column.label"
                    ></el-input>
                    <span v-else>{{ scope.row.slug }}</span>
                </template>
            </el-table-column>


            <el-table-column
                    fixed="right"
                    :width="searchWidth"
                    align="right">
                <template slot="header" slot-scope="scope">
                    #
                </template>
                <template slot-scope="scope" @click.stop>
                    <el-button-group v-if="edit===scope.row.id">
                        <el-button type="primary" size="small" icon="el-icon-check" plain @click="saveRow(scope.row)"></el-button>
                        <el-button type="danger" size="small" icon="el-icon-close" plain @click="toggleEdit(scope.row.id)"></el-button>
                    </el-button-group>
                    <el-button-group v-else>
                        <el-button type="primary" size="small" icon="el-icon-edit" @click="toggleEdit(scope.row.id)"></el-button>
                        <el-popconfirm
                                confirmButtonText='Удалить'
                                confirmButtonType='danger'
                                cancelButtonText='Отмена'
                                icon="el-icon-delete"
                                iconColor="red"
                                title="Удалить роль?"
                                v-on="{onConfirm:_=>{deleteRow(scope.row.id)},onCancel:_=>{return false;}}"
                        >
                            <el-button slot="reference" type="danger" size="small" icon="el-icon-delete"></el-button>
                        </el-popconfirm>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
    </el-main>
</template>

<script>
    export default {
        data() {
            return {
                search: '',
                searchWidth: 120,
                table: [],
                loading: true,
                edit: null,
                create: false,
                form: {
                    name: '',
                    code: '',
                },
            }
        },

        methods: {

            createRow(){
                this.loading = true;
                this.$http
                    .post('/rpac/roles/', this.form)
                    .then(r => {
                        //console.log(r.data);
                        return r.data;
                    })
                    .then(d => {
                        //console.log(d);
                        this.table.push(d);
                        this.form = {
                            name: '',
                            code: '',
                        };
                        this.create = false;
                        this.loading = false;
                    })
                    .catch(e => console.error(e));
            },
            deleteRow(rowId){
                this.loading = true;
                this.$http
                    .post('/rpac/roles/' + rowId, {_method: 'DELETE'})
                    .then(r => {
                        //console.log(r.data);
                        return r.data;
                    })
                    .then(d => {
                        //console.log(d);
                        let rowInd = this.table.findIndex(r => r.id === rowId);
                        this.edit = null;
                        this.table.splice(rowInd, 1);
                        this.loading = false;
                    })
                    .catch(e => console.error(e));
            },
            saveRow(row){
                this.loading = true;
                row._method = 'PUT';
                this.$http
                    .post('/rpac/roles/' + row.id, row)
                    .then(r => {
                        //console.log(r.data);
                        return r.data;
                    })
                    .then(d => {
                        //console.log(d);
                        let rowInd = this.table.findIndex(r => r.id === d.id);
                        this.table[rowInd] = d;
                        this.edit = null;
                        this.loading = false;
                    })
                    .catch(e => console.error(e));
            },
            editRow(val, e) {
                this.toggleEdit(val.id)
            },
            toggleEdit(rowId) {
                this.edit = this.edit===rowId ? null : rowId;
            },
            getTableData() {
                this.$http
                    .get('/rpac/roles')
                    .then(r => {
                        //console.log('getTableData', r);
                        this.table = r.data;
                        this.loading = false;
                    })
                    .catch(e => console.error(e));
            },
        },

        mounted() {
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
