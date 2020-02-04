<template>
    <el-row style="margin-top: 80px;">
        <el-col :span="20" :offset="2">
            <el-row :gutter="40" style="margin-top: 80px;">
                <el-col :span="8">
                    <el-card :body-style="{ padding: '40px 20px', textAlign: 'center' }">
                        <el-avatar icon="el-icon-user" size="large"></el-avatar>
                        <div style="padding: 20px;">
                            <router-link :to="{name: 'users'}">Пользователи</router-link>
                        </div>
                    </el-card>
                </el-col>
                <el-col :span="8">
                    <el-card :body-style="{ padding: '40px 20px', textAlign: 'center' }">
                        <el-avatar icon="el-icon-medal" size="large"></el-avatar>
                        <div style="padding: 20px;">
                            <router-link :to="{name: 'roles'}">Роли</router-link>
                        </div>
                    </el-card>
                </el-col>
                <el-col :span="8">
                    <el-card :body-style="{ padding: '40px 20px', textAlign: 'center' }">
                        <el-avatar icon="el-icon-guide" size="large"></el-avatar>
                        <div style="padding: 20px;">
                            <router-link :to="{name: 'access'}">Права</router-link>
                        </div>
                    </el-card>
                </el-col>
            </el-row>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        name: 'rpac-dashboard',
    }
</script>
