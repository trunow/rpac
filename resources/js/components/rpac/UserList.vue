<template>
    <el-main>
        <el-row>
            <el-col :span="12">
                <h3 style="margin: 0;"><i class="el-icon-user"></i> Пользователи</h3>
            </el-col>
            <el-col :span="12" style="text-align: right;">
                <el-button type="text" @click="create = true" icon="el-icon-plus">Создать</el-button>
            </el-col>
        </el-row>

        <el-dialog title="Новый пользователь" :visible.sync="create">
            <el-form :model="form" label-width="120px">
                <el-form-item label="Имя">
                    <el-input v-model="form.name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="Email">
                    <el-input v-model="form.email" type="email" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="Роль">
                    <el-select v-model="form.roles" multiple value-key="id" placeholder="Роль">
                        <el-option
                                v-for="role in roles"
                                :key="role.id"
                                :label="role.name"
                                :value="role">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="create = false">Отмена</el-button>
                <el-button type="primary" @click="storeUser">Сохранить</el-button>
             </span>
        </el-dialog>

        <div style="max-height: 70vh; overflow: hidden auto;">
            <el-table
                    v-loading="loading"
                    :data="tableData"
                    v-infinite-scroll="loadUsers"
                    infinite-scroll-disabled="disabled"
                    style="min-width: 100%"
            >
                <el-table-column
                        fixed
                        prop="id"
                        label="ID"
                        width="40">
                </el-table-column>

                <el-table-column
                        prop="name"
                        label="Имя"
                        sortable
                        width="200">
                    <template slot-scope="scope">
                        <el-input
                                v-if="edit === scope.row.id"
                                v-model="scope.row.name"
                                :placeholder="scope.column.label"
                        ></el-input>
                        <strong v-else>{{ scope.row.name }}</strong>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="email"
                        label="Email"
                        sortable
                        width="200">
                    <template slot-scope="scope">
                        <el-input
                                v-if="edit === scope.row.id"
                                v-model="scope.row.email"
                                :placeholder="scope.column.label"
                        ></el-input>
                        <strong v-else>{{ scope.row.email }}</strong>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="roles"
                        label="Роли"
                        sortable
                        :filters="roles.length?roles.map(r=>{return {text:r.name,value:r.id}}):[]"
                        :filter-method="filterByRole"
                        width="200">
                    <template slot-scope="scope">
                        <el-select
                                v-if="edit === scope.row.id"
                                v-model="scope.row.roles"
                                multiple
                                off--collapse-tags
                                value-key="id"
                                style="margin-left: 20px;"
                                :placeholder="scope.column.label">
                            <el-option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :label="role.name"
                                    :value="role">
                            </el-option>
                        </el-select>
                        <span v-else>
                            <template v-if="scope.row.roles && scope.row.roles.length">
                                <el-tag size="mini" v-for="(role,ind) in scope.row.roles" :key="ind">{{ role.name }}</el-tag>
                            </template>
                        </span>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="api_token"
                        label="API токен"
                        width="400">
                    <template slot-scope="scope">
                        <el-input
                                v-if="edit === scope.row.id"
                                v-model="scope.row.api_token"
                                :placeholder="scope.column.label"
                        ></el-input>
                        <code v-else>{{ scope.row.api_token }}</code>
                    </template>
                </el-table-column>
                <el-table-column
                        fixed="right"
                        :width="searchWidth"
                        align="right">
                    <template slot-scope="scope">
                        <el-button-group v-if="edit === scope.row.id">
                            <el-button type="primary" size="small" icon="el-icon-check" plain @click="updateUser"></el-button>
                            <el-button type="danger" size="small" icon="el-icon-close" plain @click="edit = false"></el-button>
                        </el-button-group>
                        <el-button-group v-else>
                            <el-button type="primary" size="small" icon="el-icon-edit" @click="edit = scope.row.id" :title="scope.row.id"></el-button>
                            <el-popconfirm
                                    confirmButtonText='Удалить'
                                    confirmButtonType='danger'
                                    cancelButtonText='Отмена'
                                    icon="el-icon-delete"
                                    iconColor="red"
                                    title="Удалить этого пользователя?"
                                    @confirm="deleteUser(scope.row.id)"
                                    v-on="{onConfirm:_=>deleteUser(scope.row.id),onCancel:_=>{return false;}}"
                            >
                                <el-button slot="reference" type="danger" size="small" icon="el-icon-delete"></el-button>
                            </el-popconfirm>
                        </el-button-group>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-main>
</template>

<script>
    export default {
        data() {
            return {
                roles: [],
                search: '',
                searchWidth: 120,
                searchingColumn: null,
                table: [],
                page: 0,
                noMore: false,
                loading: false,
                edit: null,
                create: false,
                form: {},
            }
        },

        computed: {
            disabled () {
                return this.loading || this.noMore
            },
            tableData () {
                let table = this.table.filter(r => !r.deleted_at);
                // TODO if filter.with_trashed ??
                return this.searchingColumn && this.search ? table.filter(r => ~r[this.searchingColumn.property].indexOf(this.search)) : table;
            },
            editableUser () {
                return this.table.find(r => r.id===this.edit);
            }
        },

        methods: {
            filterByRole(value, row, column) {
                const property = column['property'];
                return row[property].find(r => r.id === value);
            },

            getRoles() {
                this.$http
                    .get('/rpac/roles')
                    .then(r => {
                        this.roles = r.data;
                    })
                    .catch(e => console.error(e));
            },

            storeUser() {
                this.$http
                    .post('/rpac/users', Object.assign({'_method': 'POST'}, this.form))
                    .then(r => {
                        this.create = false;
                        this.form = {};
                        this.table.push(r.data);// TODO this.loadUsers();
                    })
                    .catch(e => console.error(e));
            },
            updateUser() {
                this.$http
                    .post('/rpac/users/' + this.edit, Object.assign({'_method': 'PUT'}, this.editableUser))
                    .then(r => {
                        this.edit = null;
                    })
                    .catch(e => console.error(e));
            },
            deleteUser(userId) {
                this.$http
                    .post('/rpac/users/' + userId, Object.assign({'_method': 'DELETE'}, {})) // TODO add request in UserController->destroy
                    .then(r => {
                        this.table.find(r => r.id===userId).deleted_at = r.data; // TODO 2000-00-00 00:00:00
                    })
                    .catch(e => console.error(e));
            },
            loadUsers() {
                this.loading = true;

                this.$http
                    .get('/rpac/users?page=' + (++this.page))
                    .then(r => {
                        return r.data;
                    })
                    .then(d => {
                        // console.log('loadUsers', d);
                        if(d.total && d.to) {
                            if(d.current_page === d.last_page) {
                                this.noMore = true;
                            }

                            if(d.current_page === this.page) {
                                this.table = this.table.concat(d.data);
                            }
                        }
                        else {
                            this.table = d;
                        }

                        this.loading = false;
                    })
                    .catch(e => console.error(e));
            },
        },

        mounted() {
            this.getRoles();
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
    .search-column-wrap {
        width: 80px;
        display: inline-block;
    }
</style>
