<template>
    <div class="crud-container">
        <el-container style="height: 100vh;">

            <rpac-menu></rpac-menu>

            <el-container direction="vertical">
                <router-view></router-view>
            </el-container>


        </el-container>
    </div>
</template>

<script>
    import RpacMenu from './RpacMenu';

    export default {
        components: {
            'rpac-menu': RpacMenu,
        },

        mounted() {
        }
    }
</script>

<style lang="scss">
    body {
        margin: 0;
    }
    body * {
        &::-webkit-scrollbar {
            width: 6px;
            height: 6px;
            &-track {
                background-color: rgba(200,200,200,0.3);
            }
            &-thumb {
                background-color: rgba(100,100,100,0.5);
                &:hover {
                    background-color: rgba(100,100,100,0.7);
                }
            }
        }
    }

    .el-header {
        height: 48px;
        font-size: 12px;
        align-items: center;
        display: flex;
        border-bottom: 1px solid #ebeef5;
        .el-dropdown {
            margin-left: auto;
        }
    }

    .el-aside {
        color: #333;
    }

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

    .demo-drawer {
        min-width: 320px;
    }
</style>
