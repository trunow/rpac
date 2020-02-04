<template>
        <el-aside :width="asideWidth" style="max-width: 200px; background-color: rgb(250, 251, 253)">
            <el-menu default-active="0" class="el-menu-vertical" :collapse="collapseMenu">

                <el-menu-item index="0" :class="{ 'tar': !collapseMenu }" @click="collapseMenu=!collapseMenu">
                    <i :class="[ collapseMenu ? 'el-icon-s-unfold' : 'el-icon-s-fold' ]"></i>
                </el-menu-item>

                <el-menu-item index="1">
                    <router-link :to="{name: 'home'}" class="el-menu-link"><i class="el-icon-s-home"></i> <span>Главная</span></router-link>
                </el-menu-item>

                <el-menu-item index="2">
                    <router-link :to="{name: 'users'}" class="el-menu-link"><i class="el-icon-user"></i> <span>Пользователи</span></router-link>
                </el-menu-item>
                <el-menu-item index="3">
                    <router-link :to="{name: 'roles'}" class="el-menu-link"><i class="el-icon-medal"></i> <span>Роли</span></router-link>
                </el-menu-item>
                <el-menu-item index="4">
                    <router-link :to="{name: 'access'}" class="el-menu-link"><i class="el-icon-guide"></i> <span>Права</span></router-link>
                </el-menu-item>

            </el-menu>
        </el-aside>
</template>

<script>
    export default {
        name: 'rpac-menu',
        data() {
            return {
                collapseMenu: true,
            }
        },
        computed: {
            asideWidth() {
                return this.collapseMenu ? '65px' : '200px'
            }
        }
    }
</script>

<style scoped lang="scss">
    .el-menu-vertical {
        height: 100vh;
    }
    .el-aside {
        transition: all .5s;
    }
    .el-menu.v-leave-active {
        .el-submenu .el-menu i {
            margin-left: -20px;
            margin-right: 200px;
        }
    }
    .el-menu-item .el-menu-link {
        color: inherit;
        text-decoration: none;
    }
    .el-menu--popup .el-menu-item .el-menu-link,
    .el-submenu .el-menu-item .el-menu-link {
        width: 100%;
        display: inline-block;
        margin: 0 -45px;
        padding: 0 45px;
    }
</style>
